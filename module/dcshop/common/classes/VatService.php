<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\dcShop\interfaces\ItemPriceDataInterface;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 18.10.2016
 * Time: 18:56
 */
class VatService
{

    const QUERY_GET_VAT_SETUP = '
        SELECT 
          *
        FROM
          shop_vat_posting_setup          
    ';

    const QUERY_GET_SHOP_VAT_DISPLAY_BY_SITE = '
        SELECT 
            prices_including_vat,
            vat_bus_posting_group
        FROM
          main_site ms
        INNER JOIN
          main_language ml
        ON (
            ml.main_site_id = ms.id
        )  
        LEFT JOIN
          shop_shop ss
        ON (
              ss.company = ml.company
          AND ss.code = ml.shop_code      
        )  
        WHERE
              ml.code = :main_language_code
          AND ms.code= :main_site_code
    ';

    protected $vatValues = [];

    protected $shopVatDisplaySettingBySiteLanguage = [];

    protected $shopVatBusPostingGroupBySiteLanguage = [];

    /**
     * @var PDOQueryWrapper
     */
    protected $db;

    /**
     * VatService constructor.
     * @param PDOQueryWrapper $db
     */
    public function __construct(PDOQueryWrapper $db)
    {
        $this->db = $db;
        $this->fetchVatValues();
    }

    public function fetchVatValues()
    {
        $this->db->setQuery(self::QUERY_GET_VAT_SETUP)->doQuery();
        $resArr = $this->db->getResultArray();
        $vatValues = [];
        foreach ($resArr as $row) {
            $key = $row['company'] . '|' . $row['vat_bus_posting_group'] . '|' . $row['vat_prod_posting_group'];
            $value = (float)$row['vat_percent'];
            $vatValues[$key] = $value;
        }
        $this->vatValues = $vatValues;
    }

    /**
     * @param $company
     * @param $vatBusPostingGroup
     * @param $vatProdPostingGroup
     * @return mixed
     */
    public function getVatPercent($company, $vatBusPostingGroup, $vatProdPostingGroup)
    {
        $key = $company . '|' . $vatBusPostingGroup . '|' . $vatProdPostingGroup;
        if (!array_key_exists($key,$this->vatValues)) {
            throw new \DomainException('No VAT-Setup Data found for company [' . $company . '], vat_bus_posting_group [' . $vatBusPostingGroup . '] and vat_prod_posting_group [' . $vatProdPostingGroup . '].');
        }
        return $this->vatValues[$key];
    }


    /**
     * @param $price
     * @param $percentage
     * @return float
     */
    public function getPriceIncreasedByPercentage($price, $percentage)
    {
        $price = (float)$price;
        $percentage = (float)$percentage;
        $price += ($percentage / 100) * $price;
        return $price;
    }

    /**
     * @param $price
     * @param $percentage
     * @return float
     */
    public function getPriceDecreasedByIncludedPercentage($price, $percentage)
    {
        $price = (float)$price;
        $percentage = (float)$percentage;
        $price -= ($price / (100 + $percentage)) * $percentage;
        return $price;
    }

    /**
     * @param $price
     * @param $priceIncludesVat
     * @param $percent
     * @param $shopPriceDisplayIncludesVat
     * @return float
     */
    public function getPriceAdjustedForVatPercent($price, $priceIncludesVat, $percent, $shopPriceDisplayIncludesVat)
    {
        $priceIncludesVat = (bool)$priceIncludesVat;
        $shopPriceDisplayIncludesVat = (bool)$shopPriceDisplayIncludesVat;
        $price = (float)$price;
        $percent = (float)$percent;
        if (!$priceIncludesVat && $shopPriceDisplayIncludesVat) {
            //Add percentage
            $price = $this->getPriceIncreasedByPercentage($price,$percent);
        } elseif ($priceIncludesVat && !$shopPriceDisplayIncludesVat) {
            //Remove included percentage
            $price = $this->getPriceDecreasedByIncludedPercentage($price, $percent);
        }
        return $price;
    }

    /**
     * @param $company
     * @param $siteCode
     * @param $siteLanguageCode
     * @param $vatProdPostingGroup
     * @param $price
     * @param $priceIncludesVat
     * @return float
     */
    public function getPriceAdjustedForVatBySiteLanguage($company, $siteCode, $siteLanguageCode, $vatProdPostingGroup, $price, $priceIncludesVat)
    {
        $shopPriceDisplayIncludesVat = $this->getShopPriceDisplayIncludesVatBySiteLanguage($siteCode,$siteLanguageCode);
        $vatBusPostingGroup = $this->getShopVatBusPostingGroupBySiteLanguage($siteCode, $siteLanguageCode);
        $key = $company . '|' . $vatBusPostingGroup . '|' . $vatProdPostingGroup;
        $percent = (float)$this->vatValues[$key];
        return $this->getPriceAdjustedForVatPercent($price, $priceIncludesVat,$percent,$shopPriceDisplayIncludesVat);
    }

    /**
     * @param $siteCode
     * @param $siteLanguageCode
     * @return bool
     */
    protected function getShopPriceDisplayIncludesVatBySiteLanguage($siteCode, $siteLanguageCode)
    {
        $key = $siteCode . '|' . $siteLanguageCode;
        if (!array_key_exists($key,$this->shopVatDisplaySettingBySiteLanguage)) {
            $this->fetchShopDataBySiteLanguage($siteCode, $siteLanguageCode);
        }
        return (bool)$this->shopVatDisplaySettingBySiteLanguage[$key];

    }

    /**
     * @param $siteCode
     * @param $siteLanguageCode
     * @return bool
     */
    public function getShopVatBusPostingGroupBySiteLanguage($siteCode, $siteLanguageCode)
    {
        $key = $siteCode . '|' . $siteLanguageCode;
        if (!array_key_exists($key,$this->shopVatBusPostingGroupBySiteLanguage)) {
            $this->fetchShopDataBySiteLanguage($siteCode, $siteLanguageCode);
        }
        return (bool)$this->shopVatBusPostingGroupBySiteLanguage[$key];
    }

    /**
     * @param $siteCode
     * @param $siteLanguageCode
     */
    protected function fetchShopDataBySiteLanguage($siteCode, $siteLanguageCode)
    {
        $key = $siteCode . '|' . $siteLanguageCode;

        $this->db->setQuery(self::QUERY_GET_SHOP_VAT_DISPLAY_BY_SITE)->prepareQuery();
        $params = [
            [':main_site_code',$siteCode,\PDO::PARAM_STR],
            [':main_language_code',$siteLanguageCode,\PDO::PARAM_STR],
        ];
        $this->db->bindParameters($params);
        $this->db->doQuery;
        $resArr = $this->db->getResultArray();
        if (array_key_exists(0,$resArr)) {
            if (array_key_exists('prices_including_vat',$row[0])) {
                $this->shopVatDisplaySettingBySiteLanguage[$key] = (bool)$row[0]['prices_including_vat'];
            }
            if (array_key_exists('vat_bus_posting_group',$row[0])) {
                $this->shopVatBusPostingGroupBySiteLanguage[$key] = (string)$row[0]['vat_bus_posting_group'];
            }
        }
    }

    /**
     * @param $company
     * @param $siteCode
     * @param $siteLanguageCode
     * @param ItemPriceDataInterface $itemPriceData
     */
    public function adjustItemPriceDataObjectBySite($company, $siteCode, $siteLanguageCode, ItemPriceDataInterface $itemPriceData)
    {
        $outputToIncludeVAT = $this->getShopPriceDisplayIncludesVatBySiteLanguage($siteCode,$siteLanguageCode);
        $vatBusPostingGroup = $this->getShopVatBusPostingGroupBySiteLanguage($siteCode, $siteLanguageCode);
        $this->adjustItemPriceDataObject($itemPriceData,$company,$outputToIncludeVAT,$vatBusPostingGroup);
    }

    /**
     * @param ItemPriceDataInterface $itemPriceData
     * @param $company
     * @param $outputToIncludeVAT
     * @param $vatBusPostingGroup
     */
    public function adjustItemPriceDataObject(ItemPriceDataInterface $itemPriceData, $company, $outputToIncludeVAT, $vatBusPostingGroup)
    {
        if ($itemPriceData->getVATAdjustmentStatus() !== ItemPriceData::VAT_ADJUSTMENT_STATUS_UNADJUSTED) {
            return;
        }

        $vatProdPostingGroup = $itemPriceData->getItemVATProdPostingGroup();
        $key = $company . '|' . $vatBusPostingGroup . '|' . $vatProdPostingGroup;
        $priceVATPercent = (float)$this->vatValues[$key];

        $oldPrice = $itemPriceData->price;
        $taxAmnt = 0.00;
        if ($oldPrice !== 0.00) {
            $itemPriceData->setVATPercent($priceVATPercent);
            $newPrice = $oldPrice;
            if (!$itemPriceData->priceIncludesVAT && $outputToIncludeVAT) {
                $newPrice = $this->getPriceIncreasedByPercentage($oldPrice,$priceVATPercent);
                $itemPriceData->setVATAmount($newPrice - $oldPrice);
                $itemPriceData->setPrice($newPrice);
                $itemPriceData->setVATAdjustmentStatusVATAdded();
                $itemPriceData->setPriceIncludesVAT(true);
            } elseif ($itemPriceData->priceIncludesVAT && !$outputToIncludeVAT) {
                $newPrice = $this->getPriceDecreasedByIncludedPercentage($oldPrice, $priceVATPercent);
                $itemPriceData->setVATAmount($oldPrice - $newPrice);
                $itemPriceData->setPrice($newPrice);
                $itemPriceData->setVATAdjustmentStatusVATSubtracted();
                $itemPriceData->setPriceIncludesVAT(false);
            }
        } else {
            $itemPriceData->setVATAmount(0.00);
        }

    }


}