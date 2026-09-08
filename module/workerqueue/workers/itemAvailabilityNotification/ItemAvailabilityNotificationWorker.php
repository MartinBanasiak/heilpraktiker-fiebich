<?php
declare(strict_types = 1);
namespace DynCom\dc\workerqueue\workers;

use DynCom\dc\dcShop\classes\ShopLanguage;
use DynCom\dc\dcShop\classes\ShopLanguageRepository;
use DynCom\dc\dcShop\classes\TextModule;
use DynCom\dc\dcShop\classes\TextModuleRepository;
use DynCom\dc\dcShop\classes\WebshopItemFile;
use DynCom\dc\dcShop\classes\WebshopItemService;
use DynCom\dc\workerqueue\main\exceptions\JobProcessingInvalidBackingServiceResponseErrorException;
use DynCom\dc\workerqueue\main\GenericJob;
use DynCom\dc\workerqueue\main\Job;
use DynCom\dc\workerqueue\main\JobQueueGateway;
use DynCom\dc\workerqueue\workers\email\SendEmailJobPayload;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Whoops\Exception\ErrorException;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 16.10.2016
 * Time: 16:19
 */
class ItemAvailabilityNotificationWorker
{

    protected const NOTIFICATION_MAIL_SENDER_NAME = 'Cronus Shop Customer Service';
    protected const NOTIFICATION_MAIL_SENDER_EMAIL = 'customerservice@cronusshop.de';
    protected const NOTIFICATION_MAIL_BCC_NAME = 'Notification group outgoing customer mails';
    protected const NOTIFICATION_MAIL_BCC_MAIL = 'outgoingcustomermails@cronusshop.de';

    protected const QUERY_ALL_ACTIVE_NOTIFICATIONS =
        '
        SELECT
          id,
          company,
          site_code,
          site_language_code,
          target_shop_code,
          item_shop_code,
          customer_shop_code,
          shop_language_code,
          item_no,
          variant_code,
          customer_no,
          user_name,
          user_email         
        FROM
          active_item_availability_notification
        ORDER BY 
          id ASC
        ';
    protected const QUERY_DELETE_SINGLE_ACTIVE_NOTIFICATION =
        '
        DELETE FROM
          active_item_availability_notification
        WHERE
          id = :notification_id
        ';
    public const WORER_QUEUE_NAME = 'checkitemavailability';

    /**
     * @var \PDO
     */
    private $db;

    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var JobQueueGateway
     */
    private $jobQueueGateway;
    /**
     * @var TextModuleRepository
     */
    private $textModuleRepository;
    /**
     * @var ShopLanguageRepository
     */
    private $shopLanguageRepository;

    private $textModulesByShopLanguagePrimary = [];
    /**
     * @var WebshopItemService
     */
    private $itemService;

    /**
     * ItemAvailabilityNotificationWorker constructor.
     * @param WebshopItemService $itemService
     * @param ShopLanguageRepository $shopLanguageRepository
     * @param JobQueueGateway $jobQueueGateway
     * @param TextModuleRepository $textModuleRepository
     * @param \PDO $db
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        WebshopItemService $itemService,
        ShopLanguageRepository $shopLanguageRepository,
        JobQueueGateway $jobQueueGateway,
        TextModuleRepository $textModuleRepository,
        \PDO $db,
        LoggerInterface $logger = null
    )
    {
        $this->db = $db;
        if (null === $logger) {
            $this->logger = new NullLogger();
        } else {
            $this->logger = $logger;
        }
        $this->jobQueueGateway = $jobQueueGateway;
        $this->textModuleRepository = $textModuleRepository;
        $this->shopLanguageRepository = $shopLanguageRepository;
        $this->itemService = $itemService;
    }

    public function doWork(Job $job): void
    {
        try {
            $stmt = $this->db->query(self::QUERY_ALL_ACTIVE_NOTIFICATIONS);

            $noOfRows = $stmt->rowCount();
        } catch (\Throwable $t) {
            $jobID = md5(random_bytes(16));
            $msg = $t->getMessage();
            $this->logger->alert($msg);
            $err = new JobProcessingInvalidBackingServiceResponseErrorException($jobID,self::WORER_QUEUE_NAME,'shop-mysql',$msg,[],'',$t);
            throw $err;
        }
        $this->logger->info(
            '{noOfNotificationRequests} active requests for item availability notifications have been found.',
            ['noOfNotificationRequests' => $noOfRows]
        );

        $jobsToCreate = [];
        $notificationRowIDsToDelete = [];


        foreach ($stmt as $row) {
            try {
                $this->logger->info('Checking item availability for row: {row}', ['row' => $row]);

                $itemIsOrderable = $this->itemService->isItemOrderableByInventory(
                    $row['company'],
                    $row['item_shop_code'],
                    $row['shop_language_code'],
                    $row['item_no'],
                    $row['variant_code']
                );

                if ($itemIsOrderable) {

                    $this->logger->info('Item is available - constructing data for mail');

                    $itemLink = $this->itemService->getItemURL(
                        $row['site_code'], $row['site_language_code'], $row['item_no'], $row['variant_code']
                    );

                    /**
                     * @var $itemMainImageFile WebshopItemFile
                     */
                    $itemMainImageFile = $this->itemService->getItemImages(
                        $row['company'],
                        $row['item_shop_code'],
                        $row['shop_language_code'],
                        $row['item_no'],
                        $row['variant_code']
                    )->getFirst();
                    $this->logger->info('First image for file is [' . serialize($itemMainImageFile) . '].');
                    $mainImageFilename = $itemMainImageFile->filename;
                    $mainImageDescription = $itemMainImageFile->description;

                    $custPrice = $this->itemService->getItemCustomerPrice($row['company'], $row['target_shop_code'], $row['item_shop_code'], $row['customer_shop_code'], $row['shop_language_code'], $row['customer_no'], $row['item_no'], $row['variant_code'], 1);

                    $priceValue = $custPrice->getFormattedCustomerPrice();
                    $crossPriceValue = $custPrice->getCrossPrice();
                    if (!$crossPriceValue) {
                        $crossPriceValue = '';
                    } else {
                        $crossPriceValue = $custPrice->getFormattedCrossPrice();
                    }

                    $jobsToCreate[] = [
                        'notificationID' => $row['id'],
                        'company' => $row['company'],
                        'site_code' => $row['site_code'],
                        'site_language_code' => $row['site_language_code'],
                        'target_shop_code' => $row['target_shop_code'],
                        'item_shop_code' => $row['item_shop_code'],
                        'shop_language_code' => $row['shop_language_code'],
                        'item_no' => $row['item_no'],
                        'variant_code' => $row['variant_code'],
                        'user_name' => $row['user_name'],
                        'user_email' => $row['user_email'],
                        'item_link' => $itemLink,
                        'main_image_filename' => $mainImageFilename,
                        'main_image_description' => $mainImageDescription,
                        'customer_price' => $priceValue,
                        'cross_price' => $crossPriceValue,
                    ];
                } else {
                    $this->logger->info('Item [' . print_r($row,true) . '] not available.');
                }
            } catch (\InvalidArgumentException $e) {
                $this->logger->info('Item does not exist - marking for delete');
                //Item doesn't exist - mark for deletion
                $notificationRowIDsToDelete[] = $row['id'];
            }
        }
        $stmt->closeCursor();
        unset($stmt, $row);


        $noOfInvalidRequests = count($notificationRowIDsToDelete);
        $logMsgNoOfInvalidRequests = '{noOfInvalidRequests} item availability notification requests with invalid item key have been marked for deletion.';
        $logContextNoOfInvalidRequests = ['noOfInvalidRequests' => $noOfInvalidRequests];
        if ($noOfInvalidRequests > 0) {
            $this->logger->notice($logMsgNoOfInvalidRequests, $logContextNoOfInvalidRequests);
        } else {
            $this->logger->info($logMsgNoOfInvalidRequests, $logContextNoOfInvalidRequests);
        }


        $noOfJobsToEnqueue = count($jobsToCreate);
        $this->logger->info(
            '{noOfJobsToEnqueue} sendemail-jobs to be enqueued in the general_job_queue.',
            ['noOfJobsToEnqueue' => $noOfJobsToEnqueue]
        );

        $noOfJobsEnqueued = 0;
        $jobsNotSuccessfullyEnqueued = [];
        foreach ($jobsToCreate as $job) {
            $item = $this->itemService->getItem(
                $job['company'],
                $job['item_shop_code'],
                $job['shop_language_code'],
                $job['item_no'],
                $job['variant_code']
            );
            $itemName = $item->getDescription();
            $mainImageFileName = $job['main_image_filename'];
            $mainImageDescription = $job['main_image_description'];
            $link = $job['item_link'];
            try {
                if ($this->createSendEmailJobForNotification(
                    $job['company'],
                    $job['target_shop_code'],
                    $job['shop_language_code'],
                    $job['item_no'],
                    $job['variant_code'],
                    $itemName,
                    $mainImageFileName,
                    $mainImageDescription,
                    $link,
                    $job['user_name'],
                    $job['user_email'],
                    (string)$job['customer_price'],
                    (string)$job['cross_price']
                )
                ) {
                    $noOfJobsEnqueued++;
                } else {
                    $jobsNotSuccessfullyEnqueued[] = $job;
                }
            } catch (\Throwable $throwable) {
                $msg = $throwable->getMessage();
                $this->logger->alert($msg);
                throw new ErrorException($msg);
            }
            $notificationRowIDsToDelete[] = $job['notificationID'];
        }

        $this->logger->info(
            '{noOfJobsEnqueued} sendemail-jobs have been successfully enqueued.',
            ['noOfJobsEnqueued' => $noOfJobsEnqueued]
        );

        $noOfJobsNotEnqueued = count($jobsNotSuccessfullyEnqueued);
        $logMsgJobsNotEnqueued = '{noOfJobsNotEnqueued} sendemail-jobs could not be successfully enqueued and have been marked for deletion.';
        $logContextJobsNotEnqueued = ['noOfJobsNotEnqueued' => $noOfJobsNotEnqueued];
        if ($noOfJobsNotEnqueued > 0) {
            $this->logger->alert($logMsgJobsNotEnqueued, $logContextJobsNotEnqueued);
            foreach ($jobsNotSuccessfullyEnqueued as $jobNotEnqueued) {
                $this->logger->info(
                    'The job with the following data could not be enqueued: [{jobData}].',
                    ['jobData' => print_r($jobNotEnqueued, true)]
                );
            }
        } else {
            $this->logger->info($logMsgJobsNotEnqueued, $logContextJobsNotEnqueued);
        }

        $noOfRowsToDelete = count($notificationRowIDsToDelete);
        $noOfDeletedActiveNotificationRequeusts = 0;
        foreach ($notificationRowIDsToDelete as $rowID) {
            $stmt = $this->db->prepare(self::QUERY_DELETE_SINGLE_ACTIVE_NOTIFICATION);
            $stmt->bindValue(':notification_id', $rowID);
            $noOfDeletedActiveNotificationRequeusts += $stmt->execute() ? 1 : 0;
        }
        unset($stmt);


        $logMsgDeletedNotificationRows = '{noOfDeletedRows} of {noOfRowsToDelete} to-delete entries were successfully deleted from active_item_availability_notification.';
        $logContextDeletedNotificationRows = ['noOfDeletedRows' => $noOfDeletedActiveNotificationRequeusts, 'noOfRowsToDelete' => $noOfRowsToDelete];
        if ($noOfDeletedActiveNotificationRequeusts !== $noOfRowsToDelete) {
            $this->logger->alert($logMsgDeletedNotificationRows, $logContextDeletedNotificationRows);
        } else {
            $this->logger->info($logMsgDeletedNotificationRows, $logContextDeletedNotificationRows);
        }
        $job->setStatusFinished();
    }

    /**
     * @param string $company
     * @param string $targetShopCode
     * @param string $languageCode
     * @param string $itemNo
     * @param string $variantCode
     * @param string $itemName
     * @param string $mainImageFilename
     * @param string $mainImageDescription
     * @param string $itemLink
     * @param string $userName
     * @param string $userEmail
     * @param string $customerPrice
     * @param string $crossPrice
     * @return bool
     */
    protected function createSendEmailJobForNotification(
        string $company,
        string $targetShopCode,
        string $languageCode,
        string $itemNo,
        string $variantCode,
        string $itemName,
        string $mainImageFilename,
        string $mainImageDescription,
        string $itemLink,
        string $userName,
        string $userEmail,
        string $customerPrice,
        string $crossPrice
    ): bool
    {
        /**
         * @var $textModule TextModule
         */
        $textModule = $this->getTextModuleByShopLanguagePrimary($company, $targetShopCode, $languageCode);
        $subjectPlaceholders = [];

        //@TODO: Image Paths from Config
        $mainImagePath = dirname(dirname(dirname(__DIR__))) . '/userdata/dcshop/images/thumb_1/' . $mainImageFilename;
        $bodyPlaceholders = [
            'item_link' => $itemLink,
            'item_no' => $itemNo,
            'item_variant_code' => $variantCode,
            'item_name' => $itemName,
            'image_src' => $mainImagePath,
            'image_desc' => $mainImageDescription,
            'customer_price' => $customerPrice,
            'cross_price' => $crossPrice
        ];

        $subject = $textModule->getDescriptionWithReplacedPlaceholders($subjectPlaceholders) ?? 'Item available';
        $body = $textModule->getContentWithReplacedPlaceholders($bodyPlaceholders) ?? 'Item ' . $itemNo . ' has become available.';
        $isHtml = ($body !== strip_tags($body));
        $contentType = $isHtml ? 'text/html' : 'text/plain';

        $payloadObj = new SendEmailJobPayload(
            self::NOTIFICATION_MAIL_SENDER_EMAIL,
            $userEmail,
            $subject,
            $body,
            $contentType
        );
        $payloadObj->setPrimaryRecipientName($userName);
        $payloadObj->addBcc(self::NOTIFICATION_MAIL_BCC_NAME, self::NOTIFICATION_MAIL_BCC_MAIL);
        $payloadObj->setSenderName(self::NOTIFICATION_MAIL_SENDER_NAME);

        $rootDir = dirname(dirname(dirname(__DIR__)));

        if (!empty($textModule->attachment_1)) {
            $path = $rootDir . '/userdata/private/attachments/' . ltrim($textModule->attachment_1, '/');
            if (file_exists($path) && is_readable($path) && is_file($path)) {
                $payloadObj->addAttachment($path);
            }
        }
        if (!empty($textModule->attachment_2)) {
            $path = $rootDir . '/userdata/private/attachments/' . ltrim($textModule->attachment_2, '/');
            if (file_exists($path) && is_readable($path) && is_file($path)) {
                $payloadObj->addAttachment($path);
            }
        }

        $errors = [];
        if ($payloadObj->isMinimallyValid($errors)) {
            $this->logger->info('Payload for availability notification is minimally valid -> attempting to enqueue job.');
            $job = new GenericJob(self::WORER_QUEUE_NAME, microtime(true), 0, 3, 0, $payloadObj->getAsPayloadArray());
            try {
                $this->jobQueueGateway->createJob($job);
                $this->logger->info('sendemail-Job enqueued.');
            } catch (\Exception $e) {
                $errMsg = $e->getMessage();
                $this->logger->alert('sendemail-Job could not be successfully enqueued. ErrorMessage [errMsg]', ['errMsg' => $errMsg]);
                return false;
            }
            return true;
        } else {
            $this->logger->alert('Payload for availability notification is not minimally valid! Reasons: [' . print_r($errors, true) . '].');
        }
        return false;
    }

    /**
     * @param string $company
     * @param string $shopCode
     * @param string $languageCode
     * @return TextModule
     */
    protected function getTextModuleByShopLanguagePrimary(string $company, string $shopCode, string $languageCode): TextModule
    {
        $key = $company . '|' . $shopCode . '|' . $languageCode;
        if (array_key_exists(
                $key,
                $this->textModulesByShopLanguagePrimary
            ) && $this->textModulesByShopLanguagePrimary[$key] instanceof TextModule
        ) {
            return $this->textModulesByShopLanguagePrimary[$key];
        } else {
            //$testTextModule = new TextModule(new TextModuleConfig());
            //$testTextModuleContent = file_get_contents('availability_notification_mail_template.test.html');
            //$testTestModuleArr = ['id' => 9999, 'company' => 'CRONUS AG', 'code' => 'email_item_avail_notify_de','description' => 'Artikel wieder verfügbar','content' => $testTextModuleContent,'to_delete' => 0];
            //$testTextModule->mapFromArray($testTestModuleArr);
            //return $testTextModule;
            $shopLanguagePrimary = [
                'company' => $company,
                'shop_code' => $shopCode,
                'code' => $languageCode,
            ];
            /**
             * @var $shopLanguage ShopLanguage
             */
            //$this->logger->info('Looking for text module by company [' . $company . '] and shop_code [' . $shopCode. '] and code [' . $languageCode . '].');
            $shopLanguage = $this->shopLanguageRepository->findByAltPrimary($shopLanguagePrimary);

            $textModuleCode = $shopLanguage->email_availability_notify;
            //$this->logger->info('Looking for text module by company [' . $company . '] and code [' . $textModuleCode . '].');
            $textModule = $this->textModuleRepository->findByAltPrimary(
                ['company' => $company, 'code' => $textModuleCode]
            );
            $this->textModulesByShopLanguagePrimary[$key] = $textModule;
            return $textModule;
        }

    }


}