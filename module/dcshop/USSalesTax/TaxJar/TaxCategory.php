<?php
namespace DynCom\dc\dcShop\USSalesTax\TaxJar;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 06.06.2017
 * Time: 11:39
 */
class TaxCategory implements \JsonSerializable
{
    protected $name;
    protected $productTaxCode;
    protected $description;

    /**
     * TaxCategory constructor.
     * @param string $name
     * @param string $productTaxCode
     * @param string $description
     */
    public function __construct(string $name, string $productTaxCode, string $description)
    {
        $this->name = $name;
        $this->productTaxCode = $productTaxCode;
        $this->description = $description;
    }

    public function toArray(?array $keyMap = null): array
    {
        $keyMap = (array)$keyMap;
        $arr = [];

        $keyName = $keyMap['name'] ?? 'name';
        if ('' !== $keyName) {
            $arr[$keyName] = $this->name;
        }

        $keyProductTaxCode = $keyMap['productTaxCode'] ?? 'product_tax_code';
        if ('' !== $keyProductTaxCode) {
            $arr[$keyProductTaxCode] = $this->productTaxCode;
        }

        $keyDescription = $keyMap['description'] ?? 'description';
        if ('' !== $keyDescription) {
            $arr[$keyDescription] = $this->description;
        }
        return $arr;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getProductTaxCode(): string
    {
        return $this->productTaxCode;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public static function fromArray(array $arr): self
    {
        $name = (string)($arr['name'] ?? '');
        $productTaxCode = (string)($arr['product_tax_code'] ?? $arr['productTaxCode'] ?? '');
        $description = (string)($arr['description'] ?? '');
        $taxCategory = new TaxCategory($name,$productTaxCode,$description);
        return $taxCategory;
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