<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 23.06.2017
 * Time: 10:40
 */

namespace DynCom\dc\dcShop\classes;


use DynCom\dc\common\interfaces\GenericDBQueryWrapperInterface;

class VATManagerFactory
{
    protected const QUERY_GET_VAT_BUS_POSTING_GROUP_FROM_COUNTRY = '
        SELECT 
          vat_bus_posting_group
        FROM
          shop_country
        WHERE
            company = :company
        AND code = :country_code
    ';

    protected const QUERY_VAT_BUS_POSTING_GROUP_FROM_COUNTRY_VATID = '
      SELECT COALESCE(
            (SELECT 
              vat_bus_posting_group 
            FROM 
              shop_customer_template 
            WHERE (
              shop_customer_template.code = (
                SELECT 
                  CASE WHEN :vatid != \'\' AND reverse_charge=1 THEN reverse_charge_customer_template_code ELSE customer_template_code END 
                FROM 
                  shop_country 
                WHERE 
                      company = :company 
                  AND shop_code = :shop_code 
                  AND language_code = :language_code 
                  AND country_code = :country_code
              )
            )),
            :shop_vat_code
      ) AS vat_bus_posting_group FROM DUAL
    ';

    public function getVATManager(GenericDBQueryWrapperInterface $shopDB,
                                          string $company,
                                          string $shopCode,
                                          string $languageCode,
                                          string $VATBusPostingGroup,
                                          ?string $shipToCountry,
                                          ?string $customerVatID,
                                          bool $shopPricesIncludeVAT): VATManager
    {
        $VATBusPostingGroup = $this->getVATBusPostingGroupFromCountryCodeAndShop($shopDB,$company,$shopCode,$languageCode,$shipToCountry,$customerVatID,$VATBusPostingGroup);
        $VATManager = new VATManager($shopDB,$company,$VATBusPostingGroup,$shopPricesIncludeVAT);
        return $VATManager;
    }

    protected function getVATBusPostingGroupFromCountryCodeAndShop(GenericDBQueryWrapperInterface $shopDB, string $company, string $shopCode,string $languageCode,?string $countryCode, ?string $customerVatID, string $shopVATBusPostingGroup): string
    {
        $params = [
            [':company',$company,\PDO::PARAM_STR],
            [':shop_code',$shopCode,\PDO::PARAM_STR],
            [':language_code',$languageCode,\PDO::PARAM_STR],
            [':vatid',$customerVatID,\PDO::PARAM_STR],
            [':country_code',$countryCode,\PDO::PARAM_STR],
            [':shop_vat_code',$shopVATBusPostingGroup,\PDO::PARAM_STR],
        ];

        $shopDB->setQuery(self::QUERY_VAT_BUS_POSTING_GROUP_FROM_COUNTRY_VATID);
        $shopDB->prepareQuery();
        $shopDB->bindParameters($params);
        $shopDB->executePreparedStatement();
        $resultArr = $shopDB->getResultArray();
        $VATBusPostingGroup = $resultArr[0]['vat_bus_posting_group'] ?? $shopVATBusPostingGroup;
        return $VATBusPostingGroup;
    }

}