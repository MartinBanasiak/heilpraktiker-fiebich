<?php
namespace DynCom\dc\dcShop\USSalesTax\TaxJar;
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 06.06.2017
 * Time: 11:38
 */
class TaxBreakdown implements \JsonSerializable
{
    /**
     * @var float
     */
    protected $taxableAmount;
    /**
     * @var float
     */
    protected $taxCollectable;
    /**
     * @var float
     */
    protected $combinedTaxRate;
    /**
     * @var float
     */
    protected $stateTaxableAmount;
    /**
     * @var float
     */
    protected $stateTaxRate;
    /**
     * @var float
     */
    protected $stateTaxCollectable;
    /**
     * @var float
     */
    protected $countyTaxableAmount;
    /**
     * @var float
     */
    protected $countyTaxRate;
    /**
     * @var float
     */
    protected $countyTaxCollectable;
    /**
     * @var float
     */
    protected $cityTaxableAmount;
    /**
     * @var float
     */
    protected $cityTaxRate;
    /**
     * @var float
     */
    protected $cityTaxCollectable;
    /**
     * @var float
     */
    protected $specialDistrictTaxableAmount;
    /**
     * @var float
     */
    protected $specialTaxRate;
    /**
     * @var float
     */
    protected $specialDistrictTaxCollectable;
    /**
     * @var array
     */
    protected $lineItems;

    /**
     * TaxBreakdown constructor.
     * @param float $taxableAmount
     * @param float $taxCollectable
     * @param float $combinedTaxRate
     * @param float $stateTaxableAmount
     * @param float $stateTaxRate
     * @param float $stateTaxCollectable
     * @param float $countyTaxableAmount
     * @param float $countyTaxRate
     * @param float $countyTaxCollectable
     * @param float $cityTaxableAmount
     * @param float $cityTaxRate
     * @param float $cityTaxCollectable
     * @param float $specialDistrictTaxableAmount
     * @param float $specialTaxRate
     * @param float $specialDistrictTaxCollectable
     * @param array $lineItems
     */
    public function __construct(
        float $taxableAmount,
        float $taxCollectable,
        float $combinedTaxRate,
        float $stateTaxableAmount,
        float $stateTaxRate,
        float $stateTaxCollectable,
        float $countyTaxableAmount,
        float $countyTaxRate,
        float $countyTaxCollectable,
        float $cityTaxableAmount,
        float $cityTaxRate,
        float $cityTaxCollectable,
        float $specialDistrictTaxableAmount,
        float $specialTaxRate,
        float $specialDistrictTaxCollectable,
        array $lineItems
    ) {
        $this->taxableAmount = $taxableAmount;
        $this->taxCollectable = $taxCollectable;
        $this->combinedTaxRate = $combinedTaxRate;
        $this->stateTaxableAmount = $stateTaxableAmount;
        $this->stateTaxRate = $stateTaxRate;
        $this->stateTaxCollectable = $stateTaxCollectable;
        $this->countyTaxableAmount = $countyTaxableAmount;
        $this->countyTaxRate = $countyTaxRate;
        $this->countyTaxCollectable = $countyTaxCollectable;
        $this->cityTaxableAmount = $cityTaxableAmount;
        $this->cityTaxRate = $cityTaxRate;
        $this->cityTaxCollectable = $cityTaxCollectable;
        $this->specialDistrictTaxableAmount = $specialDistrictTaxableAmount;
        $this->specialTaxRate = $specialTaxRate;
        $this->specialDistrictTaxCollectable = $specialDistrictTaxCollectable;
        $this->lineItems = $lineItems;
    }

    /**
     * @return float
     */
    public function getTaxableAmount(): float
    {
        return $this->taxableAmount;
    }

    /**
     * @return float
     */
    public function getTaxCollectable(): float
    {
        return $this->taxCollectable;
    }

    /**
     * @return float
     */
    public function getCombinedTaxRate(): float
    {
        return $this->combinedTaxRate;
    }

    /**
     * @return float
     */
    public function getStateTaxableAmount(): float
    {
        return $this->stateTaxableAmount;
    }

    /**
     * @return float
     */
    public function getStateTaxRate(): float
    {
        return $this->stateTaxRate;
    }

    /**
     * @return float
     */
    public function getStateTaxCollectable(): float
    {
        return $this->stateTaxCollectable;
    }

    /**
     * @return float
     */
    public function getCountyTaxableAmount(): float
    {
        return $this->countyTaxableAmount;
    }

    /**
     * @return float
     */
    public function getCountyTaxRate(): float
    {
        return $this->countyTaxRate;
    }

    /**
     * @return float
     */
    public function getCountyTaxCollectable(): float
    {
        return $this->countyTaxCollectable;
    }

    /**
     * @return float
     */
    public function getCityTaxableAmount(): float
    {
        return $this->cityTaxableAmount;
    }

    /**
     * @return float
     */
    public function getCityTaxRate(): float
    {
        return $this->cityTaxRate;
    }

    /**
     * @return float
     */
    public function getCityTaxCollectable(): float
    {
        return $this->cityTaxCollectable;
    }

    /**
     * @return float
     */
    public function getSpecialDistrictTaxableAmount(): float
    {
        return $this->specialDistrictTaxableAmount;
    }

    /**
     * @return float
     */
    public function getSpecialTaxRate(): float
    {
        return $this->specialTaxRate;
    }

    /**
     * @return float
     */
    public function getSpecialDistrictTaxCollectable(): float
    {
        return $this->specialDistrictTaxCollectable;
    }

    /**
     * @return array
     */
    public function getLineItems(): array
    {
        return $this->lineItems;
    }

    public function toArray(?array $keyMap = null): array
    {
        $arr = [];
        $taxableAmountKey = $keyMap['taxableAmount'] ?? 'taxable_amount';
        if ('' !== $taxableAmountKey) {
            $arr[$taxableAmountKey] = $this->getTaxableAmount();
        }

        $taxCollectableKey = $keyMap['taxCollectable'] ?? 'tax_collectable';
        if ('' !== $taxCollectableKey) {
            $arr[$taxCollectableKey] = $this->getTaxCollectable();
        }

        $combinedTaxRateKey = $keyMap['combinedTaxRate'] ?? 'combined_tax_rate';
        if ('' !== $combinedTaxRateKey) {
            $arr[$combinedTaxRateKey] = $this->getCombinedTaxRate();
        }

        $stateTaxableAmountKey = $keyMap['stateTaxableAmount'] ?? 'state_taxable_amount';
        if ('' !== $stateTaxableAmountKey) {
            $arr[$stateTaxableAmountKey] = $this->getStateTaxableAmount();
        }

        $stateTaxRateKey = $keyMap['stateTaxRate'] ?? 'state_tax_rate';
        if ('' !== $stateTaxRateKey) {
            $arr[$stateTaxRateKey] = $this->getStateTaxRate();
        }

        $stateTaxCollectableKey = $keyMap['stateTaxCollectable'] ?? 'state_tax_collectable';
        if ('' !== $stateTaxCollectableKey) {
            $ar[$stateTaxCollectableKey] = $this->getStateTaxCollectable();
        }

        $countyTaxableAmountKey = $keyMap['countyTaxableAmount'] ?? 'county_taxable_amount';
        if ('' !== $countyTaxableAmountKey) {
            $arr[$countyTaxableAmountKey] = $this->getCountyTaxableAmount();
        }

        $countyTaxRateKey = $keyMap['countyTaxRate'] ?? 'county_tax_rate';
        if ('' !== $countyTaxRateKey) {
            $arr[$countyTaxRateKey] = $this->getCountyTaxRate();
        }

        $countyTaxCollectableKey = $keyMap['countyTaxCollectable'] ?? 'county_tax_collectable';
        if ('' !== $countyTaxCollectableKey) {
            $arr[$countyTaxCollectableKey] = $this->getCountyTaxCollectable();
        }

        $specialDistrictTaxableAmountKey = $keyMap['specialDistrictTaxableAmount'] ?? 'special_district_taxable_amount';
        if ('' !== $specialDistrictTaxableAmountKey) {
            $arr[$specialDistrictTaxableAmountKey] = $this->getSpecialDistrictTaxableAmount();
        }

        $specialTaxRateKey = $keyMap['specialTaxRate'] ?? 'special_tax_rate';
        if ('' !== $specialTaxRateKey) {
            $arr[$specialTaxRateKey] = $this->getSpecialTaxRate();
        }

        $specialDistrictTaxCollectableKey = $keyMap['specialDistrictTaxCollectable'] ?? 'special_district_tax_collectable';
        if ('' !== $specialDistrictTaxCollectableKey) {
            $arr[$specialDistrictTaxCollectableKey] = $this->getSpecialDistrictTaxCollectable();
        }

        $lineItemsKey = $keyMap['lineItems'] ?? 'line_items';
        if ('' !== $lineItemsKey) {
            $linesKeyName = is_string($lineItemsKey) ? $lineItemsKey : 'line_items';
            $lineKeyMap = null;
            if (is_array($lineItemsKey)) {
                $lineKeyMap = $lineItemsKey;
            }
            $lineItemArr = [];
            /**
             * @var $lineItem TaxLineItem
             */
            foreach ($this->lineItems as $lineItem) {

                $lineItemArr[] = $lineItem->toArray($lineKeyMap);
            }
            $arr[$linesKeyName] = $lineItemArr;
        }
        return $arr;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public static function fromArray(?array $arr): self
    {
        $arr = (array)$arr;
        $taxableAmount = (float)($arr['taxable_amount'] ?? $arr['taxableAmount'] ?? 0.00);
        $taxCollectable = (float)($arr['tax_collectable'] ?? $arr['taxCollectable'] ?? 0.00);
        $combinedTaxRate = (float)($arr['combined_tax_rate'] ?? $arr['combinedTaxRate'] ?? 0.00);
        $stateTaxableAmount = (float)($arr['state_taxable_amount'] ?? $arr['stateTaxableAmount'] ?? 0.00);
        $stateTaxRate = (float)($arr['state_tax_rate'] ?? $arr['stateTaxRate'] ?? 0.00);
        $stateTaxCollectable = (float)($arr['state_tax_collectable'] ?? $arr['stateTaxCollectable'] ?? 0.00);
        $countyTaxableAmount= (float)($arr['county_taxable_amount'] ?? $arr['countyTaxableAmount'] ?? 0.00);
        $countyTaxRate = (float)($arr['county_tax_rate'] ?? $arr['countyTaxRate'] ?? 0.00);
        $countyTaxCollectable = (float)($arr['county_tax_collectable'] ?? $arr['countyTaxCollectable'] ?? 0.00);
        $cityTaxableAmount = (float)($arr['city_taxable_amount'] ?? $arr['cityTaxableAmount'] ?? 0.00);
        $cityTaxRate = (float)($arr['city_tax_rate'] ?? $arr['cityTaxRate'] ?? 0.00);
        $cityTaxCollectable = (float)($arr['city_tax_collectable'] ?? $arr['cityTaxCollectable'] ?? 0.00);
        $specialDistrictTaxableAmount = (float)($arr['special_district_taxable_amount'] ?? $arr['specialDistrictTaxableAmount'] ?? 0.00);
        $specialTaxRate = (float)($arr['special_tax_rate'] ?? $arr['specialTaxRate'] ?? 0.00);
        $specialDistrictTaxCollectable= (float)($arr['special_district_tax_collectable'] ?? $arr['specialDistrictTaxCollectable'] ?? 0.00);
        $lineItems = [];
        $arrLineItems = $arr['line_items'] ?? $arr['lineItems'] ?? [];
        $arrLineItems = (array)$arrLineItems;
        foreach ($arrLineItems as $lineItemArr)
        {
            $lineItem = TaxLineItem::fromArray($lineItemArr);
            $lineItems[] = $lineItem;
        }
        $taxBreakdown = new TaxBreakdown($taxableAmount,$taxCollectable,$combinedTaxRate,$stateTaxableAmount,$stateTaxRate,$stateTaxCollectable,$countyTaxableAmount,$countyTaxRate,$countyTaxCollectable,$cityTaxableAmount,$cityTaxRate,$cityTaxCollectable,$specialDistrictTaxableAmount,$specialTaxRate,$specialDistrictTaxCollectable,$lineItems);
        return $taxBreakdown;

    }

    public static function fromJSON(string $json): self
    {
        try {
            $arr = json_decode($json, true);
        } catch (Throwable $t) {
            $jsonLastErrorCode = json_last_error();
            $jsonLastErrorMsg = json_last_error_msg();
            if (!$jsonLastErrorMsg) {
                $jsonLastErrorMsg = $t->getMessage();
            }
            throw new InvalidArgumentException($jsonLastErrorMsg,$jsonLastErrorCode);
        }
        return self::fromArray($arr);
    }


}