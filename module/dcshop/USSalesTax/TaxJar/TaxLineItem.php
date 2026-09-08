<?php
namespace DynCom\dc\dcShop\USSalesTax\TaxJar;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 06.06.2017
 * Time: 11:37
 */
class TaxLineItem implements \JsonSerializable
{
    /**
     * @var string
     */
    protected $id;
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
    protected $stateSalesTaxRate;
    /**
     * @var float
     */
    protected $stateAmount;
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
    protected $countyAmount;
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
    protected $cityAmount;
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
    protected $specialDistrictAmount;

    /**
     * TaxLineItem constructor.
     * @param string $id
     * @param float $taxableAmount
     * @param float $taxCollectable
     * @param float $combinedTaxRate
     * @param float $stateTaxableAmount
     * @param float $stateSalesTaxRate
     * @param float $stateAmount
     * @param float $countyTaxableAmount
     * @param float $countyTaxRate
     * @param float $countyAmount
     * @param float $cityTaxableAmount
     * @param float $cityTaxRate
     * @param float $cityAmount
     * @param float $specialDistrictTaxableAmount
     * @param float $specialTaxRate
     * @param float $specialDistrictAmount
     */
    public function __construct(
        string $id,
        float $taxableAmount,
        float $taxCollectable,
        float $combinedTaxRate,
        float $stateTaxableAmount,
        float $stateSalesTaxRate,
        float $stateAmount,
        float $countyTaxableAmount,
        float $countyTaxRate,
        float $countyAmount,
        float $cityTaxableAmount,
        float $cityTaxRate,
        float $cityAmount,
        float $specialDistrictTaxableAmount,
        float $specialTaxRate,
        float $specialDistrictAmount
    ) {
        $this->id = $id;
        $this->taxableAmount = $taxableAmount;
        $this->taxCollectable = $taxCollectable;
        $this->combinedTaxRate = $combinedTaxRate;
        $this->stateTaxableAmount = $stateTaxableAmount;
        $this->stateSalesTaxRate = $stateSalesTaxRate;
        $this->stateAmount = $stateAmount;
        $this->countyTaxableAmount = $countyTaxableAmount;
        $this->countyTaxRate = $countyTaxRate;
        $this->countyAmount = $countyAmount;
        $this->cityTaxableAmount = $cityTaxableAmount;
        $this->cityTaxRate = $cityTaxRate;
        $this->cityAmount = $cityAmount;
        $this->specialDistrictTaxableAmount = $specialDistrictTaxableAmount;
        $this->specialTaxRate = $specialTaxRate;
        $this->specialDistrictAmount = $specialDistrictAmount;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
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
    public function getStateSalesTaxRate(): float
    {
        return $this->stateSalesTaxRate;
    }

    /**
     * @return float
     */
    public function getStateAmount(): float
    {
        return $this->stateAmount;
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
    public function getCountyAmount(): float
    {
        return $this->countyAmount;
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
    public function getCityAmount(): float
    {
        return $this->cityAmount;
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
    public function getSpecialDistrictAmount(): float
    {
        return $this->specialDistrictAmount;
    }

    public function toArray(?array $keyMap = null): array
    {
        $arr = [];
        $idKey = $keyMap['id'] ?? 'id';
        if ('' !== $idKey) {
            $arr[$idKey] = $this->getId();
        }

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

        $stateSalesTaxRateKey = $keyMap['stateSalesTaxRate'] ?? 'state_sales_tax_rate';
        if ('' !== $stateSalesTaxRateKey) {
            $arr[$stateSalesTaxRateKey] = $this->getStateSalesTaxRate();
        }

        $stateAmountKey = $keyMap['stateAmount'] ?? 'state_amount';
        if ('' !== $stateAmountKey) {
            $arr[$stateAmountKey] = $this->getStateAmount();
        }

        $countyTaxableAmountKey = $keyMap['countyTaxableAmount'] ?? 'county_taxable_amount';
        if ('' !== $countyTaxableAmountKey) {
            $arr[$countyTaxableAmountKey] = $this->getCountyTaxableAmount();
        }

        $countyTaxRateKey = $keyMap['countyTaxRate'] ?? 'county_tax_rate';
        if ('' !== $countyTaxRateKey) {
            $arr[$countyTaxRateKey] = $this->getCountyTaxRate();
        }

        $countyAmountKey = $keyMap['countyAmount'] ?? 'county_amount';
        if ('' !== $countyAmountKey) {
            $arr[$countyAmountKey] = $this->getCountyAmount();
        }

        $cityTaxableAmountKey = $keyMap['cityTaxableAmount'] ?? 'city_taxable_amount';
        if ('' !== $cityTaxableAmountKey) {
            $arr[$cityTaxableAmountKey] = $this->getCityTaxableAmount();
        }

        $cityTaxRateKey = $keyMap['cityTaxRate'] ?? 'city_tax_rate';
        if ('' !== $cityTaxRateKey) {
            $arr[$cityTaxRateKey] = $this->getCityTaxRate();
        }

        $cityAmountKey = $keyMap['cityAmount'] ?? 'city_amount';
        if ('' !== $cityAmountKey) {
            $arr[$cityAmountKey] = $this->getCityAmount();
        }

        $specialDistrictTaxableAmountKey = $keyMap['specialDistrictTaxableAmount'] ?? 'special_district_taxable_amount';
        if ('' !== $specialDistrictTaxableAmountKey) {
            $arr[$specialDistrictTaxableAmountKey] = $this->getSpecialDistrictTaxableAmount();
        }

        $specialTaxRateKey = $keyMap['specialTaxRate'] ?? 'special_tax_rate';
        if ('' !== $specialTaxRateKey) {
            $arr[$specialTaxRateKey] = $this->getSpecialTaxRate();
        }

        $specialDistrictAmountKey = $keyMap['specialDistrictAmount'] ?? 'special_district_amount';
        if ('' !== $specialDistrictAmountKey) {
            $arr[$specialDistrictAmountKey] = $this->getSpecialDistrictAmount();
        }
        return $arr;


    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public static function fromArray(array $arr): self
    {
        $id = $arr['id'] ?? null;
        if (null !== $id) {
            $id = (string)$id;
        }
        $taxableAmount = (float)($arr['taxable_amount'] ?? $arr['taxableAmount'] ?? 0.00);
        $taxCollectable = (float)($arr['tax_collectable'] ?? $arr['taxCollectable'] ?? 0.00);
        $combinedTaxRate = (float)($arr['combined_tax_rate'] ?? $arr['combinedTaxRate'] ?? 0.00);
        $stateTaxableAmount = (float)($arr['state_taxable_amount'] ?? $arr['stateTaxbleAmount'] ?? 0.00);
        $stateSalesTaxRate = (float)($arr['state_sales_tax_rate'] ?? $arr['stateSalesTaxRate'] ?? 0.00);
        $stateAmount = (float)($arr['state_amount'] ?? $arr['stateAmount'] ?? 0.00);
        $countyTaxableAmount= (float)($arr['county_taxable_amount'] ?? $arr['countyTaxableAmount'] ?? 0.00);
        $countyTaxRate = (float)($arr['county_tax_rate'] ?? $arr['countyTaxRate'] ?? 0.00);
        $countyAmount = (float)($arr['county_amount'] ?? $arr['countyAmount'] ?? 0.00);
        $cityTaxableAmount= (float)($arr['city_taxable_amount'] ?? $arr['cityTaxableAmount'] ?? 0.00);
        $cityTaxRate = (float)($arr['city_tax_rate'] ?? $arr['cityTaxRate'] ?? 0.00);
        $cityAmount = (float)($arr['city_amount'] ?? $arr['cityAmount'] ?? 0.00);
        $specialDistrictTaxableAmount = (float)($arr['special_district_taxable_amount'] ?? $arr['specialDistrictTaxableAmount'] ?? 0.00);
        $specialTaxRate = (float)($arr['special_tax_rate'] ?? $arr['specialTaxRate'] ?? 0.00);
        $specialDistrictAmount = (float)($arr['special_district_amount'] ?? $arr['specialDistrictAmount'] ?? 0.00);

        $taxLineItem = new TaxLineItem($id,$taxableAmount,$taxCollectable,$combinedTaxRate,$stateTaxableAmount,$stateSalesTaxRate,$stateAmount,$countyTaxableAmount,$countyTaxRate,$countyAmount,$cityTaxableAmount,$cityTaxRate,$cityAmount,$specialDistrictTaxableAmount,$specialTaxRate,$specialDistrictAmount);
        return $taxLineItem;
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