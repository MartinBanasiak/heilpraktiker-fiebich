<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\GenericDBQueryWrapperInterface;
use DynCom\dc\dcShop\interfaces\IVATManager;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 25.03.2015
 * Time: 01:40
 */
class VATManager implements IVATManager
{

    protected $db;
    protected $company;
    protected $VATBusPostingGroup;
    protected $shopPricesIncludeVAT;
    protected $vatPercentForPostingGroupMemo;
    protected $vatPercentForPricePostingGroupMemo;

    /**
     * VATManager constructor.
     * @param GenericDBQueryWrapperInterface $db
     * @param $company
     * @param $VATBusPostingGroup
     * @param $shopPricesIncludeVAT
     */
    public function __construct(
        GenericDBQueryWrapperInterface $db,
        $company,
        $VATBusPostingGroup,
        $shopPricesIncludeVAT
    ) {
        $this->db = $db;
        $this->company = $company;
        $this->VATBusPostingGroup = $VATBusPostingGroup;
        $this->shopPricesIncludeVAT = (bool)$shopPricesIncludeVAT;
    }

    /**#
     * @param $VATProdPostingGroup
     * @return float
     * @throw \DomainException
     */
    public function getVATPercentForProdPostingGroup($VATProdPostingGroup)
    {
        $VATProdPostingGroup = (string)$VATProdPostingGroup;
        if (array_key_exists($VATProdPostingGroup,$this->vatPercentForPostingGroupMemo)) {
            return $this->vatPercentForPostingGroupMemo[$VATProdPostingGroup];
        }

        $VATQuery = "SELECT vat_percent
                      FROM shop_vat_posting_setup
                      WHERE company = " . $this->db->escapeString($this->company) . "
                        AND vat_bus_posting_group = " . $this->db->escapeString($this->VATBusPostingGroup) . "
                        AND vat_prod_posting_group = " . $this->db->escapeString($VATProdPostingGroup);
        $this->db->setQuery($VATQuery);
        $this->db->doQuery();
        $noOfRows = $this->db->getNoOfReturnedRows();
        if ($noOfRows !== 1) {
            throw new \DomainException(
                "VAT-Setup query for company '{$this->company}', vat_bus_posting_group '{$this->VATBusPostingGroup}', vat_prod_posting_group '{$VATProdPostingGroup}' did not return exactly one row"
            );
        }
        $resultArray = $this->db->getResultArray();
        $VATPercent = (float)$resultArray[0][0];
        $this->vatPercentForPostingGroupMemo[$VATProdPostingGroup] = $VATPercent;
        return $VATPercent;
    }

    /**#
     * @param ItemPriceData $itemPriceData
     * @return float
     * @throw \DomainException
     */
    public function getVATPercentForProdPostingGroupFromPriceData($itemPriceData)
    {
        $VATProdPostingGroup = (string)$itemPriceData->getItemVATProdPostingGroup();
        $priceVATBusPostingGroup = (string)$itemPriceData->getPriceVATBusPostingGroup();

        if (array_key_exists($VATProdPostingGroup.'|'.$priceVATBusPostingGroup,$this->vatPercentForPricePostingGroupMemo)) {
            return $this->vatPercentForPricePostingGroupMemo[$VATProdPostingGroup.'|'.$priceVATBusPostingGroup];
        }

        $VATQuery = "SELECT vat_percent
                      FROM shop_vat_posting_setup
                      WHERE company = " . $this->db->escapeString($this->company) . "
                        AND vat_bus_posting_group = " . $this->db->escapeString($priceVATBusPostingGroup) . "
                        AND vat_prod_posting_group = " . $this->db->escapeString($VATProdPostingGroup);
        $this->db->setQuery($VATQuery);
        $this->db->doQuery();
        $noOfRows = $this->db->getNoOfReturnedRows();
        if ($noOfRows !== 1) {
            throw new \DomainException(
                "VAT-Setup query for company '{$this->company}', vat_bus_posting_group '{$priceVATBusPostingGroup}', vat_prod_posting_group '{$VATProdPostingGroup}' did not return exactly one row"
            );
        }
        $resultArray = $this->db->getResultArray();
        $VATPercent = (float)$resultArray[0][0];
        $this->vatPercentForPricePostingGroupMemo[$VATProdPostingGroup.'|'.$priceVATBusPostingGroup] = $VATPercent;
        return $VATPercent;
    }

    /**
     * @return string
     */
    public function getVATBusPostingGroup()
    {
        return $this->VATBusPostingGroup;
    }

    /**
     * @param ItemPriceData $priceData
     */
    public function doVATCalculationOnItemPrice(ItemPriceData $priceData)
    {
        if ($priceData->getVATAdjustmentStatus() !== ItemPriceData::VAT_ADJUSTMENT_STATUS_UNADJUSTED) {
            return;
        }
        $outputToIncludeVAT = $this->shopPricesIncludeVAT;
        $oldPrice = $priceData->price;
        $taxAmnt = 0.00;
        $priceVATPercent = 0.00;
        if ($oldPrice !== 0.00) {
            $newPrice = $oldPrice;
            try {
                $priceVATPercent = $this->getVATPercentForProdPostingGroup($priceData->getItemVATProdPostingGroup());
                $priceVATPercentPriceData = $this->getVATPercentForProdPostingGroupFromPriceData($priceData);

                if (!$priceData->priceIncludesVAT) {
                    $taxAmnt = $this->getTaxAmountToAdd($oldPrice, $priceVATPercent);
                    if ($outputToIncludeVAT) {
                        $newPrice = $oldPrice + $taxAmnt;
                    }
                } elseif ($priceData->priceIncludesVAT) {

                    if ((string)$priceVATPercent !== (string)$priceVATPercentPriceData) {
                        $taxAmntPriceData = $this->getIncludedTaxAmount($oldPrice, $priceVATPercentPriceData);
                        $newPriceWithoutTax = $oldPrice - $taxAmntPriceData;
                        if (!$outputToIncludeVAT) {
                            $newPrice = $newPriceWithoutTax;
                        } else {
                            $taxAmnt = $this->getIncludedTaxAmount($oldPrice, $priceVATPercent);
                            $newPrice = $newPriceWithoutTax + $taxAmnt;
                        }
                    } else {
                        $taxAmnt = $this->getIncludedTaxAmount($oldPrice, $priceVATPercent);
                        if (!$outputToIncludeVAT) {
                            $newPrice = $oldPrice - $taxAmnt;
                        }
                    }
                }
            } catch (\Exception $e) {
                //Log if needed
                $newPrice = $priceData->price;
            }
            $priceData->setVATPercent($priceVATPercent);
            $priceData->setVATAmount($taxAmnt);
            if ($oldPrice > $newPrice) {
                $priceData->setPrice($newPrice);
                $priceData->setVATAdjustmentStatusVATSubtracted();
                $priceData->setPriceIncludesVAT(false);
            } elseif ($oldPrice < $newPrice) {
                $priceData->setPrice($newPrice);
                $priceData->setVATAdjustmentStatusVATAdded();
                $priceData->setPriceIncludesVAT(true);
            }
        } else {
            $priceData->setVATAmount(0.00);
        }
    }

    /**
     * @param $price
     * @param $taxPercentIncluded
     * @return float|int
     */
    public function subtractIncludedTaxFromPrice($price, $taxPercentIncluded)
    {
        return (($price / (100 + $taxPercentIncluded)) * $taxPercentIncluded);
    }

    /**
     * @param $price
     * @param $taxPercentToAdd
     * @return mixed
     */
    public function addTaxToPrice($price, $taxPercentToAdd)
    {
        return ($price + (($taxPercentToAdd / 100) * $price));
    }

    /**
     * @param $price
     * @param $taxPercentIncluded
     * @return float|int
     */
    public function getIncludedTaxAmount($price, $taxPercentIncluded)
    {
        $taxAmnt = (($price / (100 + $taxPercentIncluded)) * $taxPercentIncluded);
        return $taxAmnt;
    }

    /**
     * @param $price
     * @param $taxPercentToAdd
     * @return float|int
     */
    public function getTaxAmountToAdd($price, $taxPercentToAdd)
    {
        return ($taxPercentToAdd / 100) * $price;
    }

    /**
     * @return bool
     */
    public function isOutputVATIncluded()
    {
        return $this->shopPricesIncludeVAT;
    }

    /**
     * @param $price
     * @param $taxPercent
     * @return float|int
     */
    public function getTaxAmountForShopVATSetting($price, $taxPercent)
    {
        if ($this->shopPricesIncludeVAT) {
            return $this->getIncludedTaxAmount($price, $taxPercent);
        } else {
            return $this->getTaxAmountToAdd($price, $taxPercent);
        }
    }

    /**
     * @return bool
     */
    public function isOutputIncludingVAT()
    {
        return (bool)$this->shopPricesIncludeVAT;
    }


}