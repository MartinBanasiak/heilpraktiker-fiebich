<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\classes\URLMaker;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 5/31/2015
 * Time: 9:57 PM
 */
class ComputopPaygateWrapper
{
    protected const PAY_TYPE_DIRECT_DEBIT = 'PAY_TYPE_DIRECT_DEBIT';
    protected const PAY_TYPE_CREDIT_CARD_DIRECT = 'PAY_TYPE_CREDIT_CARD_DIRECT';
    protected const PAY_TYPE_CREDIT_CARD_DELAYED = 'PAY_TYPE_CREDIT_CARD_DELAYED';
    protected const PAY_TYPE_PAYPAL_DIRECT = 'PAY_TYPE_PAYPAL_DIRECT';
    protected const PAY_TYPE_PAYPAL_DELAYED = 'PAY_TYPE_PAYPAL_DELAYED';
    protected const PAY_TYPE_BILLPAY_INVOICE = 'PAY_TYPE_BILLPAY_INVOICE';
    protected const PAY_TYPE_BILLPAY_DEBIT = 'PAY_TYPE_BILLPAY_DEBIT';
    protected const PAY_TYPE_BILLPAY_DEFERRED = 'PAY_TYPE_BILLPAY_DEFERRED';
    protected const PAY_TYPE_GIROPAY = 'PAY_TYPE_GIROPAY';
    protected const PAY_TYPE_SOFORTUEBERWEISUNG = 'PAY_TYPE_SOFORTUEBERWEISUNG';

    protected const CCSELECT_VISA = 'VISA';
    protected const CCSELECT_MASTERCARD = 'MasterCard';
    protected const CCSELECT_AMEX = 'AMEX';
    protected const CCSELECT_DINERS = 'DINERS';
    protected const CCSELECT_JCB = 'JCB';
    protected const CCSELECT_CBN = 'CBN';
    protected const CCSELECT_SWITCH = 'SWITCH';
    protected const CCSELECT_SOLO = 'SOLO';
    protected const CCSELECT_HIPERCARD = 'Hipercard';
    protected const CCSELECT_ELO = 'Elo';
    protected const CCSELECT_AURA = 'Aura';
    protected const CCSELECT_DANKORT = 'Dankort';

    protected const CCBRAND_VISA = 'VISA';
    protected const CCBRAND_MASTERCARD = 'MasterCard';
    protected const CCBRAND_MAESTRO = 'Maestro';
    protected const CCBRAND_AMEX = 'AMEX';
    protected const CCBRAND_JCB = 'JCB';
    protected const CCBRAND_CBN = 'CBN';
    protected const CCBRAND_SWITCH = 'SWITCH';
    protected const CCBRAND_SOLO = 'SOLO';
    protected const CCBRAND_DINERS = 'DINERS';
    protected const CCBRAND_ELO = 'ELO';
    protected const CCBRAND_AURA = 'AURA';
    protected const CCBRAND_HIPERCARD = 'Hipercard';

    protected const URL_QUERY_STRING_MAXLENGTH = 5120;

    protected const URL_PAYGATE_DOMAIN = 'www.comutop-paygate.com';
    protected const URL_SERVER_2_SERVER = 'direct.aspx';
    protected const URL_PAY_SSL = 'paySSL.aspx';
    protected const URL_PAY_SDD = 'paySDD.aspx';
    protected const URL_ONLINE_TRANSACTION = 'payOTF.aspx';
    protected const URL_PAYPAL = 'PayPal.aspx';
    protected const URL_PREPAY_VIA_DEBIT = 'cgedd.aspx';
    protected const URL_DEBIT = 'payELV.aspx';
    protected const URL_PREPAY_VIA_CC = 'cgcc.aspx';
    protected const URL_SOFORT = 'sofort.aspx';
    protected const URL_COMPUTOP_IMPRINT = 'impressum.aspx';
    protected const URL_KPN_HELP = 'kpn.aspx';
    protected const URL_VERIFIED_BY_VISA_LOGO_LINK = 'vbv.aspx';
    protected const URL_PAY_NOW = 'PayNow.aspx';
    protected const URL_MASTER_PASS = 'MasterPass.aspx';
    protected const URL_MASTER_PASS_DIRECT = 'directmp.aspx';
    protected const URL_POSTPAY = 'postpay.aspx';
    protected const URL_YAPITAL = 'yapital.aspx';
    protected const URL_YAPITAL_POS = 'yapitalPOS.aspx';
    protected const URL_YAPITAL_CREDIT = 'YapitalCredit.aspx';
    protected const URL_IPAY = 'iPay.aspx';
    protected const URL_ALIPAY = 'alipay.aspx';
    protected const URL_SKRILL = 'skrill.aspx';
    protected const URL_QIWI = 'qiwi.aspx';
    protected const URL_PAYU = 'payu.aspx';
    protected const URL_DIRECT_DEBIT_INTL = 'pproELV.aspx';
    protected const URL_DEBIT_UK = 'mandate.aspx';
    protected const URL_DEBIT_UK_PAY_INIT = 'debitUK.aspx';
    protected const URL_CASH_AND_GO_DIRECT_CC = 'cgdirectcc.aspx';
    protected const URL_CASH_AND_GO_DIRECT_EDD = 'cgdirectedd.aspx';
    protected const URL_EDD_DIRECT = 'edddirect.aspx';
    protected const URL_CASH_AND_GO_EDD = 'cgedd.aspx';
    protected const URL_MPASS = 'mpass.aspx';
    protected const URL_MILES_MORE = 'MilesandMore.aspx';
    protected const URL_MILES_MORE_FORM = 'MilesandMoreForm.aspx';
    protected const URL_GIROPAY = 'giropay.aspx';
    protected const URL_GIROPAY_CHECK = 'giropayList.aspx';
    protected const URL_IDEAL = 'ideal.aspx';
    protected const URL_IDEAL_ISSUER_LIST = 'idealIssuerList.aspx';
    protected const URL_EPS = 'EPS.aspx';
    protected const URL_P24 = 'p24.aspx';
    protected const URL_POSTFINANCE = 'postfinance.aspx';
    protected const URL_POLI = 'poli.aspx';
    protected const URL_TRUSTPAY = 'trustpay.aspx';
    protected const URL_TRUSTLY = 'trustly.aspx';
    protected const URL_PAG_BRASIL_OTF = 'BrazilOTF.aspx';
    protected const URL_PAYGATE_CHINA = 'China.aspx';
    protected const URL_SAFETYPAY = 'safetypay.aspx';
    protected const URL_PAYSAFE_CARD = 'psc.aspx';
    protected const URL_ASTROPAY = 'astropay.aspx';
    protected const URL_TELEINGRESO = 'teleingreso.aspx';
    protected const URL_RABERIL = 'raberil.aspx';
    protected const URL_RABERIL_PAYOUT = 'raberilpayout.aspx';
    protected const URL_UKASH = 'ukash.aspx';
    protected const URL_PAG_BRASIL_BOLETO_BANCARIO = 'BoletoBancario.aspx';
    protected const URL_BILLPAY = 'Billpay.aspx';
    protected const URL_CAPTURE = 'capture.aspx';
    protected const URL_REVERSE = 'reverse.aspx';
    protected const URL_CREDIT = 'credit.aspx';
    protected const URL_INQUIRE = 'inquire.aspx';
    protected const URL_BILLSAFE = 'billsafe.aspx';
    protected const URL_BILLSAFE_DIRECT = 'BillSAFEdirect.aspx';
    protected const URL_PAY_BY_BILL = 'paybybill.aspx';
    protected const URL_KLARNA = 'Klarna.aspx';
    protected const URL_KLARNA_EMAIL = 'KlarnaEmail.aspx';
    protected const URL_KLARNA_CHECKOUT = 'KCO.aspx';
    protected const URL_RATEPAY = 'ratepay.aspx';
    protected const URL_PAYMORROW = 'paymorrow.aspx';
    protected const URL_AMAZON = 'cba.aspx';
    protected const URL_BARZAHLEN = 'barzahlen.aspx';
    protected const URL_CURRENCY_CONVERSION_E4X = 'fcc.aspx';
    protected const URL_CANCEL_AUTH = 'cancelAuth.aspx';
    protected const URL_STATIONARY = 'stationary.aspx';
    protected const URL_STATIONARY_REVOKE = 'stationary_rev.aspx';
    protected const URL_ABO = 'abo.aspx';
    protected const URL_PAYPROTECT = 'payprotect.aspx';
    protected const URL_DELTAVISTA = 'deltavista.aspx';
    protected const URL_SCHUFA_IDENT_DIRECT = 'SchufaIdentDirect.aspx';
    protected const URL_ARVATO_INFOSCORE = 'escore.aspx';
    protected const URL_BUERGEL = 'Buergel.aspx';
    protected const URL_BONIVERSUM = 'boniversum.aspx';
    protected const URL_DEBTOR = 'debtor.aspx';


    protected const PARAM_MERCHANT_ID = 'MerchantID';
    protected const PARAM_LENGTH = 'Len';
    protected const PARAM_DATA = 'Data';
    protected const PARAM_TRANS_ID = 'TransID';
    protected const PARAM_CUSTOMER_ID = 'CustomerID';
    protected const PARAM_REF_NO = 'RefNr';
    protected const PARAM_AMOUNT = 'Amount';
    protected const PARAM_CURRENCY = 'Currency';
    protected const PARAM_ORDER_DESC = 'OrderDesc';
    protected const PARAM_ORDER_DESC_MULTI = 'OrderDesc_n';
    protected const PARAM_MAC = 'MAC';
    protected const PARAM_MAX = 'MAX';
    protected const PARAM_URL_SUCCESS = 'URLSuccess';
    protected const PARAM_URL_NOTIFY = 'URLNotify';
    protected const PARAM_URL_FAILURE = 'URLFailure';
    protected const PARAM_RESPONSE = 'Response';
    protected const PARAM_USER_DATA = 'UserData';
    protected const PARAM_CAPTURE = 'Capture';
    protected const PARAM_REQ_ID = 'ReqID';
    protected const PARAM_EXIPRATION_TIME = 'expirationTime';
    protected const PARAM_CREDIT_CARD_HOLDER = 'CreditCardHolder';
    protected const PARAM_NAME = 'Name';
    protected const PARAM_LANGUAGE = 'Language';
    protected const PARAM_SELLING_POINT = 'SellingPoint';
    protected const PARAM_SERVICE = 'Service';
    protected const PARAM_CHANNEL = 'Channel';
    protected const PARAM_ADDR_COUNTRY_CODE = 'AddrCountryCode';
    protected const PARAM_RTF = 'RTF';
    protected const PARAM_IP_ADDR = 'IPAddr';
    protected const PARAM_IP_ZONE = 'IPZone';
    protected const PARAM_ZONE = 'Zone';
    protected const PARAM_MARP = 'MARP';
    protected const PARAM_AD_DATA_1 = 'Addata1';
    protected const PARAM_ABO_ACTION = 'AboAction';
    protected const PARAM_START_DATE = 'StartDate';
    protected const PARAM_END_DATE = 'EndDate';
    protected const PARAM_INTERVAL = 'Interval';
    protected const PARAM_ABO_AMOUNT = 'AboAmount';
    protected const PARAM_ADDR_STREET = 'AddrStreet';
    protected const PARAM_ADDR_STREET_NO = 'AddrStreetNr';
    protected const PARAM_ADDR_POST_CODE = 'AddrZip';
    protected const PARAM_ADDR_CITY = 'AddrCity';
    protected const PARAM_ADDR_STREET_2 = 'AddrStreet2';
    protected const PARAM_ADDR_STREET_NO_2 = 'AddrStreetNr2';
    protected const PARAM_ADDR_POST_CODE_2 = 'AddrZip2';
    protected const PARAM_ADDR_CITY_2 = 'AddrCity2';
    protected const PARAM_ADDR_CHOICE = 'AddrChoice';
    protected const PARAM_FIRST_NAME = 'FirstName';
    protected const PARAM_LAST_NAME = 'LastName';
    protected const PARAM_EMAIL = 'eMail';
    protected const PARAM_PHONE = 'Phone';
    protected const PARAM_SHIP_FIRST_NAME = 'sdFirstName';
    protected const PARAM_SHIP_MIDDLE_NAME = 'dsMiddleName';
    protected const PARAM_SHIP_LAST_NAME = 'sdLastName';
    protected const PARAM_SHIP_STREET = 'sdStreet';
    protected const PARAM_SHIP_STREET_2 = 'sdStreet2';
    protected const PARAM_SHIP_STREET_NO = 'sdStreetNr';
    protected const PARAM_SHIP_CITY = 'sdCity';
    protected const PARAM_SHIP_STATE = 'sdState';
    protected const PARAM_SHIP_POST_CODE = 'sdZip';
    protected const PARAM_SHIP_POST_CODE_PAYPALCC = 'sdZIPCode';
    protected const PARAM_SHIP_EMAIL = 'sdeMail';
    protected const PARAM_SHIP_EMAIL_PAYPALCC = 'sdEMail';
    protected const PARAM_SHIP_PHONE = 'sdPhone';
    protected const PARAM_SHIP_WORK_PHONE = 'sdWorkPhone';
    protected const PARAM_SHIP_COUNTRY_CODE = 'sdCountryCode';
    protected const PARAM_CC_HOLDER = 'CreditCardHolder';
    protected const PARAM_MIDDLE_NAME = 'MiddleName';
    protected const PARAM_SALUTATION = 'Salutation';
    protected const PARAM_TITLE = 'Title';
    protected const PARAM_COMPANY_OR_PERSON = 'CompanyOrPerson';
    protected const PARAM_DATE_OF_BIRTH = 'DateOfBirth';
    protected const PARAM_GENDER = 'Gender';
    protected const PARAM_ADDR_ADD = 'AddressAddition';
    protected const PARAM_ADDR_STATE = 'AddrState';
    protected const PARAM_ADDR_DISTRICT = 'AddrDistrict';
    protected const PARAM_ADDR_POBOX = 'AddrPOBox';
    protected const PARAM_ADDR_CO_FIELD = 'AddrCOField';
    protected const PARAM_WORK_PHONE = 'WorkPhone';
    protected const PARAM_FAX = 'Fax';
    protected const PARAM_EMAIL_EVO = 'E-Mail';
    protected const PARAM_NEW_CUSTOMER = 'NewCustomer';
    protected const PARAM_DATE_OF_REGISTRATION = 'DateOfRegistration';
    protected const PARAM_SOCIAL_SECURITY_NO = 'SocialSecurityNumber';
    protected const PARAM_DRIVING_LICENSE_NO = 'DrivingLicenseNo';
    protected const PARAM_CHARGE_DESCRIPTION = 'ChDesc';
    protected const PARAM_BILL_FIRST_NAME = 'bdFirstName';
    protected const PARAM_BILL_MIDDLE_NAME = 'bdMiddleName';
    protected const PARAM_BILL_LAST_NAME = 'bdLastName';
    protected const PARAM_BILL_STREET = 'bdStreet';
    protected const PARAM_BILL_STREET_NO = 'bdStreetNr';
    protected const PARAM_BILL_STREET_2 = 'bdStreet2';
    protected const PARAM_BILL_POST_CODE = 'bdZIPCode';
    protected const PARAM_BILL_CITY = 'bdCity';
    protected const PARAM_BILL_STATE = 'bdState';
    protected const PARAM_BILL_COUNTRY_CODE = 'bdCountryCode';
    protected const PARAM_BILL_PHONE = 'bdPhone';
    protected const PARAM_BILL_WORK_PHONE = 'bdWorkPhone';
    protected const PARAM_BILL_FAX = 'bdFax';
    protected const PARAM_BILL_EMAIL = 'bdEMail';
    protected const PARAM_QUOTE_ID = 'QuoteID';
    protected const PARAM_BASE_AMOUNT = 'BaseAmount';
    protected const PARAM_LOCAL_AMOUNT = 'LocalAmount';
    protected const PARAM_AMOUNT_3D = 'Amount3D';
    protected const PARAM_CARD_NO = 'CCnr';
    protected const PARAM_CARD_CVC = 'CCCVC';
    protected const PARAM_CARD_BRAND = 'CCBrand';
    protected const PARAM_CARD_EXPIRY = 'CCExpiry';
    protected const PARAM_ACCEPTABLE_CARDS = 'AcceptableCards';
    protected const PARAM_SHIPPING_PROFILE = 'ShippingProfile';
    protected const PARAM_SUPPRESS_SHIP_ADDR = 'SuppressShippingAddress';
    protected const PARAM_REWARD_PROGRAM = 'RewardProgram';
    protected const PARAM_AUTH_LEVEL_BASIC = 'AuthLevelBasic';
    protected const PARAM_ADDR_STREET_3 = 'AddrStreet3';
    protected const PARAM_TAX_NUMBER = 'TaxNumber';
    protected const PARAM_ARTICLE_LIST = 'ArticleList';
    protected const PARAM_INSTALLMENT_NO = 'InstallmentNumber';
    protected const PARAM_TAX_TOTAL = 'TaxTotal';
    protected const PARAM_ITEM_TOTAL = 'ItemTotal';
    protected const PARAM_SHIP_COST = 'ShAmount';
    protected const PARAM_ACCOUNT = 'Account';
    protected const PARAM_BRAND_NAME = 'BrandName';
    protected const PARAM_HEADER_LOGO = 'HeaderLogo';
    protected const PARAM_BGCOLOR = 'BGColor';
    protected const PARAM_BGIMAGE = 'BGImage';
    protected const PARAM_NOTE = 'Note';
    protected const PARAM_NO_SHIPPING = 'NoShipping';
    protected const PARAM_ALLOW_NOTE = 'AllowNote';
    protected const PARAM_SUBJECT = 'Subject';
    protected const PARAM_YAPITAL_RDQR = 'RdQR';
    protected const PARAM_YAPITAL_SHIP_ADDR_READONLY = 'sdReadOnly';
    protected const PARAM_YAPITAL_SHIP_ADDR_ADD = 'sdAddressAddition';
    protected const PARAM_YAPITAL_SHIP_ADDR_POST_CODE = 'sdZip';
    protected const PARAM_YAPITAL_SHIP_ADDR_TITLE = 'sdTitle';
    protected const PARAM_BILL_TITLE = 'bdTitle';
    protected const PARAM_BILL_ADDR_ADD = 'bdAddressAddition';
    protected const PARAM_BILL_ADDR_POST_CODE = 'bdZip';
    protected const PARAM_YAPITAL_POS_ARTICLE_LIST = 'Articlelist';
    protected const PARAM_YAPITAL_TRX_ID_COUNT = 'TrxIDCount';
    protected const PARAM_IPAY_FX_CURRENCY = 'FxCurrency';
    protected const PARAM_ACC_BANK = 'AccBank';
    protected const PARAM_PPRO_SKRILL_SELLING_POINT = 'SellingPoint';
    protected const PARAM_QIWI_MOBILE_NO = 'MobileNo';
    protected const PARAM_ORDER_DESC_2 = 'OrderDesc2';
    protected const PARAM_ORDER_DESC_3 = 'OrderDesc3';
    protected const PARAM_BILLSAFE_EVENT_TOKEN = 'EventToken';
    protected const PARAM_BILLSAFE_OUTPUT_TYPE = 'OutputType';
    protected const PARAM_EDD_METHOD = 'EDDMethod';
    protected const PARAM_MANDATE_ID = 'MandateID';
    protected const PARAM_DATE_OF_SIGNATURE = 'DtOfSgntr';
    protected const PARAM_SEPA_SEQ_TYPE = 'MdtSeqType';
    protected const PARAM_PBAN = 'PBAN';
    protected const PARAM_SEPA_SUB_SEQ_TYPE = 'SubSeqType';
    protected const PARAM_DEBIT_DELAY = 'DebitDelay';
    protected const PARAM_CREDIT_DELAY = 'CreditDelay';
    protected const PARAM_BILL_MAIDEN_NAME = 'bdMaidenName';
    protected const PARAM_USE_BILLING_DATA = 'UseBillingData';
    protected const PARAM_REGULAR_CUSTOMER_INDICATOR = 'RegularCustomerIndicator';
    protected const PARAM_SHOPPING_BASKET = 'ShoppingBasket';
    protected const PARAM_ACC_OWNER = 'AccOwner';
    protected const PARAM_ACC_NO = 'AccNr';
    protected const PARAM_ACC_IBAN = 'AccIBAN';
    protected const PARAM_ACC_BANK_CITY = 'AccBankCity';
    protected const PARAM_MANDATE_NAME = 'MandateName';
    protected const PARAM_OTF_METHOD = 'otfMethod';
    protected const PARAM_SHOW_ACC_NO = 'ShowAccNr';
    protected const PARAM_SEPA_PRESELECT = 'SEPApreselect';
    protected const PARAM_ACC_EDIT = 'AccEdit';
    protected const PARAM_AUTO_START = 'AutoStart';
    protected const PARAM_ISSUER_ID = 'IssuerID';
    protected const PARAM_PRODUCT_NAME = 'ProductName';
    protected const PARAM_PAY_TYPE = 'PayType';
    protected const PARAM_SOFORTACTION = 'Sofortaction';
    protected const PARAM_TIMEOUT = 'Timeout';
    protected const PARAM_BIRTHDAY = 'Birthday';
    protected const PARAM_DISPOSITION_RESTRICTION_AGE = 'DispositionRestrictionAge';
    protected const PARAM_DISPOSITION_RESTRICTION_KYC = 'DispositionRestrictionKYC';
    protected const PARAM_DISPOSITION_RESTRICTION_COUNTRY = 'DispositionRestrictionCountry';
    protected const PARAM_EXPIRATION = 'Expiration';
    protected const PARAM_EXPIRATION_TIME = 'expirationTime';
    protected const PARAM_BILLPAY_ACTION = 'BillPayAction';
    protected const PARAM_GTC_VALUE = 'GtcValue';
    protected const PARAM_BP_METHOD = 'BpMethod';
    protected const PARAM_COMPANY_NAME = 'CompanyName';
    protected const PARAM_LEGAL_FORM = 'LegalForm';
    protected const PARAM_REGISTER_NUMBER = 'RegisterNumber';
    protected const PARAM_HOLDER_NAME = 'HolderName';
    protected const PARAM_CUSTOMER_CLASSIFICATION = 'CustomerClassification';
    protected const PARAM_ORDER_HISTORY = 'OrderHistory';
    protected const PARAM_LIMIT = 'Limit';
    protected const PARAM_BP_BASE_AMOUNT = 'BpBaseAmount';
    protected const PARAM_BP_RATE_COUNT = 'BpRateCount';
    protected const PARAM_BILL_SALUTATION = 'bdSalutation';
    protected const PARAM_SHIP_SALUTATION = 'sdSalutation';
    protected const PARAM_SHIP_MOBILE_NO = 'sdMobileNo';
    protected const PARAM_PARCEL_SERVICE = 'ParcelService';
    protected const PARAM_ANNUAL_SALARY = 'AnnualSalary';
    protected const PARAM_KLARNA_ACTION = 'KlarnaAction';
    protected const PARAM_INVOICE_FLAG = 'InvoiceFlag';
    protected const PARAM_NUMBER_PAID_PURCHASES = 'NumberPaidPurchases';
    protected const PARAM_DATE_LAST_PAID_PURCHASE = 'DateOfLastPaidPurchase';
    protected const PARAM_DATE_FIRST_PAID_PURCHASE = 'DateOfFirstPaidPurchase';
    protected const PARAM_HTTP_REFERRER = 'HttpReferrer';
    protected const PARAM_USER_AGENT = 'UserAgent';
    protected const PARAM_SCREEN_RESOLUTION = 'ScreenResolution';
    protected const PARAM_COOKIES_ENABLED = 'CookiesEnabled';
    protected const PARAM_ACCOUNT_ID = 'AccountId';
    protected const PARAM_REGISTRATION_DATE = 'RegistrationDate';
    protected const PARAM_SHOPPED_BEFORE = 'ShoppedBefore';
    protected const PARAM_DELIVERY_METHOD = 'DeliveryMethod';
    protected const PARAM_PURCHASE_COUNTRY_CODE = 'PurchaseCountryCode';
    protected const PARAM_URL_CHECKOUT = 'URLCheckout';
    protected const PARAM_URL_CONFIRM = 'URLConfirm';
    protected const PARAM_URL_TERMS = 'URLTerms';
    protected const PARAM_PHONE_AREA_CODE = 'PhoneAreaCode';
    protected const PARAM_FAX_AREA_CODE = 'FaxAreaCode';
    protected const PARAM_MOBILE_AREA_CODE = 'MobileAreaCode';
    protected const PARAM_SHIP_STREET_HOUSE_NO = 'sdStreetHouseNumber';
    protected const PARAM_SHIP_ADDRESS_ADDITION = 'sdAddressAddition';
    protected const PARAM_NATIONALITY = 'Nationality';
    protected const PARAM_ALLOW_MARKETING = 'AllowMarketing';
    protected const PARAM_ALLOW_CRED_INQ = 'AllowCredInq';
    protected const PARAM_COMPANY_ID = 'CompanyID';
    protected const PARAM_VAT_ID = 'VatID';
    protected const PARAM_SHOPPING_BASKET_AMOUNT = 'ShoppingBasketAmount';
    protected const PARAM_DEVICE_SITE = 'DeviceSite';
    protected const PARAM_DEVICE_TOKEN = 'DeviceToken';
    protected const PARAM_INVOICE_ID = 'InvoiceID';
    protected const PARAM_INTEREST_RATE = 'InterestRate';
    protected const PARAM_MONTH = 'Month';
    protected const PARAM_RATE = 'Rate';
    protected const PARAM_DUE_DATE = 'DueDate';
    protected const PARAM_FIRST_DAY = 'FirstDay';
    protected const PARAM_TOTAL_AMOUNT = 'TotalAmount';
    protected const PARAM_INTEREST_AMOUNT = 'InterestAmount';
    protected const PARAM_SERVICE_CHARGE = 'ServiceCharge';
    protected const PARAM_ANNUAL_PERCENTAGE_RATE = 'AnnualPercentageRate';
    protected const PARAM_MONTHLY_DEBIT_INTEREST = 'MonthlyDebitInterest';
    protected const PARAM_INSTALLMENT_NUMBER = 'InstallmentNumber';
    protected const PARAM_INSTALLMENT_AMOUNT = 'InstallmentAmount';
    protected const PARAM_INSTALLMENT_LAST_AMOUNT = 'InstallmentLastAmount';
    protected const PARAM_INVOICE_DATE = 'InvoiceDate';
    protected const PARAM_DELIVERY_DATE = 'DeliveryDate';
    protected const PARAM_TRACKING_ID = 'TrackingID';
    protected const PARAM_MAX_RISK = 'MaxRisk';
    protected const PARAM_DEBIT_PAY_TYPE = 'DebitPayType';
    protected const PARAM_IBAN = 'IBAN';
    protected const PARAM_RP_METHOD = 'RPMethod';
    protected const PARAM_TERMS_AND_CONDITIONS = 'TermsAndConditions';
    protected const PARAM_EMAIL_LOWER_SEPARATED = 'e-mail';
    protected const PARAM_CUSTOMER_HISTORY = 'CustomerHistory';
    protected const PARAM_SHOPPING_DURATION = 'ShoppingDuration';
    protected const PARAM_CHECKOUT_DURATION = 'CheckoutDuration';
    protected const PARAM_DEVICE_ID = 'DeviceID';
    protected const PARAM_NUMBER_OF_DAYS = 'NumberOfDays';
    protected const PARAM_PURCHASE_CONTRACT_ID = 'PurchaseContractID';
    protected const PARAM_CHARGE_AMOUNT = 'ChargeAmount';
    protected const PARAM_TAX_CHARGE_AMOUNT = 'TaxChargeAmount';
    protected const PARAM_PROMOTION = 'Promotion';
    protected const PARAM_FULFILLMENT_DATE = 'FulfillmentDate';
    protected const PARAM_CARRIER_NAME = 'CarrierName';
    protected const PARAM_SHIPPING_METHOD = 'ShippingMethod';
    protected const PARAM_TRACKING_NUMBER = 'TrackingNumber';
    protected const PARAM_REASON = 'Reason';
    protected const PARAM_MM_LOGIN = 'MMLogin';
    protected const PARAM_MM_PW = 'MMPW';
    protected const PARAM_AWARDCODE = 'Awardcode';
    protected const PARAM_MILES = 'Miles';
    protected const PARAM_TEXTFELD_1 = 'Textfeld1';
    protected const PARAM_TEXTFELD_2 = 'Textfeld2';
    protected const PARAM_PARTNER_DATA = 'PartnerData';
    protected const PARAM_PID = 'Pid';
    protected const PARAM_PROPERTY = 'Property';
    protected const PARAM_TRANSTEXT = 'Transtext';
    protected const PARAM_FINISH_AUTH = 'FinishAuth';
    protected const PARAM_DELAY = 'Delay';
    protected const PARAM_PRODUCT_NUMBER = 'ProductNr';
    protected const PARAM_PARCEL_TRACKING_ID = 'ParcelTrackingID';
    protected const PARAM_COMPLETE_TYPE = 'Completetype';
    protected const PARAM_TID = 'TID';
    protected const PARAM_INVOICE_NUMBER = 'InvoiceNr';
    protected const PARAM_CRED_NO = 'CredNo';
    protected const PARAM_NET_REBATE = 'NetRebate';
    protected const PARAM_GROSS_REBATE = 'GrRebate';
    protected const PARAM_SHIP_REBATE = 'shRebate';
    protected const PARAM_SHIP_REBATE_GROSS = 'shRebateGr';
    protected const PARAM_TRACK_2 = 'Track2';
    protected const PARAM_TRACK_3 = 'Track3';
    protected const PARAM_CC_NUMBER = 'CCnr';
    protected const PARAM_CC_CVC = 'CCCVC';
    protected const PARAM_CC_EXPIRY = 'CCExpiry';
    protected const PARAM_CC_BRAND = 'CCBrand';
    protected const PARAM_PRODUCT_COUNTRY = 'ProductCountry';
    protected const PARAM_CUSTOMER_CARD_NUMBER = 'CustomerCardNr';
    protected const PARAM_MATCH_FIRST_NAME = 'MatchFirstname';
    protected const PARAM_MATCH_SURNAME = 'MatchSurname';
    protected const PARAM_MATCH_BIRTHDATE = 'MatchBirthdate';
    protected const PARAM_MATCH_ADDR_STREET = 'MatchAddrStreet';
    protected const PARAM_MATCH_ADDR_POST_CODE = 'MatchAddrZip';
    protected const PARAM_MATCH_ADDR_CITY = 'MatchAddrCity';
    protected const PARAM_CHECK_EDD = 'CheckEDD';
    protected const PARAM_ACCOUNT_NUMBER = 'AccNr';
    protected const PARAM_ACCOUNT_IBAN = 'AccIBAN';
    protected const PARAM_ACCOUNT_BANK = 'AccBank';
    protected const PARAM_ACCOUNT_OWNER = 'AccOwner';
    protected const PARAM_CHECK_CC = 'CheckCC';
    protected const PARAM_CUSTM_ID = 'CustmID';
    protected const PARAM_REQUEST_REASON = 'RequestReason';
    protected const PARAM_AV_FEATURE = 'AVFeature';
    protected const PARAM_CONSENT = 'Consent';
    protected const PARAM_TAX_ID = 'TaxId';
    protected const PARAM_DEBITOR_ID = 'DebitorID';
    protected const PARAM_CUST_GROUP = 'Custgroup';
    protected const PARAM_AMOUNT_CLASS = 'AmountClass';
    protected const PARAM_SHOP_ID = 'ShopID';
    protected const PARAM_PAY_TERM = 'PayTerm';
    protected const PARAM_NEXT_AMOUNT = 'NextAmount';
    protected const PARAM_TAX_CODE_1 = 'TaxCode1';
    protected const PARAM_TAX_AMOUNT_1 = 'TaxAmount1';
    protected const PARAM_TAX_CODE_2 = 'TaxCode2';
    protected const PARAM_TAX_AMOUNT_2 = 'TaxAmount2';
    protected const PARAM_TAX_CODE_3 = 'TaxCode3';
    protected const PARAM_TAX_AMOUNT_3 = 'TaxAmount3';
    protected const PARAM_PLACE_OF_BIRTH = 'PlaceOfBirth';
    protected const PARAM_NET_AMOUNT = 'NetAmount';
    protected const PARAM_TAX_CODE = 'TaxCode';
    protected const PARAM_INVOICE_TEXT = 'InvoiceText';
    protected const PARAM_COUNT_RECORDS = 'CountRecords';
    protected const PARAM_SUM_AMOUNT = 'SumAmount';
    protected const PARAM_VERSION = 'Version';

    protected const AMAZON_CREDIT_REASON_NO_INVENTORY = 'NoInventory';
    protected const AMAZON_CREDIT_REASON_CUSTOMER_RETURN = 'CustomerReturn';
    protected const AMAZON_CREDIT_REASON_GENERAL_ADJUSTMENT = 'GeneralAdjustment';
    protected const AMAZON_CREDIT_REASON_COULD_NOT_SHIP = 'CouldNotShip';
    protected const AMAZON_CREDIT_REASON_DIFFERENT_ITEM = 'DifferentItem';
    protected const AMAZON_CREDIT_REASON_ABANDONED = 'Abandoned';
    protected const AMAZON_CREDIT_REASON_CUSTOMER_CANCEL = 'CustomerCancel';
    protected const AMAZON_CREDIT_REASON_PRICE_ERROR = 'PriceError';
    protected const AMAZON_CREDIT_REASON_PRODUCT_OUT_OF_STOCK = 'ProductOutOfStock';
    protected const AMAZON_CREDIT_REASON_CUSTOMER_ADDRESS_INCORRECT = 'CustomerAddressIncorrect';
    protected const AMAZON_CREDIT_REASON_EXCHANGE = 'Exchange';
    protected const AMAZON_CREDIT_REASON_OTHER = 'Other';
    protected const AMAZON_CREDIT_REASON_CARRIER_CREDIT_DECISION = 'CarrierCreditDecision';
    protected const AMAZON_CREDIT_REASON_RISK_INFO_NOT_VALID = 'RiskAssessmentInformationNotValid';
    protected const AMAZON_CREDIT_REASON_CARRIER_COVERAGE_FAILURE = 'CarrierCoverageFailure';
    protected const AMAZON_CREDIT_REASON_TRANSACTION_RECORD = 'TransactionRecord';

    protected const BP_OPERATION_GET_PAYMENT_INSTRUCTION = 'getPaymentInstruction';
    protected const BP_OPERATION_GET_TRANSACTION_ID = 'getTransactionId';
    protected const BP_OPERATION_GET_PAYOUT_STATUS = 'getPayoutStatus';
    protected const BP_OPERATION_GET_AGREED_HANDLING_CHARGES = 'getAgreedHandlingCharges';
    protected const BP_OPERATION_REPORT_DIRECT_PAYAMENT = 'reportDirectPayment';
    protected const BP_OPERATION_SET_ORDER_NUMBER = 'setOrderNumber';

    protected const BS_OPERATION_GET_PAYMENT_INSTRUCTION = 'getPaymentInstruction';
    protected const BS_OPERATION_GET_TRANSACTION_ID = 'getTransactionId';
    protected const BS_OPERATION_GET_PAYOUT_STATUS = 'getPayoutStatus';
    protected const BS_OPERATION_GET_AGREED_HANDLING_CHARGES = 'getAgreedHandlingCharges';
    protected const BS_OPERATION_REPORT_DIRECT_PAYAMENT = 'reportDirectPayment';
    protected const BS_OPERATION_SET_ORDER_NUMBER = 'setOrderNumber';

    protected const ESCORE_REQ_REASON_ABK = 'ABK';
    protected const ESCORE_REQ_REASON_ABV = 'ABV';
    protected const ESCORE_REQ_REASON_BZV = 'BZV';
    protected const ESCORE_REQ_REASON_BMT = 'BMT';
    protected const ESCORE_REQ_REASON_BFT = 'BFT';
    protected const ESCORE_REQ_REASON_ABI = 'ABI';
    protected const ESCORE_REQ_REASON_ABF = 'ABF';
    protected const ESCORE_REQ_REASON_ABD = 'ABD';
    protected const ESCORE_REQ_REASON_ABW = 'ABW';
    protected const ESCORE_REQ_REASON_ABL = 'ABL';
    protected const ESCORE_REQ_REASON_BKV = 'BKV';
    protected const ESCORE_REQ_REASON_BKE = 'BKE';
    protected const ESCORE_REQ_REASON_BKA = 'BKA';
    protected const ESCORE_REQ_REASON_BBS = 'BBS';
    protected const ESCORE_REQ_REASON_BMV = 'BMV';
    protected const ESCORE_REQ_REASON_BFV = 'BFV';
    protected const ESCORE_REQ_REASON_BER = 'BER';

    protected const PARAM_PREFILL_ACC_IBAN = 'AccIban';
    protected const PARAM_PREFILL_ACC_NO = 'AccNr';
    protected const PARAM_PREFILL_ACC_OWNER = 'AccOwner';
    protected const PARAM_PREFILL_SEPA_IBAN = 'IBAN';
    protected const PARAM_PREFILL_SEPA_BIC = 'BIC';
    protected const PARAM_PREFILL_SEPA_BLZV = 'BLZV';

    protected const RETURN_STATUS_AUTHORIZED = 'AUTHORIZED';
    protected const RETURN_STATUS_OK = 'OK';
    protected const RETURN_STATUS_FAILED = 'FAILED';

    protected const RETURN_PAY_GUARANTEE_NONE = 'NONE';
    protected const RETURN_PAY_GUARANTEE_VALIDATED = 'VALIDATED';
    protected const RETURN_PAY_GUARANTEE_FULL = 'FULL';

    protected const RETURN_EVO_AVS_STATUS_ACCEPT = 'ACCEPT';
    protected const RETURN_EVO_AVS_STATUS_DENY = 'DENY';
    protected const RETURN_EVO_AVS_STATUS_CHALLENGE = 'CHALLENGE';
    protected const RETURN_EVO_AVS_STATUS_NOSCORE = 'NOSCORE';
    protected const RETURN_EVO_AVS_STATUS_ENETFP = 'ENETFP';
    protected const RETURN_EVO_AVS_STATUS_ERROR = 'ERROR';
    protected const RETURN_EVO_AVS_STATUS_ETMOUT = 'ETMOUT';

    protected const RETURN_PARAM_MID = 'MID';
    protected const RETURN_PARAM_PAY_ID = 'PayID';
    protected const RETURN_PARAM_XID = 'XID';
    protected const RETURN_PARAM_TRANS_ID = 'TransID';
    protected const RETURN_PARAM_STATUS = 'Status';
    protected const RETURN_PARAM_DESCRIPTION = 'Description';
    protected const RETURN_PARAM_CODE = 'Code';
    protected const RETURN_PARAM_REF_NO = 'RefNr';
    protected const RETURN_PARAM_USER_DATA = 'UserData';
    protected const RETURN_PARAM_TYPE = 'Type';
    protected const RETURN_PARAM_PAY_PURPOSE = 'PaymentPurpose';
    protected const RETURN_PARAM_PAY_GUARANTEE = 'PaymentGuarantee';
    protected const RETURN_PARAM_ERROR_TEXT = 'ErrorText';
    protected const RETURN_PARAM_PSEUDO_CARD_NO = 'PCNr';
    protected const RETURN_PARAM_CARD_BRAND = 'CCBrand';
    protected const RETURN_PARAM_CARD_EXPIRY = 'CCExpiry';
    protected const RETURN_PARAM_ZONE = 'Zone';
    protected const RETURN_PARAM_IP_ZONE = 'IPZone';
    protected const RETURN_PARAM_IP_ZONE_A2 = 'IPZoneA2';
    protected const RETURN_PARAM_IP_STATE = 'IPState';
    protected const RETURN_PARAM_IP_CITY = 'IPCity';
    protected const RETURN_PARAM_IP_LONG = 'IPLongitude';
    protected const RETURN_PARAM_IP_LAT = 'IPLatitude';
    protected const RETURN_PARAM_ACQ_WIRECARD_TRANS_ID = 'GuWID';
    protected const RETURN_PARAM_EVO_AVS_STATUS = 'fsStatus';
    protected const RETURN_PARAM_EVO_AVS_CODE = 'fsCode';
    protected const RETURN_PARAM_ABO_ID = 'AboID';
    protected const RETURN_PARAM_PPCC_TRANS_REF_ID = 'Pnref';
    protected const RETURN_PARAM_CC_AUTH_VAL = 'CAVV';
    protected const RETURN_PARAM_SECURITY_LEVEL = 'ECI';
    protected const RETURN_PARAM_ADDR_CHECK_MATCH = 'Match';
    protected const RETURN_PARAM_ADDR_CHECK_MATCH_ADDR = 'MatchAddr';
    protected const RETURN_PARAM_ADDR_CHECK_MATCH_POST_CODE = 'MatchZIP';
    protected const RETURN_PARAM_ADDR_CHECK_MATCH_NAME = 'MatchName';
    protected const RETURN_PARAM_ADDR_CHECK_MATCH_EMAIL = 'MatcheMail';
    protected const RETURN_PARAM_ADDR_CHECK_MATCH_PHONE = 'MatchPhone';
    protected const RETURN_PARAM_PREPAY_CAPN_AMNT_AUTH = 'AmountAuth';
    protected const RETURN_PARAM_MASTER_PASS_ID = 'MasterPassID';
    protected const RETURN_PARAM_MASTER_PASS_TRANS_ID = 'TransactionID';
    protected const RETURN_PARAM_NAME = 'Name';
    protected const RETURN_PARAM_ADDR_STREET = 'AddrStreet';
    protected const RETURN_PARAM_ADDR_STREET_2 = 'AddrStreet2';
    protected const RETURN_PARAM_ADDR_STREET_3 = 'AddrStreet3';
    protected const RETURN_PARAM_ADDR_CITY = 'AddrCity';
    protected const RETURN_PARAM_ADDR_POST_CODE = 'AddrZip';
    protected const RETURN_PARAM_ADDR_STATE = 'AddrState';
    protected const RETURN_PARAM_PHONE = 'Phone';
    protected const RETURN_PARAM_ADDR_COUNTRY_CODE = 'AddrCountryCode';
    protected const RETURN_PARAM_BILL_NAME = 'BillingName';
    protected const RETURN_PARAM_BILL_ADDR_STREET = 'BillingAddrStreet';
    protected const RETURN_PARAM_BILL_ADDR_STREET_2 = 'BillingAddrStreet2';
    protected const RETURN_PARAM_BILL_ADDR_STREET_3 = 'BillingAddrStreet3';
    protected const RETURN_PARAM_BILL_ADDR_CITY = 'BillingAddrCity';
    protected const RETURN_PARAM_BILL_ADDR_POST_CODE = 'BillingAddrZip';
    protected const RETURN_PARAM_BILL_ADDR_STATE = 'BillingAddrState';
    protected const RETURN_PARAM_BILL_ADDR_COUNTRY_CODE = 'BillingAddrCountryCode';
    protected const RETURN_PARAM_CONTACT_FIRST_NAME = 'ConFirstName';
    protected const RETURN_PARAM_CONTACT_LAST_NAME = 'ConLastName';
    protected const RETURN_PARAM_CONTACT_GENDER = 'ConGender';
    protected const RETURN_PARAM_CONTACT_COUNTRY = 'ConCountry';
    protected const RETURN_PARAM_CONTACT_EMAIL = 'ConEMail';
    protected const RETURN_PARAM_CONTACT_PHONE = 'ConPhone';
    protected const RETURN_PARAM_CONTACT_DATE_OF_BIRTH = 'ConDateOfBirth';
    protected const RETURN_PARAM_REWARD_NUMBER = 'RewNumber';
    protected const RETURN_PARAM_REWARD_ID = 'RewID';
    protected const RETURN_PARAM_REWARD_EXPIRY = 'RewExpiry';
    protected const RETURN_PARAM_ORDER_ID = 'OrderID';
    protected const RETURN_PARAM_PP_EMAIL = 'E-mail';
    protected const RETURN_PARAM_PP_INFOTEXT = 'Infotext';
    protected const RETURN_PARAM_CODE_EXT = 'CodeExt';
    protected const RETURN_PARAM_YAPITAL_QR_URL = 'QRCodeURL';
    protected const RETURN_PARAM_YAPITAL_QR_VAL = 'QRCodeValue';
    protected const RETURN_PARAM_YAPITAL_TRANS_ID_LIST = 'TransactionIDList';
    protected const RETURN_PARAM_IBAN = 'IBAN';
    protected const RETURN_PARAM_MANDATE_ID = 'MandateID';
    protected const RETURN_PARAM_DEBIT_SEQ_TYPE = 'MdtSeqType';
    protected const RETURN_PARAM_DATE_OF_SIGNATURE = 'DtOfSgntr';
    protected const RETURN_PARAM_PBAN = 'PBAN';
    protected const RETURN_PARAM_BIC = 'BIC';
    protected const RETURN_PARAM_ACC_OWNER = 'AccOwner';
    protected const RETURN_PARAM_ACC_BANK = 'AccBank';
    protected const RETURN_PARAM_MANDATE_NAME = 'MandateName';
    protected const RETURN_PARAM_FULL_DESCRIPTION = 'FullDescription';
    protected const RETURN_PARAM_MOBILE_NET = 'MobileNet';
    protected const RETURN_PARAM_MOBILE_NO = 'MobileNo';
    protected const RETURN_PARAM_AVMATCH = 'AVMatch';
    protected const RETURN_PARAM_NOBILITY_TITLE = 'NobilityTitle';
    protected const RETURN_PARAM_GENDER = 'Gender';
    protected const RETURN_PARAM_REMAINING_MILES = 'Remaining_Miles';
    protected const RETURN_PARAM_OVERDRAFT_CREDIT = 'Overdraftcredit';
    protected const RETURN_PARAM_BIC_LIST = 'BICList';
    protected const RETURN_PARAM_IDEAL_ISSUER_LIST = 'IdealIssuerList';
    protected const RETURN_PARAM_TID = 'TID';
    protected const RETURN_PARAM_ACC_DESCRIPTOR = 'AccDescriptor';
    protected const RETURN_PARAM_USER_ID = 'UserId';
    protected const RETURN_PARAM_AMOUNT_AUTH = 'AmountAuth';
    protected const RETURN_PARAM_AMOUNT_CAO = 'AmountCao';
    protected const RETURN_PARAM_AMOUNT_CRED = 'AmountCred';
    protected const RETURN_PARAM_SEC_CRITERIA = 'SecCriteria';
    protected const RETURN_PARAM_BIRTHDAY = 'Birthday';
    protected const RETURN_PARAM_AGE = 'Age';
    protected const RETURN_PARAM_ACSXID = 'ACSXID';
    protected const RETURN_PARAM_ACS_URL = 'ACSURL';
    protected const RETURN_PARAM_PA_REQ = 'PaReq';
    protected const RETURN_PARAM_TERM_URL = 'TermURL';
    protected const RETURN_PARAM_PAY_SLIP_LINK = 'PaymentSlipLink';
    protected const RETURN_PARAM_BP_TRANS_ID = 'BpTransactionID';
    protected const RETURN_PARAM_BP_STATUS = 'BpStatus';
    protected const RETURN_PARAM_STREET = 'Street';
    protected const RETURN_PARAM_SREET_NO = 'StreetNr';
    protected const RETURN_PARAM_POST_CODE = 'Zip';
    protected const RETURN_PARAM_CITY = 'City';
    protected const RETURN_PARAM_COUNTRY = 'Country';
    protected const RETURN_PARAM_BP_ACC_OWNER = 'BpAccOwner';
    protected const RETURN_PARAM_BP_BANK = 'BpBank';
    protected const RETURN_PARAM_BP_PAY_TYPE_1 = 'BpPayType1';
    protected const RETURN_PARAM_BP_PAY_TYPE_2 = 'BpPayType2';
    protected const RETURN_PARAM_BP_PAY_TYPE_3 = 'BpPayType3';
    protected const RETURN_PARAM_BP_B2B = 'BpB2B';
    protected const RETURN_PARAM_BP_LIMIT_1 = 'BpLimit1';
    protected const RETURN_PARAM_BP_LIMIT_2 = 'BpLimit2';
    protected const RETURN_PARAM_BP_LIMIT_3 = 'BpLimit3';
    protected const RETURN_PARAM_BP_LIMIT_B2B = 'BpLimitB2B';
    protected const RETURN_PARAM_BP_MIN_AMOUNT_1 = 'BpMinAmount1';
    protected const RETURN_PARAM_BP_MIN_AMOUNT_2 = 'BpMinAmount2';
    protected const RETURN_PARAM_BP_MIN_AMOUNT_3 = 'BpMinAmount3';
    protected const RETURN_PARAM_BP_MIN_AMOUNT_B2B = 'BpMinAmountB2B';
    protected const RETURN_PARAM_BP_TERMS = 'BpTerms';
    protected const RETURN_PARAM_BP_CONDITIONS_LIST = 'BpConditionsList';
    protected const RETURN_PARAM_BP_PAYMENT_PLAN = 'BpPaymentPlan';
    protected const RETURN_PARAM_BP_LINK_1 = 'BpLink1';
    protected const RETURN_PARAM_BP_LINK_2 = 'BpLink2';
    protected const RETURN_PARAM_BP_LINK_3 = 'BpLink3';
    protected const RETURN_PARAM_ERROR_TEXT_1 = 'ErrorText1';
    protected const RETURN_PARAM_ERROR_TEXT_2 = 'ErrorText2';
    protected const RETURN_PARAM_ERROR_TEXT_3 = 'ErrorText3';
    protected const RETURN_PARAM_VALIDATION_ERRORS = 'ValidationErrors';
    protected const RETURN_PARAM_CAMPAIGN = 'Campaign';
    protected const RETURN_PARAM_PRE_PAYMENT = 'PrePayment';
    protected const RETURN_PARAM_BS_PAYMENT_INSTRUCTION = 'PaymentInstruction';
    protected const RETURN_PARAM_BS_PAYOUT_LIST = 'PayoutList';
    protected const RETURN_PARAM_BS_RETURN_LIST = 'ReturnList';
    protected const RETURN_PARAM_BS_AGREED_HANDLING_CHARGES = 'AgreedHandlingCharges';
    protected const RETURN_PARAM_RESERVATION_NO = 'RNo';
    protected const RETURN_PARAM_INSTALLMENT = 'Installment';
    protected const RETURN_PARAM_PREPEAYMENT = 'Prepayment';
    protected const RETURN_PARAM_INVOICE = 'Invoice';
    protected const RETURN_PARAM_ELV = 'ELV';
    protected const RETURN_PARAM_RP_TRANS_ID = 'RPTransID';
    protected const RETURN_PARAM_DESCRIPTOR = 'Descriptor';
    protected const RETURN_PARAM_MIN_RATE = 'MinRate';
    protected const RETURN_PARAM_DEFAULT_RATE = 'DefaultRate';
    protected const RETURN_PARAM_MAX_INTEREST_RATE = 'MaxInterestRate';
    protected const RETURN_PARAM_MIN_MONTH = 'MinMonth';
    protected const RETURN_PARAM_MAX_MONTH = 'MaxMonth';
    protected const RETURN_PARAM_MONTH_LONGRUN = 'MonthLongrun';
    protected const RETURN_PARAM_MONTH_ALLOWED = 'MonthAllowed';
    protected const RETURN_PARAM_FIRST_DAY = 'FirstDay';
    protected const RETURN_PARAM_LAST_RATE = 'LastRate';
    protected const RETURN_PARAM_MIN_RATE_NORMAL = 'MinRateNormal';
    protected const RETURN_PARAM_MIN_RATE_LONGRUN = 'MinRateLongrun';
    protected const RETURN_PARAM_SERVICE_CHARGE = 'ServiceCharge';
    protected const RETURN_PARAM_INVOICE_STATUS = 'InvoiceStatus';
    protected const RETURN_PARAM_INVOICE_DECLINE_TYPE = 'InvoiceDeclineType';
    protected const RETURN_PARAM_INVOICE_DECLINE_COMMUNICATED = 'InvoiceDeclineCommunicated';
    protected const RETURN_PARAM_SDD_STATUS = 'SDDStatus';
    protected const RETURN_PARAM_SDD_DECLINE_TYPE = 'SDDDeclineType';
    protected const RETURN_PARAM_SDD_DECLINE_COMMUNICATED = 'SDDDeclineCommunicated';
    protected const RETURN_PARAM_MARKETPLACE_ID = 'MarketplaceID';
    protected const RETURN_PARAM_EXPIRY = 'Expiry';
    protected const RETURN_PARAM_CONTRACT_STATE = 'ContractState';
    protected const RETURN_PARAM_LAST_STATUS = 'Laststatus';
    protected const RETURN_PARAM_AMOUNT_CAP = 'AmountCap';
    protected const RETURN_PARAM_NOTIFICATION_TYPE = 'NotificationType';
    protected const RETURN_PARAM_BUYER_NAME = 'BuyerName';
    protected const RETURN_PARAM_BUYER_MAIL = 'BuyerMail';
    protected const RETURN_PARAM_TRANSACTION_ID = 'Transaction_ID';
    protected const RETURN_PARAM_TRANSACTION_STATUS = 'Transaction_Status';
    protected const RETURN_PARAM_BOOKED_MILES = 'BookedMiles';
    protected const RETURN_PARAM_TRACE_NO = 'TraceNo';
    protected const RETURN_PARAM_VOUCHER_NO = 'VoucherNo';
    protected const RETURN_PARAM_PIN = 'Pin';
    protected const RETURN_PARAM_SER_NO = 'SerNo';
    protected const RETURN_PARAM_VALID = 'Valid';
    protected const RETURN_PARAM_HOTLINE = 'Hotline';
    protected const RETURN_PARAM_SERVICE = 'Service';
    protected const RETURN_PARAM_AID = 'Aid';
    protected const RETURN_PARAM_QUOTE_ID = 'QuoteID';
    protected const RETURN_PARAM_BASE_CURRENCY = 'BaseCurrency';
    protected const RETURN_PARAM_LOCAL_CURRENCY = 'LocalCurrency';
    protected const RETURN_PARAM_RECIPIENT = 'Recipient';
    protected const RETURN_PARAM_ROUND_METHOD = 'RoundMethod';
    protected const RETURN_PARAM_EXPIRE_DATE = 'ExpireDate';
    protected const RETURN_PARAM_QUOTE_TYPE = 'QuoteType';
    protected const RETURN_PARAM_REFERENCE = 'Reference';
    protected const RETURN_PARAM_REF_NUMBER = 'RefNr';
    protected const RETURN_PARAM_RNO = 'RNo';
    protected const RETURN_PARAM_INVNO = 'InvNo';
    protected const RETURN_PARAM_PARTIAL_RESULTS = 'PartialResults';
    protected const RETURN_PARAM_UNIT_ID = 'UnitID';
    protected const RETURN_PARAM_ARCHIVE_ID = 'ArchiveID';
    protected const RETURN_PARAM_EDD_STATUS = 'EddStatus';
    protected const RETURN_PARAM_EDD_CODE = 'EddCode';
    protected const RETURN_PARAM_EDD_DESCRIPTION = 'EddDescription';
    protected const RETURN_PARAM_CC_STATUS = 'CcStatus';
    protected const RETURN_PARAM_CC_CODE = 'CcCode';
    protected const RETURN_PARAM_CC_DESCRIPTION = 'CcDescription';
    protected const RETURN_PARAM_CNF = 'CNF';
    protected const RETURN_PARAM_FEATURE = 'Feature';
    protected const RETURN_PARAM_ADDRESS_FEATURE = 'AddressFeature';
    protected const RETURN_PARAM_RESULT = 'Result';
    protected const RETURN_PARAM_ESCORE_CLASS = 'eScoreClass';
    protected const RETURN_PARAM_ESCORE_VALUE = 'eScoreValue';
    protected const RETURN_PARAM_INFORMA_SCORE_VALUE = 'InformaScoreValue';
    protected const RETURN_PARAM_FEATURE_DATE = 'FeatureDate';
    protected const RETURN_PARAM_DOC_REFERENCE_OF_FEATURE = 'DocReferenceOfFeature';
    protected const RETURN_PARAM_FEHLER_HINWEIS_TEXT = 'FehlerHinweisText';
    protected const RETURN_PARAM_FEHLER_ZUSATZTEXT = 'FehlerZusatztext';
    protected const RETURN_PARAM_ASNR = 'ASNR';
    protected const RETURN_PARAM_ANF_NR = 'ANF_NR';
    protected const RETURN_PARAM_DATE_OF_BIRTH = 'DateOfBirth';
    protected const RETURN_PARAM_FIRST_NAME = 'FirstName';
    protected const RETURN_PARAM_LAST_NAME = 'LastName';
    protected const RETURN_PARAM_ANSCHRIFT_HERKUNFT = 'AnschrHerkunft';
    protected const RETURN_PARAM_ANSCHRIFT_HERKUNFT_TEXT = 'AnschrHerkunftText';
    protected const RETURN_PARAM_SCORE_WERT = 'ScoreWert';
    protected const RETURN_PARAM_HINWEIS_TEXT = 'HinweisText';
    protected const RETURN_PARAM_NEG_ART_CODE = 'NegArtCode';
    protected const RETURN_PARAM_NEG_ART_TEXT = 'NegArtText';
    protected const RETURN_PARAM_NEG_ART_ANZAHL = 'NegArtAnz';
    protected const RETURN_PARAM_NEG_BETRAG = 'NegBetrag';
    protected const RETURN_PARAM_NEG_BETRAG_KKZ = 'NegBetragWkz';
    protected const RETURN_PARAM_NEG_DATUM = 'NegDatum';
    protected const RETURN_PARAM_NEG_AKTENZEICHEN = 'NegAktenzeichen';
    protected const RETURN_PARAM_NEG_PLZ_AMTSGERICHT = 'NegPLZAmtsgericht';
    protected const RETURN_PARAM_NEG_ORT_AMTSGERICHT = 'NegOrtAmtsgericht';
    protected const RETURN_PARAM_VH_NAME = 'VhName';
    protected const RETURN_PARAM_VH_PLZ = 'VhPLZ';
    protected const RETURN_PARAM_VH_ORT = 'VhOrt';
    protected const RETURN_PARAM_VH_STAAT = 'VhStaat';
    protected const RETURN_PARAM_VH_STAAT_TEXT = 'VhStaatText';
    protected const RETURN_PARAM_QUOTE_VALUE = 'QuoteValue';

    protected const LAYOUT_PARAM_TEMPLATE = 'Template';
    protected const LAYOUT_PARAM_BACKGROUND = 'Background';
    protected const LAYOUT_PARAM_BGCOLOR = 'BGColor';
    protected const LAYOUT_PARAM_BGIMAGE = 'BGImage';
    protected const LAYOUT_PARAM_FCOLOR = 'FColor';
    protected const LAYOUT_PARAM_FFACE = 'FFace';
    protected const LAYOUT_PARAM_FSIZE = 'FSize';
    protected const LAYOUT_PARAM_LANGUAGE = 'Language';
    protected const LAYOUT_PARAM_CCSELECT = 'CCSelect';
    protected const LAYOUT_PARAM_URLBACK = 'URLBack';
    protected const LAYOUT_PARAM_CENTER = 'Center';
    protected const LAYOUT_PARAM_TWIDTH = 'tWidth';
    protected const LAYOUT_PARAM_THEIGHT = 'tHeight';
    protected const LAYOUT_PARAM_URL_IMG_BACK = 'Urlimgback';
    protected const LAYOUT_PARAM_URL_IMG_SUBMIT = 'Urlimgsubmit';
    protected const LAYOUT_PARAM_OTF_TEXT = 'OTFText';
    protected const LAYOUT_PARAM_LABEL_1 = 'Label1';
    protected const LAYOUT_PARAM_TEXT_1 = 'Text1';
    protected const LAYOUT_PARAM_LABEL_2 = 'Label2';
    protected const LAYOUT_PARAM_TEXT_2 = 'Text2';
    protected const LAYOUT_PARAM_LABEL_3 = 'Label3';
    protected const LAYOUT_PARAM_TEXT_3 = 'Text3';
    protected const LAYOUT_PARAM_LABEL_4 = 'Label4';
    protected const LAYOUT_PARAM_TEXT_4 = 'Text4';
    protected const LAYOUT_PARAM_LABEL_5 = 'Label5';
    protected const LAYOUT_PARAM_TEXT_5 = 'Text5';


    protected $merchantID;
    protected $computopPassword;
    protected $dataCharLength;
    protected $crypt;
    protected $URLMaker;
    protected $defaultPayTypeMapping = array(
        1 => self::PAY_TYPE_BILLPAY_DEBIT,
        2 => self::PAY_TYPE_CREDIT_CARD_DIRECT,
        3 => self::PAY_TYPE_CREDIT_CARD_DELAYED,
        4 => self::PAY_TYPE_PAYPAL_DIRECT,
        5 => self::PAY_TYPE_PAYPAL_DELAYED,
        6 => self::PAY_TYPE_BILLPAY_INVOICE,
        7 => self::PAY_TYPE_BILLPAY_DEBIT,
        8 => self::PAY_TYPE_BILLPAY_DEFERRED,
        9 => self::PAY_TYPE_GIROPAY,
        10 => self::PAY_TYPE_SOFORTUEBERWEISUNG
    );

    /**
     * ComputopPaygateWrapper constructor.
     * @param $merchantID
     * @param $computopPassword
     * @param URLMaker $URLMaker
     */
    public function __construct($merchantID, $computopPassword, URLMaker $URLMaker)
    {
        require_once rtrim(dirname(dirname(dirname(dirname(__DIR__)))),'/\\') . DIRECTORY_SEPARATOR . 'plugins/paygate/includes/function.inc.php';
        $this->merchantID = $merchantID;
        $this->computopPassword = $computopPassword;
        $this->URLMaker = $URLMaker;
        $this->crypt = new \ctBlowfish();
    }

    /**
     * @param array $inputParams
     * @param array $validateAgainst
     * @return bool
     */
    protected function _validateParameters(array &$inputParams, array $validateAgainst)
    {
        /* VALIDATE-AGAINST-ARRAY FORMATam
        array(
        self::PARAM_NAME_1=> array(
                'paramName' => self::PARAM_NAME_1,
                'paramTypeMatchCallable' => function($string){return preg_match('/^[[:alnum:]]+$/',$string);},
                'paramMaxLength' => 30,
                'paramRequired' => true
            ),
        self::PARAM_NAME_2 => array(
                'paramName' => self::PARAM_NAME_2,
                'paramTypeMatchCallable' => function($string){return preg_match('/^[[:digit:]]+$/',$string);},
                'paramMaxLength' => 30,
                'paramRequired' => true
            )
        );
        */
        $validParamsArr = array();
        foreach ($inputParams as $name => $value) {
            if (
                (
                    array_key_exists($name, $validateAgainst) ||
                    array_key_exists(rtrim($name, '0123456789') . '_MULTI', $validateAgainst)
                ) &&
                $validateAgainst[$name]['paramTypeMatchCallable']($value) &&
                mb_strlen($value, 'UTF-8') <= $validateAgainst[$name]['paramMaxLength']
            ) {
                $validParamsArr[$name] = $value;
            }
        }

        foreach ($validateAgainst as $name => $value) {
            if (!array_key_exists($name, $validParamsArr) && $value['paramRequired']) {
                return false;
            }
        }
        $inputParams = $validParamsArr;
        return true;
    }

    /**
     * @return array
     */
    public function getPayPalFormAllowedParameters()
    {
        $parameters = array(
            self::PARAM_MERCHANT_ID => array(
                'paramName' => self::PARAM_MERCHANT_ID,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/[[:alnum:]]/', $string);
                },
                'paramMaxLength' => 30,
                'paramRequired' => true
            ),
            self::PARAM_TRANS_ID => array(
                'paramName' => self::PARAM_TRANS_ID,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/[[:alnum:]]/', $string);
                },
                'paramMaxLength' => 64,
                'paramRequired' => true
            ),
            self::PARAM_REF_NO => array(
                'paramName' => self::PARAM_REF_NO,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 30,
                'paramRequired' => true
            ),
            self::PARAM_AMOUNT => array(
                'paramName' => self::PARAM_AMOUNT,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:]]+$/', $string);
                },
                'paramMaxLength' => 12,
                'paramRequired' => true
            ),
            self::PARAM_CURRENCY => array(
                'paramName' => self::PARAM_CURRENCY,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[A-Za-z]+$/', $string);
                },
                'paramMaxLength' => 3,
                'paramRequired' => true
            ),
            self::PARAM_CAPTURE => array(
                'paramName' => self::PARAM_CAPTURE,
                'paramTypeMatchCallable' => function ($string) {
                    return ($string === 'Auto' || $string === 'Manual');
                },
                'paramMaxLength' => 30,
                'paramRequired' => false
            ),
            self::PARAM_ORDER_DESC => array(
                'paramName' => self::PARAM_ORDER_DESC,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 127,
                'paramRequired' => true
            ),
            self::PARAM_ORDER_DESC_MULTI => array(
                'paramName' => self::PARAM_ORDER_DESC_MULTI,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:],\._\-]+$/', $string);
                },
                'paramMaxLength' => 2048,
                'paramRequired' => true
            ),
            self::PARAM_TAX_TOTAL => array(
                'paramName' => self::PARAM_TAX_TOTAL,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:]]+$/', $string);
                },
                'paramMaxLength' => 12,
                'paramRequired' => false
            ),
            self::PARAM_ITEM_TOTAL => array(
                'paramName' => self::PARAM_ITEM_TOTAL,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:]]+$/', $string);
                },
                'paramMaxLength' => 12,
                'paramRequired' => false
            ),
            self::PARAM_SHIP_COST => array(
                'paramName' => self::PARAM_SHIP_COST,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:]]+$/', $string);
                },
                'paramMaxLength' => 12,
                'paramRequired' => false
            ),
            self::PARAM_MAC => array(
                'paramName' => self::PARAM_MAC,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 64,
                'paramRequired' => true
            ),
            self::PARAM_URL_SUCCESS => array(
                'paramName' => self::PARAM_URL_SUCCESS,
                'paramTypeMatchCallable' => function ($string) {
                    return filter_var($string, FILTER_VALIDATE_URL);
                },
                'paramMaxLength' => 256,
                'paramRequired' => true
            ),
            self::PARAM_URL_FAILURE => array(
                'paramName' => self::PARAM_URL_FAILURE,
                'paramTypeMatchCallable' => function ($string) {
                    return filter_var($string, FILTER_VALIDATE_URL);
                },
                'paramMaxLength' => 256,
                'paramRequired' => true
            ),
            self::PARAM_URL_NOTIFY => array(
                'paramName' => self::PARAM_URL_NOTIFY,
                'paramTypeMatchCallable' => function ($string) {
                    return filter_var($string, FILTER_VALIDATE_URL);
                },
                'paramMaxLength' => 256,
                'paramRequired' => true
            ),
            self::PARAM_RESPONSE => array(
                'paramName' => self::PARAM_RESPONSE,
                'paramTypeMatchCallable' => function ($string) {
                    return ($string === 'encrypt');
                },
                'paramMaxLength' => 7,
                'paramRequired' => false
            ),
            self::PARAM_USER_DATA => array(
                'paramName' => self::PARAM_USER_DATA,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 1024,
                'paramRequired' => false
            ),
            self::PARAM_REQ_ID => array(
                'paramName' => self::PARAM_REQ_ID,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 32,
                'paramRequired' => false
            ),
            self::PARAM_ACCOUNT => array(
                'paramName' => self::PARAM_ACCOUNT,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 128,
                'paramRequired' => false
            ),
            self::PARAM_BRAND_NAME => array(
                'paramName' => self::PARAM_BRAND_NAME,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 127,
                'paramRequired' => false
            ),
            self::PARAM_BGCOLOR => array(
                'paramName' => self::PARAM_BGCOLOR,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]#]+$/', $string);
                },
                'paramMaxLength' => 7,
                'paramRequired' => false
            ),
            self::PARAM_BGIMAGE => array(
                'paramName' => self::PARAM_BGIMAGE,
                'paramTypeMatchCallable' => function ($string) {
                    return filter_var($string, FILTER_VALIDATE_URL);
                },
                'paramMaxLength' => 256,
                'paramRequired' => false
            ),
            self::PARAM_HEADER_LOGO => array(
                'paramName' => self::PARAM_HEADER_LOGO,
                'paramTypeMatchCallable' => function ($string) {
                    return filter_var($string, FILTER_VALIDATE_URL);
                },
                'paramMaxLength' => 127,
                'paramRequired' => false
            ),
            self::PARAM_LANGUAGE => array(
                'paramName' => self::PARAM_LANGUAGE,
                'paramTypeMatchCallable' => function ($string) {
                    return in_array($string, array('AU', 'DE', 'FR', 'IT', 'GB', 'ES', 'US'), true);
                },
                'paramMaxLength' => 2,
                'paramRequired' => false
            ),
            self::PARAM_FIRST_NAME => array(
                'paramName' => self::PARAM_FIRST_NAME,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-]+$/', $string);
                },
                'paramMaxLength' => 30,
                'paramRequired' => false
            ),
            self::PARAM_LAST_NAME => array(
                'paramName' => self::PARAM_LAST_NAME,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-]+$/', $string);
                },
                'paramMaxLength' => 30,
                'paramRequired' => false
            ),
            self::PARAM_ADDR_STREET => array(
                'paramName' => self::PARAM_ADDR_STREET,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\.]+$/', $string);
                },
                'paramMaxLength' => 100,
                'paramRequired' => false
            ),
            self::PARAM_ADDR_STREET_2 => array(
                'paramName' => self::PARAM_ADDR_STREET_2,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\.]+$/', $string);
                },
                'paramMaxLength' => 100,
                'paramRequired' => false
            ),
            self::PARAM_ADDR_CITY => array(
                'paramName' => self::PARAM_ADDR_CITY,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\.]+$/', $string);
                },
                'paramMaxLength' => 40,
                'paramRequired' => false
            ),
            self::PARAM_ADDR_STATE => array(
                'paramName' => self::PARAM_ADDR_STATE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\.]+$/', $string);
                },
                'paramMaxLength' => 40,
                'paramRequired' => false
            ),
            self::PARAM_ADDR_POST_CODE => array(
                'paramName' => self::PARAM_ADDR_POST_CODE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\.]+$/', $string);
                },
                'paramMaxLength' => 20,
                'paramRequired' => false
            ),
            self::PARAM_ADDR_COUNTRY_CODE => array(
                'paramName' => self::PARAM_ADDR_COUNTRY_CODE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 2,
                'paramRequired' => false
            ),
            self::PARAM_PHONE => array(
                'paramName' => self::PARAM_PHONE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^\+{0,1}[[:digit:]\\\/\-\(\)\s]+$/', $string);
                },
                'paramMaxLength' => 20,
                'paramRequired' => false
            ),
            self::PARAM_NO_SHIPPING => array(
                'paramName' => self::PARAM_NO_SHIPPING,
                'paramTypeMatchCallable' => function ($string) {
                    return in_array($string, array(0, 1), true);
                },
                'paramMaxLength' => 1,
                'paramRequired' => false
            ),
            self::PARAM_ALLOW_NOTE => array(
                'paramName' => self::PARAM_ALLOW_NOTE,
                'paramTypeMatchCallable' => function ($string) {
                    return ($string === 'no');
                },
                'paramMaxLength' => 1,
                'paramRequired' => false
            ),
            self::PARAM_NOTE => array(
                'paramName' => self::PARAM_NOTE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\r\n\t\s\-\_\.\!\?\<\>\=]+$/', $string);
                },
                'paramMaxLength' => 768,
                'paramRequired' => false
            ),
            self::PARAM_SUBJECT => array(
                'paramName' => self::PARAM_SUBJECT,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\_\.\!\?\<\>\=]+$/', $string);
                },
                'paramMaxLength' => 768,
                'paramRequired' => false
            )
        );
        return $parameters;
    }

    /**
     * @return array
     */
    public function getCreditCardPaySSLFormAllowedPaymentParameters()
    {
        $parameters = array(
            self::PARAM_MERCHANT_ID => array(
                'paramName' => self::PARAM_MERCHANT_ID,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/[[:alnum:]]/', $string);
                },
                'paramMaxLength' => 30,
                'paramRequired' => true
            ),
            self::PARAM_TRANS_ID => array(
                'paramName' => self::PARAM_TRANS_ID,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/[[:alnum:]]/', $string);
                },
                'paramMaxLength' => 64,
                'paramRequired' => true
            ),
            self::PARAM_CUSTOMER_ID => array(
                'paramName' => self::PARAM_CUSTOMER_ID,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/[[:alnum:]]/', $string);
                },
                'paramMaxLength' => 64,
                'paramRequired' => false
            ),
            self::PARAM_REF_NO => array(
                'paramName' => self::PARAM_REF_NO,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 30,
                'paramRequired' => true
            ),
            self::PARAM_AMOUNT => array(
                'paramName' => self::PARAM_AMOUNT,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:]]+$/', $string);
                },
                'paramMaxLength' => 12,
                'paramRequired' => true
            ),
            self::PARAM_CURRENCY => array(
                'paramName' => self::PARAM_REF_NO,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[A-Z]+$/', $string);
                },
                'paramMaxLength' => 3,
                'paramRequired' => true
            ),
            self::PARAM_ORDER_DESC => array(
                'paramName' => self::PARAM_ORDER_DESC,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 768,
                'paramRequired' => true
            ),
            self::PARAM_MAC => array(
                'paramName' => self::PARAM_MAC,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 64,
                'paramRequired' => true
            ),
            self::PARAM_URL_SUCCESS => array(
                'paramName' => self::PARAM_URL_SUCCESS,
                'paramTypeMatchCallable' => function ($string) {
                    return filter_var($string, FILTER_VALIDATE_URL);
                },
                'paramMaxLength' => 256,
                'paramRequired' => true
            ),
            self::PARAM_URL_FAILURE => array(
                'paramName' => self::PARAM_URL_FAILURE,
                'paramTypeMatchCallable' => function ($string) {
                    return filter_var($string, FILTER_VALIDATE_URL);
                },
                'paramMaxLength' => 256,
                'paramRequired' => true
            ),
            self::PARAM_RESPONSE => array(
                'paramName' => self::PARAM_RESPONSE,
                'paramTypeMatchCallable' => function ($string) {
                    return ($string === 'encrypt');
                },
                'paramMaxLength' => 7,
                'paramRequired' => false
            ),
            self::PARAM_URL_NOTIFY => array(
                'paramName' => self::PARAM_URL_NOTIFY,
                'paramTypeMatchCallable' => function ($string) {
                    return filter_var($string, FILTER_VALIDATE_URL);
                },
                'paramMaxLength' => 256,
                'paramRequired' => true
            ),
            self::PARAM_USER_DATA => array(
                'paramName' => self::PARAM_USER_DATA,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 1024,
                'paramRequired' => false
            ),
            self::PARAM_CAPTURE => array(
                'paramName' => self::PARAM_CAPTURE,
                'paramTypeMatchCallable' => function ($string) {
                    return ($string === 'Auto' || $string === 'AUTO' || $string === 'Manual' || $string === 'MANUAL');
                },
                'paramMaxLength' => 6,
                'paramRequired' => false
            ),
            self::PARAM_REQ_ID => array(
                'paramName' => self::PARAM_REQ_ID,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 32,
                'paramRequired' => false
            ),
            self::PARAM_EXPIRATION_TIME => array(
                'paramName' => self::PARAM_EXPIRATION_TIME,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 19,
                'paramRequired' => false
            ),
            self::PARAM_CREDIT_CARD_HOLDER => array(
                'paramName' => self::PARAM_CREDIT_CARD_HOLDER,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\-\s\.]+$/', $string);
                },
                'paramMaxLength' => 60,
                'paramRequired' => false
            ),

            self::PARAM_NAME => array(
                'paramName' => self::PARAM_NAME,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\-\s\.]+$/', $string);
                },
                'paramMaxLength' => 128,
                'paramRequired' => false
            ),
            self::PARAM_LANGUAGE => array(
                'paramName' => self::PARAM_LANGUAGE,
                'paramTypeMatchCallable' => function ($string) {
                    return in_array($string, array('AU', 'DE', 'FR', 'IT', 'GB', 'ES', 'US'), true);
                },
                'paramMaxLength' => 2,
                'paramRequired' => false
            ),
            self::PARAM_SELLING_POINT => array(
                'paramName' => self::PARAM_SELLING_POINT,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:],\._\-\s\+]+$/', $string);
                },
                'paramMaxLength' => 50,
                'paramRequired' => false
            ),
            self::PARAM_SERVICE => array(
                'paramName' => self::PARAM_SERVICE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:],\._\-\s\+]+$/', $string);
                },
                'paramMaxLength' => 50,
                'paramRequired' => false
            ),
            self::PARAM_CHANNEL => array(
                'paramName' => self::PARAM_CHANNEL,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:],\._\-\s\+]+$/', $string);
                },
                'paramMaxLength' => 64,
                'paramRequired' => false
            ),
            self::PARAM_ADDR_COUNTRY_CODE => array(
                'paramName' => self::PARAM_ADDR_COUNTRY_CODE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 2,
                'paramRequired' => false
            ),
            self::PARAM_RTF => array(
                'paramName' => self::PARAM_RTF,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 1,
                'paramRequired' => false
            ),
            self::PARAM_IP_ADDR => array(
                'paramName' => self::PARAM_IP_ADDR,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:]\.\:[a-fA-F]]+$/', $string);
                },
                'paramMaxLength' => 15,
                'paramRequired' => false
            ),
            self::PARAM_IP_ZONE => array(
                'paramName' => self::PARAM_IP_ZONE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:],]$/', $string);
                },
                'paramMaxLength' => 350,
                'paramRequired' => false
            ),
            self::PARAM_ZONE => array(
                'paramName' => self::PARAM_ZONE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:],]$/', $string);
                },
                'paramMaxLength' => 350,
                'paramRequired' => false
            ),
            self::PARAM_MARP => array(
                'paramName' => self::PARAM_MARP,
                'paramTypeMatchCallable' => function ($string) {
                    return ($string === 'yes' || $string === 'no');
                },
                'paramMaxLength' => 3,
                'paramRequired' => false
            ),
            self::PARAM_AD_DATA_1 => array(
                'paramName' => self::PARAM_AD_DATA_1,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:],\._\-\/\\]+$/', $string);
                },
                'paramMaxLength' => 28,
                'paramRequired' => false
            ),
            self::PARAM_ABO_ACTION => array(
                'paramName' => self::PARAM_ABO_ACTION,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\_\s]+$/', $string);
                },
                'paramMaxLength' => 10,
                'paramRequired' => false
            ),
            self::PARAM_START_DATE => array(
                'paramName' => self::PARAM_START_DATE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:]]{8}$/', $string);
                },
                'paramMaxLength' => 8,
                'paramRequired' => false
            ),
            self::PARAM_END_DATE => array(
                'paramName' => self::PARAM_END_DATE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:]]{8}$/', $string);
                },
                'paramMaxLength' => 8,
                'paramRequired' => false
            ),
            self::PARAM_INTERVAL => array(
                'paramName' => self::PARAM_INTERVAL,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:]]{1,3}$/', $string);
                },
                'paramMaxLength' => 3,
                'paramRequired' => false
            ),
            self::PARAM_ABO_AMOUNT => array(
                'paramName' => self::PARAM_ABO_AMOUNT,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:]]{1,12}$/', $string);
                },
                'paramMaxLength' => 12,
                'paramRequired' => false
            ),
            self::PARAM_ADDR_STREET => array(
                'paramName' => self::PARAM_ADDR_STREET,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\.\-]+$/', $string);
                },
                'paramMaxLength' => 50,
                'paramRequired' => false
            ),
            self::PARAM_ADDR_STREET_NO => array(
                'paramName' => self::PARAM_ADDR_STREET_NO,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\.\-]+$/', $string);
                },
                'paramMaxLength' => 15,
                'paramRequired' => false
            ),
            self::PARAM_ADDR_POST_CODE => array(
                'paramName' => self::PARAM_ADDR_POST_CODE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 10,
                'paramRequired' => false
            ),
            self::PARAM_ADDR_CITY => array(
                'paramName' => self::PARAM_ADDR_CITY,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:],\._\-]+$/', $string);
                },
                'paramMaxLength' => 40,
                'paramRequired' => false
            ),
            self::PARAM_ADDR_STREET_2 => array(
                'paramName' => self::PARAM_ADDR_STREET_2,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\.\-]+$/', $string);
                },
                'paramMaxLength' => 35,
                'paramRequired' => false
            ),
            self::PARAM_ADDR_STREET_NO_2 => array(
                'paramName' => self::PARAM_ADDR_STREET_NO_2,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\.\-]+$/', $string);
                },
                'paramMaxLength' => 5,
                'paramRequired' => false
            ),
            self::PARAM_ADDR_POST_CODE_2 => array(
                'paramName' => self::PARAM_ADDR_POST_CODE_2,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 5,
                'paramRequired' => false
            ),
            self::PARAM_ADDR_CITY_2 => array(
                'paramName' => self::PARAM_ADDR_CITY_2,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:],\._\-]+$/', $string);
                },
                'paramMaxLength' => 32,
                'paramRequired' => false
            ),
            self::PARAM_ADDR_CHOICE => array(
                'paramName' => self::PARAM_ADDR_CHOICE,
                'paramTypeMatchCallable' => function ($string) {
                    return ((int)$string === 1 || (int)$string === 2);
                },
                'paramMaxLength' => 1,
                'paramRequired' => false
            ),
            self::PARAM_FIRST_NAME => array(
                'paramName' => self::PARAM_FIRST_NAME,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-]+$/', $string);
                },
                'paramMaxLength' => 128,
                'paramRequired' => false
            ),
            self::PARAM_LAST_NAME => array(
                'paramName' => self::PARAM_LAST_NAME,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-]+$/', $string);
                },
                'paramMaxLength' => 128,
                'paramRequired' => false
            ),
            self::PARAM_EMAIL => array(
                'paramName' => self::PARAM_EMAIL,
                'paramTypeMatchCallable' => function ($string) {
                    return filter_var($string, FILTER_VALIDATE_EMAIL);
                },
                'paramMaxLength' => 128,
                'paramRequired' => false
            ),
            self::PARAM_PHONE => array(
                'paramName' => self::PARAM_PHONE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^\+{0,1}[[:digit:]\\\/\-\(\)\s]+$/', $string);
                },
                'paramMaxLength' => 40,
                'paramRequired' => false
            ),
            self::PARAM_SHIP_FIRST_NAME => array(
                'paramName' => self::PARAM_SHIP_FIRST_NAME,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-]+$/', $string);
                },
                'paramMaxLength' => 128,
                'paramRequired' => false
            ),
            self::PARAM_SHIP_LAST_NAME => array(
                'paramName' => self::PARAM_SHIP_LAST_NAME,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-]+$/', $string);
                },
                'paramMaxLength' => 128,
                'paramRequired' => false
            ),
            self::PARAM_SHIP_STREET => array(
                'paramName' => self::PARAM_SHIP_STREET,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\.]+$/', $string);
                },
                'paramMaxLength' => 46,
                'paramRequired' => false
            ),
            self::PARAM_SHIP_CITY => array(
                'paramName' => self::PARAM_SHIP_CITY,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\.]+$/', $string);
                },
                'paramMaxLength' => 40,
                'paramRequired' => false
            ),
            self::PARAM_SHIP_POST_CODE => array(
                'paramName' => self::PARAM_SHIP_POST_CODE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]{2,8}$/', $string);
                },
                'paramMaxLength' => 8,
                'paramRequired' => false
            ),
            self::PARAM_SHIP_EMAIL => array(
                'paramName' => self::PARAM_SHIP_EMAIL,
                'paramTypeMatchCallable' => function ($string) {
                    return filter_var($string, FILTER_VALIDATE_EMAIL);
                },
                'paramMaxLength' => 128,
                'paramRequired' => false
            ),
            self::PARAM_SHIP_PHONE => array(
                'paramName' => self::PARAM_SHIP_PHONE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^\+{0,1}[[:digit:]\\\/\-\(\)\s]+$/', $string);
                },
                'paramMaxLength' => 40,
                'paramRequired' => false
            ),
            self::PARAM_SHIP_COUNTRY_CODE => array(
                'paramName' => self::PARAM_SHIP_COUNTRY_CODE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]{3}$/', $string);
                },
                'paramMaxLength' => 3,
                'paramRequired' => false
            ),
            self::PARAM_CREDIT_CARD_HOLDER => array(
                'paramName' => self::PARAM_CREDIT_CARD_HOLDER,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-]+$/', $string);
                },
                'paramMaxLength' => 60,
                'paramRequired' => false
            ),
            self::PARAM_MIDDLE_NAME => array(
                'paramName' => self::PARAM_MIDDLE_NAME,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\.]+$/', $string);
                },
                'paramMaxLength' => 128,
                'paramRequired' => false
            ),
            self::PARAM_SALUTATION => array(
                'paramName' => self::PARAM_SALUTATION,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\.]+$/', $string);
                },
                'paramMaxLength' => 10,
                'paramRequired' => false
            ),
            self::PARAM_TITLE => array(
                'paramName' => self::PARAM_TITLE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\.]+$/', $string);
                },
                'paramMaxLength' => 20,
                'paramRequired' => false
            ),
            self::PARAM_COMPANY_OR_PERSON => array(
                'paramName' => self::PARAM_COMPANY_OR_PERSON,
                'paramTypeMatchCallable' => function ($string) {
                    return ($string === 'F' || $string === 'f' || $string === 'P' || $string === 'p');
                },
                'paramMaxLength' => 1,
                'paramRequired' => false
            ),
            self::PARAM_DATE_OF_BIRTH => array(
                'paramName' => self::PARAM_DATE_OF_BIRTH,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:]]{8}$/', $string);
                },
                'paramMaxLength' => 8,
                'paramRequired' => false
            ),
            self::PARAM_GENDER => array(
                'paramName' => self::PARAM_GENDER,
                'paramTypeMatchCallable' => function ($string) {
                    return ($string === 'F' || $string === 'f' || $string === 'M' || $string === 'm' || $string === 'W' || $string === 'w');
                },
                'paramMaxLength' => 1,
                'paramRequired' => false
            ),
            self::PARAM_ADDR_ADD => array(
                'paramName' => self::PARAM_ADDR_ADD,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\.\-]+$/', $string);
                },
                'paramMaxLength' => 60,
                'paramRequired' => false
            ),
            self::PARAM_ADDR_COUNTRY_CODE => array(
                'paramName' => self::PARAM_ADDR_COUNTRY_CODE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]{2}$/', $string);
                },
                'paramMaxLength' => 2,
                'paramRequired' => false
            ),
            self::PARAM_ADDR_STATE => array(
                'paramName' => self::PARAM_ADDR_STATE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\.\-]+$/', $string);
                },
                'paramMaxLength' => 40,
                'paramRequired' => false
            ),
            self::PARAM_ADDR_DISTRICT => array(
                'paramName' => self::PARAM_ADDR_DISTRICT,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\.\-]+$/', $string);
                },
                'paramMaxLength' => 40,
                'paramRequired' => false
            ),
            self::PARAM_ADDR_POBOX => array(
                'paramName' => self::PARAM_ADDR_POBOX,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\_]+$/', $string);
                },
                'paramMaxLength' => 35,
                'paramRequired' => false
            ),
            self::PARAM_WORK_PHONE => array(
                'paramName' => self::PARAM_WORK_PHONE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^\+{0,1}[[:digit:]\\\/\-\(\)\s]+$/', $string);
                },
                'paramMaxLength' => 20,
                'paramRequired' => false
            ),
            self::PARAM_FAX => array(
                'paramName' => self::PARAM_FAX,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^\+{0,1}[[:digit:]\s\/\(\)]+$/', $string);
                },
                'paramMaxLength' => 20,
                'paramRequired' => false
            ),
            self::PARAM_EMAIL_EVO => array(
                'paramName' => self::PARAM_EMAIL_EVO,
                'paramTypeMatchCallable' => function ($string) {
                    return filter_var($string, FILTER_VALIDATE_EMAIL);
                },
                'paramMaxLength' => 80,
                'paramRequired' => false
            ),
            self::PARAM_NEW_CUSTOMER => array(
                'paramName' => self::PARAM_NEW_CUSTOMER,
                'paramTypeMatchCallable' => function ($string) {
                    return ($string === 'Yes' || $string === 'No');
                },
                'paramMaxLength' => 3,
                'paramRequired' => false
            ),
            self::PARAM_DATE_OF_REGISTRATION => array(
                'paramName' => self::PARAM_DATE_OF_REGISTRATION,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:]]{8}$/', $string);
                },
                'paramMaxLength' => 8,
                'paramRequired' => false
            ),
            self::PARAM_SOCIAL_SECURITY_NO => array(
                'paramName' => self::PARAM_SOCIAL_SECURITY_NO,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\_]+$/', $string);
                },
                'paramMaxLength' => 9,
                'paramRequired' => false
            ),
            self::PARAM_DRIVING_LICENSE_NO => array(
                'paramName' => self::PARAM_DRIVING_LICENSE_NO,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\_]+$/', $string);
                },
                'paramMaxLength' => 37,
                'paramRequired' => false
            ),
            self::PARAM_CHARGE_DESCRIPTION => array(
                'paramName' => self::PARAM_CHARGE_DESCRIPTION,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\_]+$/', $string);
                },
                'paramMaxLength' => 12,
                'paramRequired' => false
            ),
            self::PARAM_BILL_FIRST_NAME => array(
                'paramName' => self::PARAM_BILL_FIRST_NAME,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\_\.]+$/', $string);
                },
                'paramMaxLength' => 30,
                'paramRequired' => false
            ),
            self::PARAM_BILL_MIDDLE_NAME => array(
                'paramName' => self::PARAM_BILL_MIDDLE_NAME,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\_\.]+$/', $string);
                },
                'paramMaxLength' => 30,
                'paramRequired' => false
            ),
            self::PARAM_BILL_LAST_NAME => array(
                'paramName' => self::PARAM_BILL_LAST_NAME,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\_\.]+$/', $string);
                },
                'paramMaxLength' => 30,
                'paramRequired' => false
            ),
            self::PARAM_BILL_STREET => array(
                'paramName' => self::PARAM_BILL_STREET,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\_\.]+$/', $string);
                },
                'paramMaxLength' => 30,
                'paramRequired' => false
            ),
            self::PARAM_BILL_STREET_NO => array(
                'paramName' => self::PARAM_BILL_STREET_NO,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\_\.]+$/', $string);
                },
                'paramMaxLength' => 10,
                'paramRequired' => false
            ),
            self::PARAM_BILL_STREET_2 => array(
                'paramName' => self::PARAM_BILL_STREET_2,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\_\.]+$/', $string);
                },
                'paramMaxLength' => 30,
                'paramRequired' => false
            ),
            self::PARAM_BILL_POST_CODE => array(
                'paramName' => self::PARAM_BILL_POST_CODE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\_\.]+$/', $string);
                },
                'paramMaxLength' => 10,
                'paramRequired' => false
            ),
            self::PARAM_BILL_CITY => array(
                'paramName' => self::PARAM_BILL_CITY,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\_\.]+$/', $string);
                },
                'paramMaxLength' => 20,
                'paramRequired' => false
            ),
            self::PARAM_BILL_STATE => array(
                'paramName' => self::PARAM_BILL_STATE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\_\.]+$/', $string);
                },
                'paramMaxLength' => 2,
                'paramRequired' => false
            ),
            self::PARAM_BILL_COUNTRY_CODE => array(
                'paramName' => self::PARAM_BILL_COUNTRY_CODE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\_\.]+$/', $string);
                },
                'paramMaxLength' => 3,
                'paramRequired' => false
            ),
            self::PARAM_BILL_PHONE => array(
                'paramName' => self::PARAM_BILL_PHONE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^\+{0,1}[[:digit:]\\\/\-\(\)\s]+$/', $string);
                },
                'paramMaxLength' => 20,
                'paramRequired' => false
            ),
            self::PARAM_BILL_WORK_PHONE => array(
                'paramName' => self::PARAM_BILL_WORK_PHONE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^\+{0,1}[[:digit:]\\\/\-\(\)\s]+$/', $string);
                },
                'paramMaxLength' => 20,
                'paramRequired' => false
            ),
            self::PARAM_BILL_FAX => array(
                'paramName' => self::PARAM_BILL_FAX,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^\+{0,1}[[:digit:]\\\/\-\(\)\s]+$/', $string);
                },
                'paramMaxLength' => 20,
                'paramRequired' => false
            ),
            self::PARAM_BILL_EMAIL => array(
                'paramName' => self::PARAM_BILL_EMAIL,
                'paramTypeMatchCallable' => function ($string) {
                    return filter_var($string, FILTER_VALIDATE_EMAIL);
                },
                'paramMaxLength' => 60,
                'paramRequired' => false
            ),
            self::PARAM_SHIP_MIDDLE_NAME => array(
                'paramName' => self::PARAM_SHIP_MIDDLE_NAME,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\.]+$/', $string);
                },
                'paramMaxLength' => 30,
                'paramRequired' => false
            ),
            self::PARAM_SHIP_STREET_NO => array(
                'paramName' => self::PARAM_SHIP_STREET_NO,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\.]+$/', $string);
                },
                'paramMaxLength' => 10,
                'paramRequired' => false
            ),
            self::PARAM_SHIP_STREET_2 => array(
                'paramName' => self::PARAM_SHIP_STREET_2,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\.]+$/', $string);
                },
                'paramMaxLength' => 30,
                'paramRequired' => false
            ),
            self::PARAM_SHIP_POST_CODE_PAYPALCC => array(
                'paramName' => self::PARAM_SHIP_POST_CODE_PAYPALCC,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\.]+$/', $string);
                },
                'paramMaxLength' => 10,
                'paramRequired' => false
            ),
            self::PARAM_SHIP_STATE => array(
                'paramName' => self::PARAM_SHIP_STATE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\_\.]+$/', $string);
                },
                'paramMaxLength' => 2,
                'paramRequired' => false
            ),
            self::PARAM_SHIP_WORK_PHONE => array(
                'paramName' => self::PARAM_SHIP_WORK_PHONE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^\+{0,1}[[:digit:]\\\/\-\(\)\s]+$/', $string);
                },
                'paramMaxLength' => 20,
                'paramRequired' => false
            ),
            self::PARAM_QUOTE_ID => array(
                'paramName' => self::PARAM_QUOTE_ID,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:],]+$/', $string);
                },
                'paramMaxLength' => 10,
                'paramRequired' => false
            ),
            self::PARAM_BASE_AMOUNT => array(
                'paramName' => self::PARAM_BASE_AMOUNT,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:]]+$/', $string);
                },
                'paramMaxLength' => 12,
                'paramRequired' => false
            ),
            self::PARAM_LOCAL_AMOUNT => array(
                'paramName' => self::PARAM_LOCAL_AMOUNT,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:]]+$/', $string);
                },
                'paramMaxLength' => 12,
                'paramRequired' => false
            )
        );
        return $parameters;
    }

    /**
     * @return array
     */
    public function getCreditCardPaySSLFormAllowedLayoutParameters()
    {
        $parameters = array(
            self::LAYOUT_PARAM_TEMPLATE => array(
                'paramName' => self::LAYOUT_PARAM_TEMPLATE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\.\~\(\)\[\]_\-]+$/', $string);
                },
                'paramMaxLength' => 20,
                'paramRequired' => false
            ),
            self::LAYOUT_PARAM_BACKGROUND => array(
                'paramName' => self::LAYOUT_PARAM_BACKGROUND,
                'paramTypeMatchCallable' => function ($string) {
                    return filter_var($string, FILTER_VALIDATE_URL);
                },
                'paramMaxLength' => 256,
                'paramRequired' => false
            ),
            self::LAYOUT_PARAM_BGCOLOR => array(
                'paramName' => self::LAYOUT_PARAM_BGCOLOR,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^#{0,1}[[:digit:]a-fA-F]+$/', $string);
                },
                'paramMaxLength' => 7,
                'paramRequired' => false
            ),
            self::LAYOUT_PARAM_BGIMAGE => array(
                'paramName' => self::LAYOUT_PARAM_BGIMAGE,
                'paramTypeMatchCallable' => function ($string) {
                    return filter_var($string, FILTER_VALIDATE_URL);
                },
                'paramMaxLength' => 256,
                'paramRequired' => false
            ),
            self::LAYOUT_PARAM_FCOLOR => array(
                'paramName' => self::LAYOUT_PARAM_FCOLOR,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:]a-fA-F]+$/', $string);
                },
                'paramMaxLength' => 6,
                'paramRequired' => false
            ),
            self::LAYOUT_PARAM_FSIZE => array(
                'paramName' => self::LAYOUT_PARAM_FSIZE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:]]{1,2}$/', $string);
                },
                'paramMaxLength' => 2,
                'paramRequired' => false
            ),
            self::LAYOUT_PARAM_LANGUAGE => array(
                'paramName' => self::LAYOUT_PARAM_LANGUAGE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]{2}$/', $string);
                },
                'paramMaxLength' => 2,
                'paramRequired' => false
            ),
            self::LAYOUT_PARAM_CCSELECT => array(
                'paramName' => self::LAYOUT_PARAM_CCSELECT,
                'paramTypeMatchCallable' => function ($string) {
                    return in_array($string, array(
                        self::CCSELECT_VISA,
                        self::CCSELECT_MASTERCARD,
                        self::CCSELECT_AMEX,
                        self::CCSELECT_DINERS,
                        self::CCSELECT_JCB,
                        self::CCSELECT_CBN,
                        self::CCSELECT_SWITCH,
                        self::CCSELECT_SOLO,
                        self::CCSELECT_HIPERCARD,
                        self::CCSELECT_ELO,
                        self::CCSELECT_AURA,
                        self::CCSELECT_DANKORT
                    ), true);
                },
                'paramMaxLength' => 10,
                'paramRequired' => false
            ),
            self::LAYOUT_PARAM_URLBACK => array(
                'paramName' => self::LAYOUT_PARAM_URLBACK,
                'paramTypeMatchCallable' => function ($string) {
                    return filter_var($string, FILTER_VALIDATE_URL);
                },
                'paramMaxLength' => 256,
                'paramRequired' => false
            ),
            self::LAYOUT_PARAM_CENTER => array(
                'paramName' => self::LAYOUT_PARAM_CENTER,
                'paramTypeMatchCallable' => function ($string) {
                    return ((int)$string === 1);
                },
                'paramMaxLength' => 1,
                'paramRequired' => false
            ),
            self::LAYOUT_PARAM_TWIDTH => array(
                'paramName' => self::LAYOUT_PARAM_TWIDTH,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\%\.]+$/', $string);
                },
                'paramMaxLength' => 12,
                'paramRequired' => false
            ),
            self::LAYOUT_PARAM_THEIGHT => array(
                'paramName' => self::LAYOUT_PARAM_THEIGHT,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\%\.]+$/', $string);
                },
                'paramMaxLength' => 12,
                'paramRequired' => false
            )
        );
        return $parameters;
    }

    /**
     * @return array
     */
    public function getCreditCardPaySSLReturnParameters()
    {
        $parameters = array(
            self::RETURN_PARAM_MID => array(
                'paramName' => self::RETURN_PARAM_MID,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 30,
                'paramRequired' => true
            ),
            self::RETURN_PARAM_PAY_ID => array(
                'paramName' => self::RETURN_PARAM_PAY_ID,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 32,
                'paramRequired' => true
            ),
            self::RETURN_PARAM_XID => array(
                'paramName' => self::RETURN_PARAM_XID,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 64,
                'paramRequired' => true
            ),
            self::RETURN_PARAM_TRANS_ID => array(
                'paramName' => self::RETURN_PARAM_TRANS_ID,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 64,
                'paramRequired' => true
            ),
            self::RETURN_PARAM_STATUS => array(
                'paramName' => self::RETURN_PARAM_STATUS,
                'paramTypeMatchCallable' => function ($string) {
                    return (in_array($string, array(
                        self::RETURN_STATUS_AUTHORIZED,
                        self::RETURN_STATUS_OK,
                        self::RETURN_STATUS_FAILED
                    ), true));
                },
                'paramMaxLength' => 50,
                'paramRequired' => true
            ),
            self::RETURN_PARAM_TYPE => array(
                'paramName' => self::RETURN_PARAM_TYPE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 20,
                'paramRequired' => false
            ),
            self::RETURN_PARAM_DESCRIPTION => array(
                'paramName' => self::RETURN_PARAM_DESCRIPTION,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:print:]]+$/', $string);
                },
                'paramMaxLength' => 1024,
                'paramRequired' => false
            ),
            self::RETURN_PARAM_CODE => array(
                'paramName' => self::RETURN_PARAM_CODE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:]]+$/', $string);
                },
                'paramMaxLength' => 8,
                'paramRequired' => true
            ),
            self::RETURN_PARAM_USER_DATA => array(
                'paramName' => self::RETURN_PARAM_USER_DATA,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:print:]]+$/', $string);
                },
                'paramMaxLength' => 1024,
                'paramRequired' => false
            ),
            self::RETURN_PARAM_PSEUDO_CARD_NO => array(
                'paramName' => self::RETURN_PARAM_PSEUDO_CARD_NO,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:]\-\s]+$/', $string);
                },
                'paramMaxLength' => 16,
                'paramRequired' => false
            ),
            self::RETURN_PARAM_PAY_PURPOSE => array(
                'paramName' => self::RETURN_PARAM_PAY_PURPOSE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 26,
                'paramRequired' => false
            ),
            self::RETURN_PARAM_PAY_GUARANTEE => array(
                'paramName' => self::RETURN_PARAM_PAY_GUARANTEE,
                'paramTypeMatchCallable' => function ($string) {
                    return (in_array($string, array(
                        self::RETURN_PAY_GUARANTEE_NONE,
                        self::RETURN_PAY_GUARANTEE_VALIDATED,
                        self::RETURN_PAY_GUARANTEE_FULL
                    ), true));
                },
                'paramMaxLength' => 12,
                'paramRequired' => false
            ),
            self::RETURN_PARAM_ERROR_TEXT => array(
                'paramName' => self::RETURN_PARAM_ERROR_TEXT,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:print:]]+$/', $string);
                },
                'paramMaxLength' => 128,
                'paramRequired' => false
            ),
            self::RETURN_PARAM_CARD_EXPIRY => array(
                'paramName' => self::RETURN_PARAM_CARD_EXPIRY,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:]]{6}$/', $string);
                },
                'paramMaxLength' => 6,
                'paramRequired' => false
            ),
            self::RETURN_PARAM_CARD_BRAND => array(
                'paramName' => self::RETURN_PARAM_CARD_BRAND,
                'paramTypeMatchCallable' => function ($string) {
                    return (in_array($string, array(
                        self::CCBRAND_AMEX,
                        self::CCBRAND_AURA,
                        self::CCBRAND_CBN,
                        self::CCBRAND_DINERS,
                        self::CCBRAND_ELO,
                        self::CCBRAND_HIPERCARD,
                        self::CCBRAND_JCB,
                        self::CCBRAND_MAESTRO,
                        self::CCBRAND_MASTERCARD,
                        self::CCBRAND_SOLO,
                        self::CCBRAND_SWITCH,
                        self::CCBRAND_VISA
                    ), true));
                },
                'paramMaxLength' => 22,
                'paramRequired' => false
            ),
            self::RETURN_PARAM_ZONE => array(
                'paramName' => self::RETURN_PARAM_ZONE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 7,
                'paramRequired' => false
            ),
            self::RETURN_PARAM_IP_ZONE => array(
                'paramName' => self::RETURN_PARAM_IP_ZONE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 7,
                'paramRequired' => false
            ),
            self::RETURN_PARAM_IP_ZONE_A2 => array(
                'paramName' => self::RETURN_PARAM_IP_ZONE_A2,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 7,
                'paramRequired' => false
            ),
            self::RETURN_PARAM_IP_STATE => array(
                'paramName' => self::RETURN_PARAM_IP_STATE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:print:]]+$/', $string);
                },
                'paramMaxLength' => 32,
                'paramRequired' => false
            ),
            self::RETURN_PARAM_IP_CITY => array(
                'paramName' => self::RETURN_PARAM_IP_CITY,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:print:]]+$/', $string);
                },
                'paramMaxLength' => 32,
                'paramRequired' => false
            ),
            self::RETURN_PARAM_IP_LONG => array(
                'paramName' => self::RETURN_PARAM_IP_LONG,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:]\.,]+$/', $string);
                },
                'paramMaxLength' => 20,
                'paramRequired' => false
            ),
            self::RETURN_PARAM_IP_LAT => array(
                'paramName' => self::RETURN_PARAM_IP_LAT,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:]\.,]+$/', $string);
                },
                'paramMaxLength' => 20,
                'paramRequired' => false
            ),
            self::RETURN_PARAM_ACQ_WIRECARD_TRANS_ID => array(
                'paramName' => self::RETURN_PARAM_ACQ_WIRECARD_TRANS_ID,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 6,
                'paramRequired' => false
            ),
            self::RETURN_PARAM_EVO_AVS_STATUS => array(
                'paramName' => self::RETURN_PARAM_EVO_AVS_STATUS,
                'paramTypeMatchCallable' => function ($string) {
                    return (in_array($string, array(
                        self::RETURN_EVO_AVS_STATUS_ACCEPT,
                        self::RETURN_EVO_AVS_STATUS_DENY,
                        self::RETURN_EVO_AVS_STATUS_CHALLENGE,
                        self::RETURN_EVO_AVS_STATUS_NOSCORE,
                        self::RETURN_EVO_AVS_STATUS_ENETFP,
                        self::RETURN_EVO_AVS_STATUS_ERROR,
                        self::RETURN_EVO_AVS_STATUS_ETMOUT
                    ), true));
                },
                'paramMaxLength' => 9,
                'paramRequired' => false
            ),
            self::RETURN_PARAM_EVO_AVS_CODE => array(
                'paramName' => self::RETURN_PARAM_EVO_AVS_CODE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:]]{4}$/', $string);
                },
                'paramMaxLength' => 4,
                'paramRequired' => false
            ),
            self::RETURN_PARAM_PPCC_TRANS_REF_ID => array(
                'paramName' => self::RETURN_PARAM_PPCC_TRANS_REF_ID,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:]]+$/', $string);
                },
                'paramMaxLength' => 12,
                'paramRequired' => false
            ),
            self::RETURN_PARAM_ABO_ID => array(
                'paramName' => self::RETURN_PARAM_ABO_ID,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 32,
                'paramRequired' => false
            ),
            self::RETURN_PARAM_CC_AUTH_VAL => array(
                'paramName' => self::RETURN_PARAM_CC_AUTH_VAL,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[0-9a-fA-F]+$/', $string);
                },
                'paramMaxLength' => 40,
                'paramRequired' => false
            ),
            self::RETURN_PARAM_SECURITY_LEVEL => array(
                'paramName' => self::RETURN_PARAM_SECURITY_LEVEL,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:]]+$/', $string);
                },
                'paramMaxLength' => 2,
                'paramRequired' => false
            ),
            self::RETURN_PARAM_ADDR_CHECK_MATCH => array(
                'paramName' => self::RETURN_PARAM_ADDR_CHECK_MATCH,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 1,
                'paramRequired' => false
            ),
            self::RETURN_PARAM_ADDR_CHECK_MATCH_ADDR => array(
                'paramName' => self::RETURN_PARAM_ADDR_CHECK_MATCH_ADDR,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 1,
                'paramRequired' => false
            ),
            self::RETURN_PARAM_ADDR_CHECK_MATCH_POST_CODE => array(
                'paramName' => self::RETURN_PARAM_ADDR_CHECK_MATCH_POST_CODE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 1,
                'paramRequired' => false
            ),
            self::RETURN_PARAM_ADDR_CHECK_MATCH_NAME => array(
                'paramName' => self::RETURN_PARAM_ADDR_CHECK_MATCH_NAME,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 1,
                'paramRequired' => false
            ),
            self::RETURN_PARAM_ADDR_CHECK_MATCH_EMAIL => array(
                'paramName' => self::RETURN_PARAM_ADDR_CHECK_MATCH_EMAIL,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 1,
                'paramRequired' => false
            ),
            self::RETURN_PARAM_ADDR_CHECK_MATCH_PHONE => array(
                'paramName' => self::RETURN_PARAM_ADDR_CHECK_MATCH_PHONE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 1,
                'paramRequired' => false
            ),
            self::RETURN_PARAM_AMOUNT_AUTH => array(
                'paramName' => self::RETURN_PARAM_AMOUNT_AUTH,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:]]+$/', $string);
                },
                'paramMaxLength' => 12,
                'paramRequired' => false
            )
        );
        return $parameters;
    }

    /**
     * @return array
     */
    public function getServer2ServerDirectPaymentParamaters()
    {
        $parameters = array(
            self::PARAM_MERCHANT_ID => array(
                'paramName' => self::PARAM_MERCHANT_ID,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/[[:alnum:]]/', $string);
                },
                'paramMaxLength' => 30,
                'paramRequired' => true
            ),
            self::PARAM_TRANS_ID => array(
                'paramName' => self::PARAM_TRANS_ID,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/[[:alnum:]]/', $string);
                },
                'paramMaxLength' => 64,
                'paramRequired' => true
            ),
            self::PARAM_CUSTOMER_ID => array(
                'paramName' => self::PARAM_CUSTOMER_ID,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/[[:alnum:]]/', $string);
                },
                'paramMaxLength' => 64,
                'paramRequired' => false
            ),
            self::PARAM_REF_NO => array(
                'paramName' => self::PARAM_REF_NO,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 30,
                'paramRequired' => true
            ),
            self::PARAM_AMOUNT => array(
                'paramName' => self::PARAM_AMOUNT,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:]]+$/', $string);
                },
                'paramMaxLength' => 12,
                'paramRequired' => true
            ),
            self::PARAM_CURRENCY => array(
                'paramName' => self::PARAM_CURRENCY,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[a-zA-Z]+$/', $string);
                },
                'paramMaxLength' => 3,
                'paramRequired' => true
            ),
            self::PARAM_CC_NUMBER => array(
                'paramName' => self::PARAM_CC_NUMBER,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:]]+$/', $string);
                },
                'paramMaxLength' => 19,
                'paramRequired' => true
            ),
            self::PARAM_CC_CVC => array(
                'paramName' => self::PARAM_CC_CVC,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:]]+$/', $string);
                },
                'paramMaxLength' => 4,
                'paramRequired' => true
            ),
            self::PARAM_CC_EXPIRY => array(
                'paramName' => self::PARAM_CC_EXPIRY,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:]]{6}$/', $string);
                },
                'paramMaxLength' => 6,
                'paramRequired' => true
            ),
            self::RETURN_PARAM_CARD_BRAND => array(
                'paramName' => self::RETURN_PARAM_CARD_BRAND,
                'paramTypeMatchCallable' => function ($string) {
                    return (in_array($string, array(
                        self::CCBRAND_AMEX,
                        self::CCBRAND_AURA,
                        self::CCBRAND_CBN,
                        self::CCBRAND_DINERS,
                        self::CCBRAND_ELO,
                        self::CCBRAND_HIPERCARD,
                        self::CCBRAND_JCB,
                        self::CCBRAND_MAESTRO,
                        self::CCBRAND_MASTERCARD,
                        self::CCBRAND_SOLO,
                        self::CCBRAND_SWITCH,
                        self::CCBRAND_VISA
                    ), true));
                },
                'paramMaxLength' => 22,
                'paramRequired' => false
            ),
            self::PARAM_CAPTURE => array(
                'paramName' => self::PARAM_CAPTURE,
                'paramTypeMatchCallable' => function ($string) {
                    return (strcasecmp($string, 'Auto') === 0 || strcasecmp($string, 'Manual') === 0);
                },
                'paramMaxLength' => 30,
                'paramRequired' => false
            ),
            self::PARAM_ORDER_DESC => array(
                'paramName' => self::PARAM_ORDER_DESC,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 127,
                'paramRequired' => true
            ),
            self::PARAM_URL_NOTIFY => array(
                'paramName' => self::PARAM_URL_NOTIFY,
                'paramTypeMatchCallable' => function ($string) {
                    return filter_var($string, FILTER_VALIDATE_URL);
                },
                'paramMaxLength' => 256,
                'paramRequired' => true
            ),
            self::PARAM_MAC => array(
                'paramName' => self::PARAM_MAC,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 64,
                'paramRequired' => true
            ),
            self::PARAM_USER_DATA => array(
                'paramName' => self::PARAM_USER_DATA,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 1024,
                'paramRequired' => false
            ),
            self::PARAM_REQ_ID => array(
                'paramName' => self::PARAM_REQ_ID,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 32,
                'paramRequired' => false
            ),
            self::PARAM_LANGUAGE => array(
                'paramName' => self::PARAM_LANGUAGE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[a-zA-Z]{2}+$/', $string);
                },
                'paramMaxLength' => 2,
                'paramRequired' => false
            ),
            self::PARAM_SELLING_POINT => array(
                'paramName' => self::PARAM_SELLING_POINT,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:],\._\-\s\+]+$/', $string);
                },
                'paramMaxLength' => 50,
                'paramRequired' => false
            ),
            self::PARAM_SERVICE => array(
                'paramName' => self::PARAM_SERVICE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:],\._\-\s\+]+$/', $string);
                },
                'paramMaxLength' => 50,
                'paramRequired' => false
            ),
            self::PARAM_CHANNEL => array(
                'paramName' => self::PARAM_CHANNEL,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:],\._\-\s\+]+$/', $string);
                },
                'paramMaxLength' => 64,
                'paramRequired' => false
            ),
            self::PARAM_ADDR_COUNTRY_CODE => array(
                'paramName' => self::PARAM_ADDR_COUNTRY_CODE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 2,
                'paramRequired' => false
            ),
            self::PARAM_CREDIT_CARD_HOLDER => array(
                'paramName' => self::PARAM_CREDIT_CARD_HOLDER,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-]+$/', $string);
                },
                'paramMaxLength' => 60,
                'paramRequired' => false
            ),
            self::PARAM_RTF => array(
                'paramName' => self::PARAM_RTF,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 1,
                'paramRequired' => false
            ),
            self::PARAM_IP_ADDR => array(
                'paramName' => self::PARAM_IP_ADDR,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:]\.\:[a-fA-F]]+$/', $string);
                },
                'paramMaxLength' => 15,
                'paramRequired' => false
            ),
            self::PARAM_IP_ZONE => array(
                'paramName' => self::PARAM_IP_ZONE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:],]$/', $string);
                },
                'paramMaxLength' => 350,
                'paramRequired' => false
            ),
            self::PARAM_ZONE => array(
                'paramName' => self::PARAM_ZONE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:],]$/', $string);
                },
                'paramMaxLength' => 350,
                'paramRequired' => false
            ),
            self::PARAM_MARP => array(
                'paramName' => self::PARAM_MARP,
                'paramTypeMatchCallable' => function ($string) {
                    return ($string === 'yes' || $string === 'no');
                },
                'paramMaxLength' => 3,
                'paramRequired' => false
            ),
            self::PARAM_AD_DATA_1 => array(
                'paramName' => self::PARAM_AD_DATA_1,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:],\._\-\/\\]+$/', $string);
                },
                'paramMaxLength' => 28,
                'paramRequired' => false
            ),
            self::PARAM_ABO_ACTION => array(
                'paramName' => self::PARAM_ABO_ACTION,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\_\s]+$/', $string);
                },
                'paramMaxLength' => 10,
                'paramRequired' => false
            ),
            self::PARAM_START_DATE => array(
                'paramName' => self::PARAM_START_DATE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:]]{8}$/', $string);
                },
                'paramMaxLength' => 8,
                'paramRequired' => false
            ),
            self::PARAM_END_DATE => array(
                'paramName' => self::PARAM_END_DATE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:]]{8}$/', $string);
                },
                'paramMaxLength' => 8,
                'paramRequired' => false
            ),
            self::PARAM_INTERVAL => array(
                'paramName' => self::PARAM_INTERVAL,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:]]{1,3}$/', $string);
                },
                'paramMaxLength' => 3,
                'paramRequired' => false
            ),
            self::PARAM_ABO_AMOUNT => array(
                'paramName' => self::PARAM_ABO_AMOUNT,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:]]{1,12}$/', $string);
                },
                'paramMaxLength' => 12,
                'paramRequired' => false
            ),
            self::PARAM_ADDR_CITY => array(
                'paramName' => self::PARAM_ADDR_CITY,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:],\._\-]+$/', $string);
                },
                'paramMaxLength' => 40,
                'paramRequired' => false
            ),
            self::PARAM_ADDR_POST_CODE => array(
                'paramName' => self::PARAM_ADDR_POST_CODE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\.]+$/', $string);
                },
                'paramMaxLength' => 20,
                'paramRequired' => false
            ),
            self::PARAM_ADDR_STREET => array(
                'paramName' => self::PARAM_ADDR_STREET,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\.\-]+$/', $string);
                },
                'paramMaxLength' => 50,
                'paramRequired' => false
            ),
            self::PARAM_ADDR_STREET_NO => array(
                'paramName' => self::PARAM_ADDR_STREET_NO,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\.\-]+$/', $string);
                },
                'paramMaxLength' => 15,
                'paramRequired' => false
            ),
            self::PARAM_ADDR_POBOX => array(
                'paramName' => self::PARAM_ADDR_POBOX,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\_]+$/', $string);
                },
                'paramMaxLength' => 35,
                'paramRequired' => false
            ),
            self::PARAM_ADDR_CO_FIELD => array(
                'paramName' => self::PARAM_ADDR_CO_FIELD,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\_]+$/', $string);
                },
                'paramMaxLength' => 40,
                'paramRequired' => false
            ),
            self::PARAM_ADDR_DISTRICT => array(
                'paramName' => self::PARAM_ADDR_DISTRICT,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\.\-]+$/', $string);
                },
                'paramMaxLength' => 40,
                'paramRequired' => false
            ),
            self::PARAM_NAME => array(
                'paramName' => self::PARAM_NAME,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\-\s\.]+$/', $string);
                },
                'paramMaxLength' => 128,
                'paramRequired' => false
            ),
            self::PARAM_ADDR_STATE => array(
                'paramName' => self::PARAM_ADDR_STATE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\.\-]+$/', $string);
                },
                'paramMaxLength' => 2,
                'paramRequired' => false
            ),
            self::PARAM_EMAIL => array(
                'paramName' => self::PARAM_EMAIL,
                'paramTypeMatchCallable' => function ($string) {
                    return filter_var($string, FILTER_VALIDATE_EMAIL);
                },
                'paramMaxLength' => 128,
                'paramRequired' => false
            ),
            self::PARAM_PHONE => array(
                'paramName' => self::PARAM_PHONE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^\+{0,1}[[:digit:]\\\/\-\(\)\s]+$/', $string);
                },
                'paramMaxLength' => 40,
                'paramRequired' => false
            ),
            self::PARAM_CHARGE_DESCRIPTION => array(
                'paramName' => self::PARAM_CHARGE_DESCRIPTION,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\_]+$/', $string);
                },
                'paramMaxLength' => 12,
                'paramRequired' => false
            ),
            self::PARAM_BILL_FIRST_NAME => array(
                'paramName' => self::PARAM_BILL_FIRST_NAME,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\_\.]+$/', $string);
                },
                'paramMaxLength' => 30,
                'paramRequired' => false
            ),
            self::PARAM_BILL_MIDDLE_NAME => array(
                'paramName' => self::PARAM_BILL_MIDDLE_NAME,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\_\.]+$/', $string);
                },
                'paramMaxLength' => 30,
                'paramRequired' => false
            ),
            self::PARAM_BILL_LAST_NAME => array(
                'paramName' => self::PARAM_BILL_LAST_NAME,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\_\.]+$/', $string);
                },
                'paramMaxLength' => 30,
                'paramRequired' => false
            ),
            self::PARAM_BILL_STREET => array(
                'paramName' => self::PARAM_BILL_STREET,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\_\.]+$/', $string);
                },
                'paramMaxLength' => 30,
                'paramRequired' => false
            ),
            self::PARAM_BILL_STREET_NO => array(
                'paramName' => self::PARAM_BILL_STREET_NO,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\_\.]+$/', $string);
                },
                'paramMaxLength' => 10,
                'paramRequired' => false
            ),
            self::PARAM_BILL_STREET_2 => array(
                'paramName' => self::PARAM_BILL_STREET_2,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\_\.]+$/', $string);
                },
                'paramMaxLength' => 30,
                'paramRequired' => false
            ),
            self::PARAM_BILL_POST_CODE => array(
                'paramName' => self::PARAM_BILL_POST_CODE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\_\.]+$/', $string);
                },
                'paramMaxLength' => 10,
                'paramRequired' => false
            ),
            self::PARAM_BILL_CITY => array(
                'paramName' => self::PARAM_BILL_CITY,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\_\.]+$/', $string);
                },
                'paramMaxLength' => 20,
                'paramRequired' => false
            ),
            self::PARAM_BILL_STATE => array(
                'paramName' => self::PARAM_BILL_STATE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\_\.]+$/', $string);
                },
                'paramMaxLength' => 2,
                'paramRequired' => false
            ),
            self::PARAM_BILL_COUNTRY_CODE => array(
                'paramName' => self::PARAM_BILL_COUNTRY_CODE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\_\.]+$/', $string);
                },
                'paramMaxLength' => 3,
                'paramRequired' => false
            ),
            self::PARAM_BILL_PHONE => array(
                'paramName' => self::PARAM_BILL_PHONE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^\+{0,1}[[:digit:]\\\/\-\(\)\s]+$/', $string);
                },
                'paramMaxLength' => 20,
                'paramRequired' => false
            ),
            self::PARAM_BILL_WORK_PHONE => array(
                'paramName' => self::PARAM_BILL_WORK_PHONE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^\+{0,1}[[:digit:]\\\/\-\(\)\s]+$/', $string);
                },
                'paramMaxLength' => 20,
                'paramRequired' => false
            ),
            self::PARAM_BILL_FAX => array(
                'paramName' => self::PARAM_BILL_FAX,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^\+{0,1}[[:digit:]\\\/\-\(\)\s]+$/', $string);
                },
                'paramMaxLength' => 20,
                'paramRequired' => false
            ),
            self::PARAM_BILL_EMAIL => array(
                'paramName' => self::PARAM_BILL_EMAIL,
                'paramTypeMatchCallable' => function ($string) {
                    return filter_var($string, FILTER_VALIDATE_EMAIL);
                },
                'paramMaxLength' => 60,
                'paramRequired' => false
            ),
            self::PARAM_SHIP_FIRST_NAME => array(
                'paramName' => self::PARAM_SHIP_FIRST_NAME,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-]+$/', $string);
                },
                'paramMaxLength' => 30,
                'paramRequired' => false
            ),
            self::PARAM_SHIP_MIDDLE_NAME => array(
                'paramName' => self::PARAM_SHIP_MIDDLE_NAME,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\.]+$/', $string);
                },
                'paramMaxLength' => 30,
                'paramRequired' => false
            ),
            self::PARAM_SHIP_LAST_NAME => array(
                'paramName' => self::PARAM_SHIP_LAST_NAME,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-]+$/', $string);
                },
                'paramMaxLength' => 30,
                'paramRequired' => false
            ),
            self::PARAM_SHIP_STREET => array(
                'paramName' => self::PARAM_SHIP_STREET,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\.]+$/', $string);
                },
                'paramMaxLength' => 46,
                'paramRequired' => false
            ),
            self::PARAM_SHIP_STREET_NO => array(
                'paramName' => self::PARAM_SHIP_STREET_NO,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\.]+$/', $string);
                },
                'paramMaxLength' => 10,
                'paramRequired' => false
            ),
            self::PARAM_SHIP_STREET_2 => array(
                'paramName' => self::PARAM_SHIP_STREET_2,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\.]+$/', $string);
                },
                'paramMaxLength' => 30,
                'paramRequired' => false
            ),
            self::PARAM_SHIP_POST_CODE => array(
                'paramName' => self::PARAM_SHIP_POST_CODE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]{2,8}$/', $string);
                },
                'paramMaxLength' => 10,
                'paramRequired' => false
            ),
            self::PARAM_SHIP_CITY => array(
                'paramName' => self::PARAM_SHIP_CITY,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\.]+$/', $string);
                },
                'paramMaxLength' => 20,
                'paramRequired' => false
            ),
            self::PARAM_SHIP_STATE => array(
                'paramName' => self::PARAM_SHIP_STATE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\-\_\.]+$/', $string);
                },
                'paramMaxLength' => 2,
                'paramRequired' => false
            ),
            self::PARAM_SHIP_COUNTRY_CODE => array(
                'paramName' => self::PARAM_SHIP_COUNTRY_CODE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]{3}$/', $string);
                },
                'paramMaxLength' => 3,
                'paramRequired' => false
            ),
            self::PARAM_SHIP_PHONE => array(
                'paramName' => self::PARAM_SHIP_PHONE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^\+{0,1}[[:digit:]\\\/\-\(\)\s]+$/', $string);
                },
                'paramMaxLength' => 40,
                'paramRequired' => false
            ),
            self::PARAM_SHIP_WORK_PHONE => array(
                'paramName' => self::PARAM_SHIP_WORK_PHONE,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^\+{0,1}[[:digit:]\\\/\-\(\)\s]+$/', $string);
                },
                'paramMaxLength' => 20,
                'paramRequired' => false
            ),
            self::PARAM_SHIP_EMAIL => array(
                'paramName' => self::PARAM_SHIP_EMAIL,
                'paramTypeMatchCallable' => function ($string) {
                    return filter_var($string, FILTER_VALIDATE_EMAIL);
                },
                'paramMaxLength' => 60,
                'paramRequired' => false
            ),
            self::PARAM_ADDR_STREET_NO_2 => array(
                'paramName' => self::PARAM_ADDR_STREET_NO_2,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]\s\.\-]+$/', $string);
                },
                'paramMaxLength' => 5,
                'paramRequired' => false
            ),
            self::PARAM_ADDR_POST_CODE_2 => array(
                'paramName' => self::PARAM_ADDR_POST_CODE_2,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:]]+$/', $string);
                },
                'paramMaxLength' => 5,
                'paramRequired' => false
            ),
            self::PARAM_ADDR_CITY_2 => array(
                'paramName' => self::PARAM_ADDR_CITY_2,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:alnum:],\._\-]+$/', $string);
                },
                'paramMaxLength' => 32,
                'paramRequired' => false
            ),
            self::PARAM_ADDR_CHOICE => array(
                'paramName' => self::PARAM_ADDR_CHOICE,
                'paramTypeMatchCallable' => function ($string) {
                    return ((int)$string === 1 || (int)$string === 2);
                },
                'paramMaxLength' => 1,
                'paramRequired' => false
            ),
            self::PARAM_QUOTE_ID => array(
                'paramName' => self::PARAM_QUOTE_ID,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:],]+$/', $string);
                },
                'paramMaxLength' => 10,
                'paramRequired' => false
            ),
            self::PARAM_BASE_AMOUNT => array(
                'paramName' => self::PARAM_BASE_AMOUNT,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:]]+$/', $string);
                },
                'paramMaxLength' => 12,
                'paramRequired' => false
            ),
            self::PARAM_LOCAL_AMOUNT => array(
                'paramName' => self::PARAM_LOCAL_AMOUNT,
                'paramTypeMatchCallable' => function ($string) {
                    return preg_match('/^[[:digit:]]+$/', $string);
                },
                'paramMaxLength' => 12,
                'paramRequired' => false
            )
        );
        return $parameters;
    }


    /**
     * @param array $params
     * @return bool|string
     */
    public function getPayPalRedirectURL(array $params)
    {
        if (!$this->_validateParameters($params, $this->getPayPalFormAllowedParameters())) {
            return false;
        }
        $URLMaker = $this->URLMaker;
        $dataQueryString = $URLMaker->getQueryStringFromParamArray($params);
        $length = mb_strlen($dataQueryString, 'UTF-8');
        $dataEncrypted = $this->crypt->ctEncrypt($dataQueryString, $length, $this->computopPassword);

        $URLMaker->setProtocol(URLMaker::PROTOCOL_HTTPS);
        $URLMaker->setDomain(self::URL_PAYGATE_DOMAIN);
        $URLMaker->setPath('/' . self::URL_PAYPAL);
        $URLMaker->addQueryParam(self::PARAM_MERCHANT_ID, $this->merchantID);
        $URLMaker->addQueryParam(self::PARAM_LENGTH, $length);
        $URLMaker->addQueryParam(self::PARAM_DATA, $dataEncrypted);
        $url = $URLMaker->getFullURL();
        if (!$this->_isURLQueryStringBelowMaxLength($url)) {
            throw new \DomainException('Query-String too long.');
        }
        return $url;
    }

    /**
     * @param array $paymentParams
     * @param array $layoutParams
     * @return bool|string
     */
    public function getCCPaySSLURL(array $paymentParams, array $layoutParams)
    {
        if (
            !$this->_validateParameters($paymentParams, $this->getCreditCardPaySSLFormAllowedPaymentParameters()) ||
            !$this->_validateParameters($layoutParams, $this->getCreditCardPaySSLFormAllowedLayoutParameters())
        ) {
            return false;
        }

        $URLMaker = $this->URLMaker;


        $payDataQueryString = $URLMaker->getQueryStringFromParamArray($paymentParams);
        $payDataLength = mb_strlen($payDataQueryString, 'UTF-8');
        $payDataEncrypted = $this->crypt->ctEncrypt($payDataQueryString, $payDataLength, $this->computopPassword);

        $URLMaker->setProtocol(URLMaker::PROTOCOL_HTTPS);
        $URLMaker->setDomain(self::URL_PAYGATE_DOMAIN);
        $URLMaker->setPath('/' . self::URL_PAY_SSL);
        $URLMaker->addQueryParam(self::PARAM_MERCHANT_ID, $this->merchantID);
        $URLMaker->addQueryParam(self::PARAM_LENGTH, $payDataLength);
        $URLMaker->addQueryParam(self::PARAM_DATA, $payDataEncrypted);
        foreach ($layoutParams as $layoutParamName => $layoutParamVal) {
            $URLMaker->addQueryParam($layoutParamName, $layoutParamVal);
        }
        $url = $URLMaker->getFullURL();
        if (!$this->_isURLQueryStringBelowMaxLength($url)) {
            throw new \DomainException('Query-String too long.');
        }
        return $url;
    }

    /**
     * @param array $params
     * @return array|bool
     */
    public function makeDirectPayment(array $params)
    {
        if (!$this->_validateParameters($params, $this->getPayPalFormAllowedParameters())) {
            return false;
        }

        $URLMaker = $this->URLMaker;
        $URLMaker->setProtocol(URLMaker::PROTOCOL_HTTPS);
        $URLMaker->setDomain(self::URL_PAYGATE_DOMAIN);
        $URLMaker->setPath('/' . self::URL_SERVER_2_SERVER);
        $url = $URLMaker->getFullURL();

        $payDataQueryString = $URLMaker->getQueryStringFromParamArray($params);
        $payDataLength = mb_strlen($payDataQueryString, 'UTF-8');
        $payDataEncrypted = $this->crypt->ctEncrypt($payDataQueryString, $payDataLength, $this->computopPassword);

        $paramArray = array(
            self::PARAM_MERCHANT_ID => $this->merchantID,
            self::PARAM_LENGTH => $payDataLength,
            self::PARAM_DATA => $payDataEncrypted
        );
        $totalQueryString = $URLMaker->getQueryStringFromParamArray($paramArray);
        $totalQueryStringLength = mb_strlen($totalQueryString, 'UTF-8');

        $cURLHandler = curl_init();
        curl_setopt($cURLHandler, CURLOPT_URL, $url);
        curl_setopt($cURLHandler, CURLOPT_POST, 1);
        curl_setopt($cURLHandler, CURLOPT_POSTFIELDS, $totalQueryString);
        curl_setopt($cURLHandler, CURLOPT_HTTPHEADER, array(
                'Connection: Close',
                'Content-type: application/x-www-form-urlencoded',
                "Content-length: $totalQueryStringLength"
            )
        );
        curl_setopt($cURLHandler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURLHandler, CURLOPT_HEADER, 0);
        curl_setopt($cURLHandler, CURLOPT_TIMEOUT, 60);
        $cURLOutput = curl_exec($cURLHandler);

        $parsedcURLOutput = array();
        parse_str($cURLOutput, $parsedcURLOutput);
        if (!is_array($parsedcURLOutput) || count($parsedcURLOutput) !== 2) {
            return false;
        }

        $returnDataLength = 0;
        $returnData = '';
        foreach($parsedcURLOutput as $key => $value) {
            if(strcasecmp('Len',$key) === 0) {
                $returnDataLength = $value;
            } elseif(strcasecmp('Data',$key)) {
                $returnData = $value;
            }
        }

        $decryptedReturnData = $this->crypt->ctDecrypt($returnData, $returnDataLength, $this->computopPassword);
        $returnDataArr = array();
        parse_str($decryptedReturnData, $returnDataArr);
        array_walk($returnDataArr,function($val){return urldecode($val);});
        return $returnDataArr;
    }

    /**
     * @param $url
     * @return bool
     */
    protected function _isURLQueryStringBelowMaxLength($url)
    {
        $queryString = explode('?', $url)[1];
        return ($queryString <= self::URL_QUERY_STRING_MAXLENGTH);
    }

}