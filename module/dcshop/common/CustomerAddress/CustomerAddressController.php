<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 01.12.2017
 * Time: 07:02
 */

namespace DynCom\dc\dcShop\common\CustomerAddress;


use DynCom\dc\common\classes\NewValidator;
use DynCom\dc\dcShop\CustomerAddress\CustomerAddress;
use DynCom\dc\dcShop\CustomerAddress\CustomerAddressConfig;
use DynCom\dc\dcShop\CustomerAddress\CustomerAddressRepository;
use DynCom\dc\regionalization\RegionalizedTextProvider;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class CustomerAddressController
{
    public const URL_PATH = 'customer-address';
    public const URL_PATH_ACTION_SHOW_ADDRESS       = 'show-address';
    public const URL_PATH_ACTION_SHOW_ADDRESSES     = 'show-addresses';
    public const URL_PATH_ACTION_EDIT_ADDRESS       = 'edit-address';
    public const URL_PATH_ACTION_CREATE_ADDRESS     = 'create-address';
    public const URL_PATH_ACTION_SHOW_EDIT_ADDRESS  = 'show-edit-address';
    public const URL_PATH_ACTION_NEW_ADDRESS        = 'new-address';

    protected const FIELD_NAME_ID           = 'id';
    protected const FIELD_NAME_COMPANY      = 'company';
    protected const FIELD_NAME_CUSTOMER_NO  = 'customer_no';
    protected const FIELD_NAME_NAME         = 'name';
    protected const FIELD_NAME_NAME_2       = 'name_2';
    protected const FIELD_NAME_ADDRESS      = 'address';
    protected const FIELD_NAME_ADDRESS_2    = 'address_2';
    protected const FIELD_NAME_POST_CODE    = 'post_code';
    protected const FIELD_NAME_CITY         = 'city';
    protected const FIELD_NAME_COUNTRY      = 'country';
    protected const FIELD_NAME_CODE         = 'code';

    protected const TEMPLATE_NAME_CUSTOMER_ADDRESS_FORM = 'customer_address_form';
    protected const TEMPLATE_NAME_CUSTOMER_ADDRESS_LIST = 'customer_address_list';
    protected const TEMPLATE_NAME_CUSTOMER_ADDRESS_VIEW = 'customer_address_view';

    protected const URL_ACTION_METHOD_MAPPINGS = [
        self::URL_PATH_ACTION_SHOW_ADDRESS => 'showAddress',
        self::URL_PATH_ACTION_SHOW_ADDRESSES => 'showAddresses',
        self::URL_PATH_ACTION_EDIT_ADDRESS => 'editAddress',
        self::URL_PATH_ACTION_CREATE_ADDRESS => 'createAddress',
        self::URL_PATH_ACTION_SHOW_EDIT_ADDRESS => 'showEditAddress',
        self::URL_PATH_ACTION_NEW_ADDRESS => 'newAddress',
    ];

    protected const ERR_ACTION_REDIRECTS = [
        __CLASS__ . '::handleRequest' => '/',
        __CLASS__ . '::' . self::URL_PATH_ACTION_SHOW_ADDRESSES => '/',
        __CLASS__ . '::' . self::URL_PATH_ACTION_SHOW_ADDRESS => __CLASS__ . '::' . self::URL_PATH_ACTION_SHOW_ADDRESSES,
        __CLASS__ . '::' . self::URL_PATH_ACTION_EDIT_ADDRESS => __CLASS__ . '::' . self::URL_PATH_ACTION_SHOW_ADDRESSES,
        __CLASS__ . '::' . self::URL_PATH_ACTION_CREATE_ADDRESS => __CLASS__ . '::' . self::URL_PATH_ACTION_SHOW_ADDRESSES,
        __CLASS__ . '::' . self::URL_PATH_ACTION_SHOW_EDIT_ADDRESS => __CLASS__ . '::' . self::URL_PATH_ACTION_SHOW_ADDRESSES,
        __CLASS__ . '::' . self::URL_PATH_ACTION_SHOW_EDIT_ADDRESS => __CLASS__ . '::' . self::URL_PATH_ACTION_SHOW_ADDRESSES,
        __CLASS__ . '::' . self::URL_PATH_ACTION_NEW_ADDRESS => __CLASS__ . '::' . self::URL_PATH_ACTION_SHOW_ADDRESSES,
    ];

    protected $customerAddressRepo;
    protected $templateDirectory;
    protected $mustacheEngine;
    protected $textProvider;
    protected $logger;


    public function __construct(CustomerAddressRepository $customerAddressRepository, RegionalizedTextProvider $textProvider, LoggerInterface $logger = null)
    {
        $this->customerAddressRepo = $customerAddressRepository;
        $this->templateDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'templates';
        $mustacheOptions = [
            'loader' => new \Mustache_Loader_FilesystemLoader($this->templateDirectory),
        ];
        $this->mustacheEngine = new \Mustache_Engine($mustacheOptions);
        $this->textProvider = $textProvider;
        $logger = $logger ?? new NullLogger();
        $this->logger = $logger;
    }

    /**
     * @param ServerRequestInterface $request
     * @param string $company
     * @param string $customerNo
     * @return string
     * @throws \ErrorException
     */
    public function handleRequest(ServerRequestInterface $request, string $company, string $customerNo): string
    {
        $method = $request->getMethod();
        $target = $request->getRequestTarget();
        $queryParams = $request->getQueryParams();
        $parsedBody = $request->getParsedBody();

        $userNotifications = [];
        $output = '';

        try {
            $id = null;
            if (array_key_exists(self::FIELD_NAME_ID, $queryParams)) {
                $id = filter_var($queryParams[self::FIELD_NAME_ID], FILTER_SANITIZE_NUMBER_INT);
            }
            if ($id > 0 && strpos($target, '/' . self::URL_PATH_ACTION_SHOW_ADDRESS . '/') !== false) {
                $output = $this->showAddress($request, $company, $customerNo);
            } elseif ($company && $customerNo && strpos($target, '/' . self::URL_PATH_ACTION_SHOW_ADDRESSES . '/') !== false) {
                $output = $this->showAddresses($request, $company, $customerNo);
            } elseif ($id > 0 && strpos($target, '/' . self::URL_PATH_ACTION_EDIT_ADDRESS . '/')) {
                $output = $this->editAddress($request, $company, $customerNo);
            } elseif ($company && $customerNo && strpos($target, '/' . self::URL_PATH_ACTION_CREATE_ADDRESS . '/') !== false) {
                $output = $this->createAddress($request, $company, $customerNo);
            } elseif ($company && $customerNo && strpos($target, '/' . self::URL_PATH_ACTION_SHOW_EDIT_ADDRESS . '/') !== false) {
                $output = $this->showEditAddress($request, $company, $customerNo);
            } elseif (strpos($target, '/' . self::URL_PATH_ACTION_NEW_ADDRESS . '/')) {
                $output = $this->newAddress($company, $customerNo);
            }
        } catch (\Throwable $t) {

        }
        $errMsgCode = 'error_cust_addr_invalid_action';
        $this->errMsgs[] = $errMsgCode;
        return $this->handleError(__METHOD__);
    }

    /**
     * @param string $company
     * @param string $customerNo
     * @return string
     */
    public function newAddress(string $company, string $customerNo): string
    {
        $arr = [
            self::FIELD_NAME_COMPANY => $company,
            self::FIELD_NAME_CUSTOMER_NO => $customerNo,
        ];
        $address = new CustomerAddress(new CustomerAddressConfig());
        $address->mapFromArray($arr);
        return $this->getCustomerAddressForm($address);
    }

    /**
     * @param ServerRequestInterface $request
     * @param string $company
     * @param string $customerNo
     * @return string
     */
    public function showEditAddress(ServerRequestInterface $request, string $company, string $customerNo): string
    {
        $queryParams = $request->getQueryParams();
        $bodyParams = $request->getParsedBody();
        $id = $bodyParams[self::FIELD_NAME_ID] ?? $queryParams[self::FIELD_NAME_ID] ?? null;
        if (!((int)$id > 0)) {
            $errMsgCode = 'error_edit_cust_addr_no_id';
            $this->errMsgs[] = $errMsgCode;
            return $this->handleError(__METHOD__);
        }
        /**
         * @var $address CustomerAddress
         */
        $address = $this->customerAddressRepo->findByID($id);
        $viewModel = new CustomerAddressViewModel($this->textProvider);
        $viewModel->customerAddress = $address;
        $string = '';
        if ($company === $address->company && $customerNo === $address->customer_no) {
            $string = $this->mustacheEngine->render(self::TEMPLATE_NAME_CUSTOMER_ADDRESS_FORM, $viewModel);
        } else {
            $errMsgCode = 'error_edit_cust_addr_no_id';
            $errMsgCodes = [$errMsgCode];
            return $this->handleError(__METHOD__,$errMsgCodes);
        }
        return $string;
    }

    /**
     * @param ServerRequestInterface $request
     * @param string $company
     * @param string $customerNo
     * @return string
     */
    public function showAddress(ServerRequestInterface $request, string $company, string $customerNo): string
    {
        $queryParams = $request->getQueryParams();
        $bodyParams = $request->getParsedBody();
        $address = new CustomerAddress(new CustomerAddressConfig());
        $id = $queryParams[self::FIELD_NAME_ID] ?? $bodyParams[self::FIELD_NAME_ID] ?? null;
        if (null !== $id) {
            $id = (int)$id;
            $address = $this->customerAddressRepo->findByID($id);
        }
        $string = '';
        if ($company === $address->company && $customerNo === $address->customer_no) {
            $viewModel = new CustomerAddressViewModel($this->textProvider);
            $viewModel->customerAddress = $address;
            $string = $this->mustacheEngine->render(self::TEMPLATE_NAME_CUSTOMER_ADDRESS_VIEW, $viewModel);
        } else {
            //handle non-matching primary
        }
        return $string;
    }

    /**
     * @param ServerRequestInterface $request
     * @param string $company
     * @param string $customerNo
     * @return string
     * @throws \ErrorException
     */
    public function editAddress(ServerRequestInterface $request, string $company, string $customerNo): string
    {
        $bodyParams = $request->getParsedBody();
        $id = $bodyParams[self::FIELD_NAME_ID] ?? null;
        if (!((int)$id > 0)) {
            throw new \ErrorException('Can only edit Address with id');
        }
        $arr = [
            self::FIELD_NAME_ID         => (int)$id,
            self::FIELD_NAME_NAME       => $bodyParams[self::FIELD_NAME_NAME] ?? '',
            self::FIELD_NAME_NAME_2     => $bodyParams[self::FIELD_NAME_NAME_2] ?? '',
            self::FIELD_NAME_ADDRESS    => $bodyParams[self::FIELD_NAME_ADDRESS] ?? '',
            self::FIELD_NAME_ADDRESS_2  => $bodyParams[self::FIELD_NAME_ADDRESS_2] ?? '',
            self::FIELD_NAME_POST_CODE  => $bodyParams[self::FIELD_NAME_POST_CODE] ?? '',
            self::FIELD_NAME_CITY       => $bodyParams[self::FIELD_NAME_CITY] ?? '',
            self::FIELD_NAME_COUNTRY    => $bodyParams[self::FIELD_NAME_COUNTRY] ?? '',
            self::FIELD_NAME_CODE       => $bodyParams[self::FIELD_NAME_CODE] ?? '',
        ];
        /**
         * @var $address CustomerAddress
         */
        $address = $this->customerAddressRepo->findByID((int)$id);
        if ($company === $address->company && $customerNo === $address->customer_no) {
            $address->mapFromArray($arr);
            $validator = new NewValidator(CustomerAddress::getValidationRules(), $arr);
            $fieldStatusArr = [];
            if (!$validator->isValid($fieldStatusArr)) {
                throw new \DomainException('Entered address data does not meet validation criteria.' . ' Errors: ' . print_r($fieldStatusArr, true));
            }
            $this->customerAddressRepo->updateSingle($address);
            return $this->showAddress($request, $company, $customerNo);
        } else {
            return '';
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @param string $company
     * @param string $customerNo
     * @return string
     */
    public function showAddresses(ServerRequestInterface $request, string $company, string $customerNo): string
    {
        $addresses = $this->customerAddressRepo->getAllCustomerAddresses($company, $customerNo);
        $allBelongToCustomer = true;
        foreach ($addresses as $address) {
            if (!($company === $address->company && $address->customer_no === $customerNo)) {
                $allBelongToCustomer = false;
            }
        }
        $string = '';
        if ($allBelongToCustomer) {
            $viewModel = new CustomerAddressViewModel($this->textProvider);
            $viewModel->customerAddressCollection = $addresses;
            $string = $this->mustacheEngine->render(self::TEMPLATE_NAME_CUSTOMER_ADDRESS_LIST, $viewModel);
        } else {
            //handle non-matching primary
        }
        return $string;
    }

    /**
     * @param ServerRequestInterface $request
     * @param string $company
     * @param string $customerNo
     * @return string
     * @throws \ErrorException
     */
    public function createAddress(ServerRequestInterface $request, string $company, string $customerNo): string
    {
        $bodyParams = $request->getParsedBody();
        $id = $bodyParams[self::FIELD_NAME_ID] ?? null;
        if ((int)$id > 0) {
            throw new \ErrorException('Can only create Address without id');
        }
        $string = '';
        if (array_key_exists(self::FIELD_NAME_COMPANY, $bodyParams)
            && array_key_exists(self::FIELD_NAME_CUSTOMER_NO, $bodyParams)
            && $company === $bodyParams[self::FIELD_NAME_COMPANY]
            && $customerNo === $bodyParams[self::FIELD_NAME_CUSTOMER_NO]) {

            $arr = [
                self::FIELD_NAME_ID             => (int)$id,
                self::FIELD_NAME_COMPANY        => $company,
                self::FIELD_NAME_CUSTOMER_NO    => $customerNo,
                self::FIELD_NAME_NAME           => $bodyParams[self::FIELD_NAME_NAME] ?? '',
                self::FIELD_NAME_NAME_2         => $bodyParams[self::FIELD_NAME_NAME_2] ?? '',
                self::FIELD_NAME_ADDRESS        => $bodyParams[self::FIELD_NAME_ADDRESS] ?? '',
                self::FIELD_NAME_ADDRESS_2      => $bodyParams[self::FIELD_NAME_ADDRESS_2] ?? '',
                self::FIELD_NAME_POST_CODE      => $bodyParams[self::FIELD_NAME_POST_CODE] ?? '',
                self::FIELD_NAME_CITY           => $bodyParams[self::FIELD_NAME_CITY] ?? '',
                self::FIELD_NAME_COUNTRY        => $bodyParams[self::FIELD_NAME_COUNTRY] ?? '',
                self::FIELD_NAME_CODE           => $bodyParams[self::FIELD_NAME_CODE] ?? '',
            ];
            $address = new CustomerAddress(new CustomerAddressConfig());
            $address->mapFromArray($arr);
            $validator = new NewValidator(CustomerAddress::getValidationRules(), $arr);
            $fieldStatusArr = [];
            if (!$validator->isValid($fieldStatusArr)) {
                throw new \DomainException('Entered address data does not meet validation criteria.' . ' Errors: ' . print_r($fieldStatusArr, true));
            }
            $id = $this->customerAddressRepo->createInDB($address);
            $string = $this->showAddresses($request, $company, $customerNo);
        } else {
            //handle non-matching primary
        }
        return $string;
    }

    /**
     * @param CustomerAddress|null $address
     * @return string
     */
    public function getCustomerAddressForm(?CustomerAddress $address): string
    {
        if (null === $address) {
            $address = new CustomerAddress(new CustomerAddressConfig());
        }

        $viewModel = new CustomerAddressViewModel($this->textProvider);
        $viewModel->customerAddress = $address;

        $string = $this->mustacheEngine->render(self::TEMPLATE_NAME_CUSTOMER_ADDRESS_FORM, $viewModel);
        return $string;
    }

    public function handleError(string $fromMethod) : string
    {
        $redirectTo = self::ERR_ACTION_REDIRECTS[$fromMethod] ?? '/';
        if ($redirectTo !== '/') {

        }

    }

}