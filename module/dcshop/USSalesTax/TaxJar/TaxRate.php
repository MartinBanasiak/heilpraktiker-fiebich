<?php
namespace DynCom\dc\dcShop\USSalesTax\TaxJar;
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 06.06.2017
 * Time: 11:38
 */
class TaxRate implements \JsonSerializable
{
    protected $zip;
    protected $state;
    protected $stateRate;
    protected $county;
    protected $countyRate;
    protected $city;
    protected $cityRate;
    protected $combinedDistrictRate;
    protected $combinedRate;
    protected $freightTaxable;

    /**
     * Rate constructor.
     * @param string $zip
     * @param string $state
     * @param float $stateRate
     * @param string $county
     * @param float $countyRate
     * @param string $city
     * @param float $cityRate
     * @param float $combinedDistrictRate
     * @param float $combinedRate
     * @param bool $freightTaxable
     */
    public function __construct(
        string $zip,
        string $state,
        float $stateRate,
        string $county,
        float $countyRate,
        string $city,
        float $cityRate,
        float $combinedDistrictRate,
        float $combinedRate,
        bool $freightTaxable
    ) {
        $this->zip = $zip;
        $this->state = $state;
        $this->stateRate = $stateRate;
        $this->county = $county;
        $this->countyRate = $countyRate;
        $this->city = $city;
        $this->cityRate = $cityRate;
        $this->combinedDistrictRate = $combinedDistrictRate;
        $this->combinedRate = $combinedRate;
        $this->freightTaxable = $freightTaxable;
    }

    /**
     * @return string
     */
    public function getZip(): string
    {
        return $this->zip;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @return float
     */
    public function getStateRate(): float
    {
        return $this->stateRate;
    }

    /**
     * @return string
     */
    public function getCounty(): string
    {
        return $this->county;
    }

    /**
     * @return float
     */
    public function getCountyRate(): float
    {
        return $this->countyRate;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return float
     */
    public function getCityRate(): float
    {
        return $this->cityRate;
    }

    /**
     * @return float
     */
    public function getCombinedDistrictRate(): float
    {
        return $this->combinedDistrictRate;
    }

    /**
     * @return float
     */
    public function getCombinedRate(): float
    {
        return $this->combinedRate;
    }

    /**
     * @return bool
     */
    public function isFreightTaxable(): bool
    {
        return $this->freightTaxable;
    }

    public function toArray(?array $keyMap = null): array
    {
        $keyMap = (array)$keyMap;
        $arr = [];

        $keyZip = $keyMap['zip'] ?? 'zip';
        if ('' !== $keyZip) {
            $arr[$keyZip] = $this->getZip();
        }

        $keyState = $keyMap['state'] ?? 'state';
        if ('' !== $keyState) {
            $arr[$keyState] = $this->getState();
        }

        $keyStateRate = $keyMap['stateRate'] ?? 'state_rate';
        if ('' !== $keyStateRate) {
            $arr[$keyStateRate] = $this->getStateRate();
        }

        $keyCounty = $keyMap['county'] ?? 'county';
        if ('' !== $keyCounty) {
            $arr[$keyCounty] = $this->getCounty();
        }

        $keyCountyRate = $keyMap['countyRate'] ?? 'county_rate';
        if ('' !== $keyCountyRate) {
            $arr[$keyCountyRate] = $this->getCountyRate();
        }

        $keyCity = $keyMap['city'] ?? 'city';
        if ('' !== $keyCity) {
            $arr[$keyCity] = $this->getCity();
        }

        $keyCityRate = $keyMap['cityRate'] ?? 'city_rate';
        if ('' !== $keyCityRate) {
            $arr[$keyCity] = $this->getCityRate();
        }

        $keyCombinedDistrictRate = $keyMap['combinedDistrictRate'] ?? 'combined_district_rate';
        if ('' !== $keyCombinedDistrictRate) {
            $arr[$keyCombinedDistrictRate] = $this->getCombinedDistrictRate();
        }

        $keyCombinedRate = $keyMap['combinedRate'] ?? 'combined_rate';
        if ('' !== $keyCombinedRate) {
            $arr[$keyCombinedRate] = $this->getCombinedRate();
        }

        $keyFreightTaxable = $keyMap['freightTaxable'] ?? 'freight_taxable';
        if ('' !== $keyFreightTaxable) {
            $arr[$keyFreightTaxable] = $this->isFreightTaxable();
        }

        return $arr;

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

    public static function fromArray(array $arr): self
    {

        $zip = $arr['zip'] ?? '';
        $state = $arr['state'] ?? '';
        $stateRate = $arr['state_rate'] ?? $arr['stateRate'] ?? 0.00;
        $stateRate = (float)$stateRate;
        $county = $arr['county'] ?? '';
        $countyRate = $arr['county_rate'] ?? $arr['countyRate'] ?? 0.00;
        $countyRate = (float)$countyRate;
        $city = $arr['city'] ?? '';
        $cityRate = $arr['city_rate'] ?? $arr['cityRate'] ?? 0.00;
        $cityRate = (float)$cityRate;
        $combinedDistrictRate = $arr['combined_district_rate'] ?? $arr['combinedDistrictRate'] ?? 0.00;
        $combinedDistrictRate = (float)$combinedDistrictRate;
        $combinedRate = $arr['combined_rate'] ?? $arr['combinedRate'] ?? 0.00;
        $combinedRate = (float)$combinedRate;
        $isFreightTaxable = $arr['freight_taxable'] ?? $arr['freightTaxable'] ?? false;

        $taxRate = new TaxRate(
            $zip,
            $state,
            $stateRate,
            $county,
            $countyRate,
            $city,
            $cityRate,
            $combinedDistrictRate,
            $combinedRate,
            $isFreightTaxable
        );
        return $taxRate;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }


}