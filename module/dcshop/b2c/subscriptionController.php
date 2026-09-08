<?php
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 16.07.2015
 * Time: 13:45
 */
/*
include('../../../htdocs/plugins/PSR/Logger/LogLevel.php');
include('../../../htdocs/plugins/PSR/Logger/LoggerAwareInterface.php');
include('../../../htdocs/plugins/PSR/Logger/LoggerInterface.php');
include('../../../htdocs/plugins/PSR/Logger/AbstractLogger.php');
include('../../../htdocs/plugins/PSR/Logger/\InvalidArgumentException.php');
include('../../../htdocs/plugins/PSR/Logger/LoggerTrait.php');
include('../../../htdocs/plugins/PSR/Logger/LoggerAwareTrait.php');*/

use DynCom\dc\common\classes\EMail;
use DynCom\dc\common\classes\NAVDateFormulaManagement;
use DynCom\dc\dcShop\classes\CurrShopConfiguration;
use DynCom\dc\dcShop\classes\Customer;
use DynCom\dc\dcShop\classes\CustomerConfig;
use DynCom\dc\dcShop\classes\WebshopItemRepository;
use DynCom\dc\dcShop\subscriptions\classes\ItemSubscriptionDataBuilder;
use DynCom\dc\dcShop\subscriptions\classes\SubscriptionCustomerLink;
use DynCom\dc\dcShop\subscriptions\classes\SubscriptionCustomerLinkConfig;
use DynCom\dc\dcShop\subscriptions\classes\SubscriptionCustomerLinkRepository;
use DynCom\dc\dcShop\subscriptions\classes\SubscriptionDateCalculator;
use DynCom\dc\dcShop\subscriptions\classes\SubscriptionItemDecorator;
use DynCom\dc\dcShop\subscriptions\classes\SubscriptionItemPriceDecorator;
use DynCom\dc\dcShop\subscriptions\classes\SubscriptionSequenceStep;

$subscriptionItemID 		 = filter_var($_POST['subscription_item_id'],FILTER_SANITIZE_NUMBER_INT);
$subscriptionItemVariantCode = filter_var($_POST['subscription_item_var_code'],FILTER_SANITIZE_STRING);
$subscriptionItemQuantity 	 = filter_var($_POST['subscription_item_qty'],FILTER_SANITIZE_NUMBER_FLOAT);
$subscriptionHeaderID 		 = filter_var($_POST['subscription_header_id'],FILTER_SANITIZE_NUMBER_INT);

$GLOBALS['subscription_order_data']['subscription_item_id'] = $subscriptionItemID;
$GLOBALS['subscription_order_data']['subscription_item_var_code'] = $subscriptionItemVariantCode;
$GLOBALS['subscription_order_data']['subscription_item_qty'] = $subscriptionItemQuantity;
$GLOBALS['subscription_order_data']['subscription_header_id'] = $subscriptionHeaderID;

if(!($subscriptionItemID > 0) && isset($_SESSION['subscription_item_id'])) {
	$subscriptionItemID 		 = filter_var($_SESSION['subscription_item_id'],FILTER_SANITIZE_NUMBER_INT);
	$subscriptionItemVariantCode = filter_var($_SESSION['subscription_item_var_code'],FILTER_SANITIZE_STRING);
	$subscriptionItemQuantity 	 = filter_var($_SESSION['subscription_item_qty'],FILTER_SANITIZE_NUMBER_FLOAT);
	$subscriptionHeaderID 		 = filter_var($_SESSION['subscription_header_id'],FILTER_SANITIZE_NUMBER_INT);
}

if($subscriptionItemID > 0) {
	$_SESSION['subscription_item_id'] = $subscriptionItemID;
	$_SESSION['subscription_item_var_code'] = $subscriptionItemVariantCode;
	$_SESSION['subscription_item_qty'] = $subscriptionItemQuantity;
	$_SESSION['subscription_header_id'] = $subscriptionHeaderID;
}


if(!isset($IOCContainer)) {
    $IOCContainer = unserialize($_SESSION['IOC'],['allowed_classes' => [\Dice\Dice::class]]);
}

$GLOBALS['IOC'] = $IOCContainer;

$currShopConfiguration = $IOCContainer->create('$CurrShopConfig');

$GLOBALS['curr_shop_configuration'] = &$currShopConfiguration;

$GLOBALS['in_subscription_order'] = false;
$webshopItemRepository = $IOCContainer->create('DynCom\dc\dcShop\classes\WebshopItemRepository');
$subscriptionDataBuilder = $IOCContainer->create('DynCom\dc\dcShop\subscriptions\classes\ItemSubscriptionDataBuilder');
$vatMgr = $IOCContainer->create('$VATManager');
$priceProvider = $IOCContainer->create('DynCom\dc\dcShop\classes\AdvancedPriceProvider');
$availabilityProvider = $IOCContainer->create('DynCom\dc\dcShop\classes\DefaultItemAvailabilityProvider');
$subscriptionRepo = $IOCContainer->create('DynCom\dc\dcShop\subscriptions\classes\SubscriptionHeaderRepository');
$seqStepRepo = $IOCContainer->create('DynCom\dc\dcShop\subscriptions\classes\SubscriptionSequenceStepRepository');


if($subscriptionItemID > 0) {
    if(($webshopItemRepository instanceof WebshopItemRepository) && ($subscriptionDataBuilder instanceof ItemSubscriptionDataBuilder)) {
        $itemObj = $webshopItemRepository->findByID($subscriptionItemID);
        $subscHeader = $subscriptionRepo->findByID($subscriptionHeaderID);
        $itemPriceData = $priceProvider->getItemCustomerPrice($itemObj,$subscriptionItemQuantity,$currShopConfiguration->getCustomer());
		

        $itemSubscriptionData = $subscriptionDataBuilder->getItemSubscriptionData($itemObj);
        $subscriptionItemWithPrice = new SubscriptionItemPriceDecorator($itemPriceData,$subscHeader,$seqStepRepo,new NAVDateFormulaManagement(),$vatMgr,$itemSubscriptionData->getItemLink($subscHeader->code));
        $subscriptionItem = new SubscriptionItemDecorator($itemSubscriptionData,$subscriptionItemWithPrice,$availabilityProvider);
        $subscriptionItem->setQuantity($subscriptionItemQuantity);
		
		$logger = null;
		$subscriptionSubtotal = $subscriptionItem->getLineAmount();
        $subscriptionVATCode = $subscriptionItem->getVATCode();
        $subscriptionVATPercent = $subscriptionItem->getVATPercent();
        $subscriptionVATAmount = $subscriptionItem->getVATAmount();

        $GLOBALS['in_subscription_order'] = true;
        $GLOBALS['subscription_item_id'] = $subscriptionItemID;
        $GLOBALS['subscription_item'] = $subscriptionItem;
        $GLOBALS['subscription_item_query'] =
            "SELECT * FROM shop_item WHERE id = '" . $subscriptionItemID."'";
        $GLOBALS['subscription_item_basket_query'] =
            'SELECT shop_item.*,
            ' . (string)((float)$subscriptionItemQuantity) . ' as \'basket_quantity\',
            ' . (string)((float)$subscriptionItem->getUnitPrice()) . ' as \'customer_price\',
            0 as \'allow_invoice_disc\',
            \'\' as \'package_for_item\'
            FROM shop_item
            WHERE id = ' . $subscriptionItemID;

            $GLOBALS['subscription_item_vat_query'] =
            '
            SELECT
             ' . $subscriptionSubtotal . ' as \'total\',
             \'' . $subscriptionVATCode . '\' as \'vat_group\',
             ' . $subscriptionVATPercent . ' as \'vat_percent\',
             ' . $subscriptionVATAmount . ' as \'vat_amount\'
            FROM DUAL
            LIMIT 1
            ';
		$GLOBALS['subscription_item_vat_group_query'] = "
			SELECT '$subscriptionVATCode' AS 'vat_prod_posting_group'
			FROM DUAL
		";
       /* $_SESSION['subscription_data_container']['subscription_item_id'] = $subscriptionItemID;
        $_SESSION['subscription_data_container']['subscription_item'] = $subscriptionItem;
        $_SESSION['subscription_data_container']['subscription_item_query'] =  $GLOBALS['subscription_item_query'];
        $_SESSION['subscription_data_container']['subscription_item_basket_query'] =  $GLOBALS['subscription_item_basket_query'];
        $_SESSION['subscription_data_container']['subscription_item_vat_query'] = $GLOBALS['subscription_item_vat_query'];*/
    }

} elseif(array_key_exists('subscription_data_container',$_SESSION)) {
    $GLOBALS['in_subscription_order'] = true;
    if(array_key_exists('subscription_item_id',$_SESSION['subscription_data_container'])) {
        $GLOBALS['subscription_item_id'] = $_SESSION['subscription_data_container']['subscription_item_id'];
    }
    if(array_key_exists('subscription_item',$_SESSION['subscription_data_container'])) {
        $GLOBALS['subscription_item'] = $_SESSION['subscription_data_container']['subscription_item'];
    }
    if(array_key_exists('subscription_item_query',$_SESSION['subscription_data_container'])) {
        $GLOBALS['subscription_item_query'] = $_SESSION['subscription_data_container']['subscription_item_query'];
    }
    if(array_key_exists('subscription_item_basket_query',$_SESSION['subscription_data_container'])) {
        $GLOBALS['subscription_item_basket_query'] = $_SESSION['subscription_data_container']['subscription_item_basket_query'];
    }
    if(array_key_exists('subscription_item_vat_query',$_SESSION['subscription_data_container'])) {
        $GLOBALS['subscription_item_vat_query'] = $_SESSION['subscription_data_container']['subscription_item_vat_query'];
    }
}

function createSubscriptionCustomerLink(CurrShopConfiguration $currShopConfiguration, SubscriptionItemDecorator $subscriptionItem, $salesHeader)  {
	include_once('../../../htdocs/plugins/PSR/Logger/TXTLogger.php');	
	$subscriptionHeader = $subscriptionItem->getSubscriptionHeader();
    $IOCContainer = $GLOBALS['IOC'];

    $today = new DateTime();
    $today->setTimestamp(strtotime('midnight'));
	$seqStepRepository = $IOCContainer->create('DynCom\dc\dcShop\subscriptions\classes\SubscriptionSequenceStepRepository');
    $custLinkRepository = $IOCContainer->create('DynCom\dc\dcShop\subscriptions\classes\SubscriptionCustomerLinkRepository');
    if($custLinkRepository instanceof SubscriptionCustomerLinkRepository) {
        $custLinkRepository->startTransaction();
        $subscDateHandler = new SubscriptionDateCalculator($currShopConfiguration,$IOCContainer->create('DynCom\dc\dcShop\subscriptions\classes\SubscriptionHeaderRepository'),
            $IOCContainer->create('DynCom\dc\dcShop\subscriptions\classes\SubscriptionSequenceStepRepository'),new NAVDateFormulaManagement());
        //$logger = new TxtLog(realpath('../../../htdocs/userdata/'),'SubscriptionDateCalcLog.txt');
        $logger = null;
        //---Logging ACTIVE
        //$subscDateHandler->setLogger($logger);
        //+++
        $customerObj = new Customer(new CustomerConfig());
		if(count($GLOBALS['shop_customer']) > 0) {
			$customerObj->mapFromArray($GLOBALS["shop_customer"]);
		} else {
			$customerID = existing_customer_id_from_sales_header($salesHeader);
			if($customerID > 0) {
				$customerRepository = $IOCContainer->create('DynCom\dc\dcShop\classes\CustomerRepository');
				$customerObj = $customerRepository->findByID($customerID);
			}
		}
		$firstStepLineNo = 0;
		$firstStep = $seqStepRepository->getFirstStep($subscriptionHeader,new DateTime(),SubscriptionDateCalculator::BASE_DATE_HANDLING_EXCLUDED);
		if($firstStep instanceof SubscriptionSequenceStep) {
			$firstStepLineNo = $firstStep->line_no;
		}
		
		
        $arr = [
            'company'                       => $subscriptionHeader->company,
            'subscription_code'             => $subscriptionHeader->code,
            'customer_no'                   => $salesHeader['customer_no'],
            'webshop_order_email'           => $salesHeader['user_email'],
            'line_no'                       => $custLinkRepository->getNextLineNoForSubscriptionAndCustomer($subscriptionHeader,$customerObj,new EMail($salesHeader['user_email'])),
            'bill_to_name'                  => $salesHeader['bill_to_name'],
            'bill_to_name_2'                => $salesHeader['bill_to_name_2'],
            'bill_to_address'               => $salesHeader['bill_to_address'],
            'bill_to_address_2'             => $salesHeader['bill_to_address_2'],
            'bill_to_city'                  => $salesHeader['bill_to_city'],
            'ship_to_name'                  => $salesHeader['ship_to_name'],
            'ship_to_name_2'                => $salesHeader['ship_to_name_2'],
            'ship_to_address'               => $salesHeader['ship_to_address'],
            'ship_to_address_2'             => $salesHeader['ship_to_address_2'],
            'ship_to_city'                  => $salesHeader['ship_to_city'],
            'currency_code'                 => $salesHeader['currency_code'],
            'bill_to_post_code'             => $salesHeader['bill_to_post_code'],
            'bill_to_country_region_code'   => $salesHeader['bill_to_country'],
            'ship_to_post_code'             => $salesHeader['ship_to_post_code'],
            'ship_to_country_region_code'   => $salesHeader['ship_to_country'],
            'webshop_shop_code'             => $salesHeader['shop_code'],
            'webshop_language_code'         => $salesHeader['language_code'],
            'webshop_pay_opt_line_no'       => $salesHeader['payment_option_line_no'],
            'webshop_ship_opt_line_no'      => $salesHeader['shipping_option_line_no'],
            'signup_date'                   => $today->format('Y-m-d'),
			'cancellation_date'				=> 'NULL',
            'no_of_turns_processed'         => 0,
            'user_name'                     => $salesHeader['user_name'],
            'subscription_item_no'          => $subscriptionItem->getItemNo(),
			'next_sequence_step_line_no'	=> $firstStepLineNo,
            'subscription_qty_per_turn'     => $subscriptionItem->getQuantity(),
            'initial_order_no'              => $salesHeader['order_no'],
			'update_insert'                 => 1,
            'to_delete'                     => 0
        ];

        $custLink = new SubscriptionCustomerLink(new SubscriptionCustomerLinkConfig());
        $custLink->mapFromArray($arr);

        $shipOrderCreateDate = $subscDateHandler->getCustomerSubscriptionNextShipOrderCreateDate($custLink,new DateTime(),SubscriptionDateCalculator::BASE_DATE_HANDLING_INCLUDED);
        $payOrderCreateDate = $subscDateHandler->getCustomerSubscriptionNextPayOrderCreateDate($custLink,new DateTime(),SubscriptionDateCalculator::BASE_DATE_HANDLING_INCLUDED);

		if($shipOrderCreateDate instanceof DateTime) {
			$arr['next_order_creation_date'] = $shipOrderCreateDate->format('Y-m-d');
		}
		if($payOrderCreateDate instanceof DateTime) {
			$arr['next_order_w_payment_date'] = $payOrderCreateDate->format('Y-m-d');
		}
		
        $custLink->mapFromArray($arr);

        $id = $custLinkRepository->createInDB($custLink);
        if($custLinkRepository->commitTransaction()) {
            return $custLinkRepository->findByID($id);
        }
        return false;
    }

    return false;

}