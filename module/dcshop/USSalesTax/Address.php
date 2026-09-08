<?php
namespace DynCom\dc\dcShop\USSalesTax;
use DynCom\dc\common\classes\NewValidator;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 06.06.2017
 * Time: 11:07
 */
class Address implements \JsonSerializable
{
    protected $countryCode;
    protected $zip;
    protected $state;
    protected $city;
    protected $streetAndNo;
    protected $lat;
    protected $lng;

    protected $validationSchema;

    public function __construct(string $countryCode, string $zip, string $state, string $city, string $streetAndNo, ?string $lat = null, ?string $lng = null, ?array $validationSchema = null, ?array $fieldStatusArray = null)
    {
        $this->countryCode = $countryCode;
        $this->zip = $zip;
        $this->state = $state;
        $this->city = $city;
        $this->streetAndNo = $streetAndNo;

        if ($validationSchema) {
            $fieldStatusArray = (array)$fieldStatusArray;
            $isValid = $this->isAddressValid($validationSchema,$fieldStatusArray);
            if (!$isValid) {
                throw new \InvalidArgumentException('Address is invalid according to the following rules [' . $validationSchema . ']');
            }
        }
    }

    public function isAddressValid(array $validationSchema, ?array &$fieldStatusArray): bool
    {
        static $validator;
        if (null === $validator) {
            $validator = new NewValidator($validationSchema);
        }
        $validator->setRules($validationSchema);
        $validator->setData($this->toArray());
        $fieldStatusArray = (array)$fieldStatusArray;
        return $validator->isValid($fieldStatusArray);
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
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
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getStreetAndNo(): string
    {
        return $this->streetAndNo;
    }

    public function toArray(?array $keyMap = null): array
    {
        $keyMap = (array)$keyMap;
        $arr = [];

        $keyCountryCode = $keyMap['countryCode'] ?? 'countryCode';
        if ('' !== $keyCountryCode) {
            $arr[$keyCountryCode] = $this->countryCode;
        }

        $keyZip = $keyMap['zip'] ?? 'zip';
        if ('' !== $keyZip) {
            $arr[$keyZip] = $this->zip;
        }
        $keyState = $keyMap['state'] ?? 'state';
        if ('' !== $keyState) {
            $arr[$keyState] = $this->state;
        }
        $keyCity = $keyMap['city'] ?? 'city';
        if ('' !== $keyCity) {
            $arr[$keyCity] = $this->city;
        }
        $keyStreetAndNo = $keyMap['streetAndNo'] ?? 'streetAndNo';
        if ('' !== $keyStreetAndNo) {
            $arr[$keyStreetAndNo] = $this->streetAndNo;
        }
        return $arr;

    }

    public function withLatLng(string $lat, string $lng, ?array $validationSchema = null, ?array &$fieldStatusArray = null): Address
    {
        $validationSchema = (array)($validationSchema ?? $this->validationSchema);
        return new static($this->countryCode,$this->zip,$this->state,$this->city,$this->streetAndNo,$lat,$lng,$validationSchema,$fieldStatusArray);
    }

    public static function fromJSON(string $json, ?array $validationSchema = null, ?array &$fieldStatusArray = null): self
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
        return self::fromArray($arr,$validationSchema,$fieldStatusArray);
    }

    public static function fromArray(array $arr, ?array $validationSchema = null, ?array &$fieldStatusArray = null): self
    {

        $countryCode = $arr['country_code'] ?? $arr['countryCode'] ?? '';
        $zip = $arr['zip'] ?? '';
        $state = $arr['state'] ?? '';
        $city = $arr['city'] ?? '';
        $streetAndNo = $arr['street_and_no'] ?? $arr['streetAndNo'] ?? $arr['street'] ?? '';
        $lat = $arr['lat'] ?? $arr['latitude'] ?? null;
        $lng = $arr['lng'] ?? $arr['longitude'] ?? null;

        $address = new Address($countryCode,$zip,$state,$city,$streetAndNo,$lat,$lng,$validationSchema,$fieldStatusArray);
        return $address;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }


}