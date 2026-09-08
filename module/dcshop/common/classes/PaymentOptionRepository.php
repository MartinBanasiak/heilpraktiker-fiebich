<?php

namespace DynCom\dc\dcShop\classes;

use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;

/**
 * Class PaymentOptionRepository
 */
class PaymentOptionRepository implements Repository
{

    use genericRepositoryTrait;

    /**
     * PaymentOptionRepository constructor.
     * @param PDOQueryWrapper $db
     * @param PaymentOptionConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param PaymentOptionCollection $collection
     * @param $cacheAll
     */
    public function __construct(
        PDOQueryWrapper $db,
        PaymentOptionConfig $config,
        CriteriaHelperInterface $criteriaValidationService,
        PaymentOptionCollection $collection,
        $cacheAll = false
    ) {
        $this->db = $db;
        $this->config = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection = $collection;
        $this->collectionEntryClassName = $this->collection->getEntryClassName();
        $this->cacheAll = $cacheAll;
    }

    /**
     * @return PaymentOption
     */
    public function getNullObject()
    {
        return new PaymentOption($this->config);
    }

    /**
     * @param CurrShopConfiguration $configuration
     * @return $ids
     */
    function getrAllowedPaymentOptions(CurrShopConfiguration $configuration)
    {
        $company = $configuration->getCompany();
        $shopCode = $configuration->getShopCode();
        $languageCode = $configuration->getShopLanguageCode();
        $customerNo = $configuration->getCustomerNo();
        $query = "
        Select id 
        from  
            (
                SELECT distinct shop_payment_option.id   
                FROM shop_permissions_group_link
                    join shop_payment_option on shop_permissions_group_link.line_no = shop_payment_option.line_no 
                        and shop_permissions_group_link.shop_code  = shop_payment_option.shop_code
                        and shop_permissions_group_link.language_code = shop_payment_option.language_code
                        and shop_permissions_group_link.company = shop_payment_option.company
                where shop_permissions_group_link.type = 8 
                    and shop_permissions_group_link.customer_no = '" . $customerNo . "' 
                    and shop_permissions_group_link.shop_code = '" . $shopCode . "'  
                    and shop_permissions_group_link.language_code = '" . $languageCode . "' 
                    and shop_permissions_group_link.company = '" . $company . "'  
             
             union 
             
              SELECT distinct shop_payment_option.id 
              FROM shop_permissions_group_link
                    join shop_payment_option on shop_permissions_group_link.line_no = shop_payment_option.line_no 
                        and shop_permissions_group_link.shop_code  = shop_payment_option.shop_code
                        and shop_permissions_group_link.language_code = shop_payment_option.language_code
                        and shop_permissions_group_link.company = shop_payment_option.company
                    where shop_permissions_group_link.type = 7 
                        and shop_permissions_group_link.shop_code = '" . $shopCode . "'  
                        and shop_permissions_group_link.language_code = '" . $languageCode . "'   
                        and shop_permissions_group_link.company = '" . $company . "' 
                        and shop_permissions_group_link.permission_group_code in
                        ( 
                            SELECT permission_group_code
                            FROM shop_permissions_group_link
                            where customer_no = '" . $customerNo . "'  
                                and company = '" . $company . "' 
                                and type = 0
                        )
                        
              union   
              
              SELECT distinct shop_payment_option.id 
              FROM shop_permissions_group_link
                    join shop_payment_option on shop_permissions_group_link.line_no <> shop_payment_option.line_no 
                        and shop_permissions_group_link.shop_code  = shop_payment_option.shop_code
                        and shop_permissions_group_link.language_code = shop_payment_option.language_code
                        and shop_permissions_group_link.company = shop_payment_option.company
                    where  shop_permissions_group_link.shop_code = '" . $shopCode . "' 
                        and shop_permissions_group_link.language_code = '" . $languageCode . "'  
                         and shop_permissions_group_link.company = '" . $company . "' 
                        and (shop_permissions_group_link.type = 7 or shop_permissions_group_link.type = 8)
                       
                        
            ) as shipping_option 
        where shipping_option.id not in 
        (
            
            SELECT distinct shop_payment_option.id   
            FROM shop_permissions_group_link
                join shop_payment_option on shop_permissions_group_link.line_no = shop_payment_option.line_no 
                    and shop_permissions_group_link.shop_code  = shop_payment_option.shop_code
                    and shop_permissions_group_link.language_code = shop_payment_option.language_code
                    and shop_permissions_group_link.company = shop_payment_option.company
            where shop_permissions_group_link.type = 8 
                and shop_permissions_group_link.customer_no <> '" . $customerNo . "'  
                and shop_permissions_group_link.shop_code = '" . $shopCode . "' 
                and shop_permissions_group_link.language_code = '" . $languageCode . "' 
                and shop_permissions_group_link.company = '" . $company . "'  
         
           union 
         
          SELECT distinct shop_payment_option.id
          FROM shop_permissions_group_link
                join shop_payment_option on shop_permissions_group_link.line_no = shop_payment_option.line_no 
                    and shop_permissions_group_link.shop_code  = shop_payment_option.shop_code
                    and shop_permissions_group_link.language_code = shop_payment_option.language_code
                    and shop_permissions_group_link.company = shop_payment_option.company
          where shop_permissions_group_link.type = 7 
                and shop_permissions_group_link.shop_code = '" . $shopCode . "' 
                and shop_permissions_group_link.language_code = '" . $languageCode . "' 
                and shop_permissions_group_link.company = '" . $company . "'   
                and shop_permissions_group_link.permission_group_code in
                ( 
                    SELECT permission_group_code
                    FROM shop_permissions_group_link
                    where customer_no <> '" . $customerNo . "' 
                          and company = '" . $company . "'  
                          and type = 0
                )
         ) 
      ";

        $allowedPaymentOptions = $this->getQueryResultArray($query);
        return $allowedPaymentOptions;

    }

}