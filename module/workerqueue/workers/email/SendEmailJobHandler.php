<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 19.10.2016
 * Time: 10:49
 */

namespace DynCom\dc\workerqueue\workers\email;


use DynCom\dc\workerqueue\main\exceptions\InvalidJobPayloadErrorException;
use DynCom\dc\workerqueue\main\Job;
use DynCom\dc\workerqueue\main\JobHandler;
use DynCom\dc\workerqueue\main\jobHandlerTrait;
use Swift_Mailer;
use Swift_Message;


/**
 * Class SendEmailJobHandler
 * @package DynCom\dc\workerqueue\email
 */
class SendEmailJobHandler implements JobHandler
{
    use jobHandlerTrait;

    protected const KEY_SENDER_NAME = 'sender_name';
    protected const KEY_SENDER_MAIL = 'sender_mail';
    protected const KEY_RECIPIENTS = 'recipients';
    protected const KEY_RECIPIENT_NAME = 'recipient_name';
    protected const KEY_RECIPIENT_MAIL = 'recipient_mail';
    protected const KEY_MAIL_SUBJECT = 'mail_subject';
    protected const KEY_CCS = 'ccs';
    protected const KEY_CC_NAME = 'cc_name';
    protected const KEY_CC_MAIL = 'cc_mail';
    protected const KEY_BCCS = 'bccs';
    protected const KEY_BCC_NAME = 'bcc_name';
    protected const KEY_BCC_MAIL = 'bcc_mail';
    protected const KEY_ATTACHMENTS = 'attachments';
    protected const KEY_ATTACHMENT_PATH = 'attachment_path';
    protected const KEY_MAIL_BODY = 'mail_body';
    protected const KEY_PARTS = 'mail_parts';
    protected const KEY_BODY_CONTENT_TYPE = 'mail_body_content_type';
    protected const KEY_PART_CONTENT_TYPE = 'mail_part_content_type';
    protected const KEY_PART_CONTENT = 'mail_part_content';

    public const QUEUE_NAME = 'sendemail';

    protected $mailer;
    /**
     * @var SendEmailJobPayload
     */
    protected $payloadObject;

    /**
     * SendEmailJobHandler constructor.
     * @param Swift_Mailer $mailer
     */
    public function __construct(Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param Job $job
     */
    public function doJob(Job $job): void
    {
        $failed = false;
        $logger = $this->getLogger();
        $dbgMsg = 'Method [' . __METHOD__ . '] in Class [' . __CLASS__ . '] called with Job [' . json_encode($job) . '].';
        $logger->debug($dbgMsg);

        $exception = null;
        try {
            $this->handlePayload($job);
            $logger->info('Payload handled successfully.');
        } catch (InvalidJobPayloadErrorException $e) {
            $job->setStatusFailed($e->getCode(), json_encode($e));
            $errMsg = $e->getMessage();
            $logger->alert('InvalidJobPayloadErrorException - [' . $errMsg . ']. Job-Status has been set to failed.');
            return;
        }

        $logger->info('Getting Swift_Mail from Payload.');
        try {
            $mail = $this->getMailFromPayload();
        } catch (\Exception $e) {
            $logger->error($e->getMessage());
            $failed = true;
        }

        $noOfDeliveredMails = 0;
        $failedRecipients = count($this->payloadObject->getRecipients());
        if (!$failed) {
            $to = $mail->getTo();
            $cc = $mail->getCc();
            $bcc = $mail->getBcc();
            $logger->info('Sending mail to recipients [' . serialize($to) . '], cc [' . serialize($cc) . '], bcc [' . serialize($bcc) . '].');

            $failedRecipients = [];

            $logger->info('Attempting to send mails.');
            $errorMsg = '';
            try {
                $noOfDeliveredMails = $this->mailer->send($mail, $failedRecipients);
            } catch (\Exception $e) {
                $errorMsg = $e->getMessage();
            }


            $logger->info('No of delivered mails: [' . $noOfDeliveredMails . ']. Message [' . $errorMsg . ']');
        }
        if ($failed || !$noOfDeliveredMails) {
            $exception = new SendEmailErrorException($this->payloadObject->getSenderMail(), $this->payloadObject->getSubject(), implode(',', $this->payloadObject->getRecipients()), $failedRecipients);
            $job->setStatusFailed($exception->getCode(), json_encode($exception));
        } else {
            $job->setStatusFinished();
        }
        return;
    }

    /**
     * @param Job $job
     * @throws InvalidJobPayloadErrorException
     */
    protected function handlePayload(Job $job): void
    {
        $payload = $job->getPayload();
        $errorDetails = null;
        if (!$this->checkPayloadForValidRecipientEMailAddress($payload)) {
            $errorDetails[] = 'Could not extract at least one valid recipient email-address from job-payload.';
        }
        if (!$this->checkPayloadForValidSenderEMailAddress($payload)) {
            $errorDetails[] = 'Could not extract a valid sender email-address from job-payload.';
        }
        if (!$this->checkPayloadForNonEmptyMailBody($payload)) {
            $errorDetails[] = 'Could not extract a nonempty email-body from job-payload.';
        }
        if (!$this->checkPayloadForNonEmptyBodyContentType($payload)) {
            $errorDetails[] = 'Could not extract a nonempty content-type for the body.';
        }

        if (!$this->checkPayloadForNonEmptyMailSubject($payload)) {
            $errorDetails[] = 'Could not extract a nonempty email-subjet from job-payload.';
        }
        if ($errorDetails !== null) {
            $errorDetailString = implode(' ', $errorDetails);
            $exception = new InvalidJobPayloadErrorException(
                $job->getID(),
                $job->getQueueName(),
                $payload,
                $errorDetailString
            );
            throw $exception;
        }
        $this->extractedValidatedPayloadData = $payload;
        return;
    }

    /**
     * @param array $payload
     * @return bool
     */
    protected function checkPayloadForValidRecipientEMailAddress(array $payload): bool
    {
        return (
            array_key_exists(static::KEY_RECIPIENTS, $payload)
            && is_array($payload[static::KEY_RECIPIENTS])
            && count($payload[static::KEY_RECIPIENTS]) > 0
            && is_array($payload[static::KEY_RECIPIENTS][0])
            && array_key_exists(static::KEY_RECIPIENT_MAIL, $payload[static::KEY_RECIPIENTS][0])
            && filter_var($payload[static::KEY_RECIPIENTS][0][static::KEY_RECIPIENT_MAIL], FILTER_VALIDATE_EMAIL)
        );
    }

    /**
     * @param array $payload
     * @return bool
     */
    protected function checkPayloadForValidSenderEMailAddress(array $payload): bool
    {
        return (
            array_key_exists(static::KEY_SENDER_MAIL, $payload)
            && filter_var($payload[static::KEY_SENDER_MAIL], FILTER_VALIDATE_EMAIL)
        );
    }

    /**
     * @param array $payload
     * @return bool
     */
    protected function checkPayloadForNonEmptyMailBody(array $payload): bool
    {
        return (
            array_key_exists(static::KEY_MAIL_BODY, $payload)
            && is_string($payload[static::KEY_MAIL_BODY])
            && mb_strlen($payload[static::KEY_MAIL_BODY]) > 0
        );
    }

    /**
     * @param array $payload
     * @return bool
     */
    protected function checkPayloadForNonEmptyBodyContentType(array $payload): bool
    {
        return (
            array_key_exists(static::KEY_BODY_CONTENT_TYPE, $payload)
            && is_string($payload[static::KEY_BODY_CONTENT_TYPE])
            && mb_strlen($payload[static::KEY_BODY_CONTENT_TYPE]) > 0
        );
    }

    /**
     * @param array $payload
     * @return bool
     */
    protected function checkPayloadForNonEmptyMailSubject(array $payload): bool
    {
        return (
            array_key_exists(static::KEY_MAIL_SUBJECT, $payload)
            && is_string($payload[static::KEY_MAIL_SUBJECT])
            && mb_strlen($payload[static::KEY_MAIL_SUBJECT]) > 0
        );
    }

    /**
     * @return Swift_Message
     */
    protected function getMailFromPayload(): Swift_Message
    {
        $mail = Swift_Message::newInstance();
        $payloadData = $this->extractedValidatedPayloadData;
        $payloadObj = SendEmailJobPayload::fromJobPayloadArray($payloadData);
        $this->payloadObject = $payloadObj;
        $payloadObj->fillSwiftMessage($mail);
        return $mail;
    }

    /**
     * @return string
     */
    public function getJobQueueName(): string
    {
        return self::QUEUE_NAME;
    }
}