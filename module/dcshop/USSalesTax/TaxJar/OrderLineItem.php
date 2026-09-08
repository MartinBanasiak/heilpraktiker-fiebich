<?php
namespace DynCom\dc\dcShop\USSalesTax\TaxJar;
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 06.06.2017
 * Time: 11:37
 */
class OrderLineItem implements \JsonSerializable
{
    protected $id;
    protected $quantity;
    protected $productIdentifier;
    protected $productTaxCode;
    protected $unitPrice;
    protected $discount;
    protected $salesTax;

    /**
     * OrderLineItem constructor.
     * @param string|null $id
     * @param int $quantity
     * @param null|string $productIdentifier
     * @param null|string $productTaxCode
     * @param float $unitPrice
     * @param float $discount
     * @param float|null $salesTax
     */
    public function __construct(?string $id, int $quantity, ?string $productIdentifier, ?string $productTaxCode, float $unitPrice, float $discount, ?float $salesTax)
    {
        $this->id = $id;
        $this->quantity = $quantity;
        $this->productIdentifier = $productIdentifier;
        $this->productTaxCode = $productTaxCode;
        $this->unitPrice = $unitPrice;
        $this->discount = $discount;
        $this->salesTax = $salesTax;
    }

    /**
     * @return string|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return null|string
     */
    public function getProductTaxCode(): ?string
    {
        return $this->productTaxCode;
    }

    /**
     * @return float
     */
    public function getUnitPrice(): float
    {
        return $this->unitPrice;
    }

    /**
     * @return float
     */
    public function getDiscount(): float
    {
        return $this->discount;
    }

    /**
     * @return float
     */
    public function getSalesTax(): ?float
    {
        return $this->salesTax;
    }

    /**
     * @return null|string
     */
    public function getProductIdentifier(): ?string
    {
        return $this->productIdentifier;
    }



    /**
     * @param float $salesTax
     * @return OrderLineItem
     */
    public function withSalesTax(float $salesTax): self
    {
        return new OrderLineItem($this->getId(),$this->getQuantity(),$this->getProductIdentifier(),$this->getProductTaxCode(),$this->getUnitPrice(),$this->getDiscount(),$this->getSalesTax());
    }

    /**
     * @param int $id
     * @return OrderLineItem
     */
    public function withID(int $id): self
    {
        return new OrderLineItem($id,$this->getQuantity(),$this->getProductIdentifier(),$this->getProductTaxCode(),$this->getUnitPrice(),$this->getDiscount(),$this->getSalesTax());
    }

    public function toArray(?array $keyMap = null): array
    {
        $arr = [];
        $idKey = $keyMap['id'] ?? 'id';
        if ('' !== $idKey) {
            $arr[$idKey] = $this->getId();
        }

        $quantityKey = $keyMap['quantity'] ?? 'quantity';
        if ('' !== $quantityKey) {
            $arr[$quantityKey] = $this->getQuantity();
        }

        $productIdentifierKey = $keyMap['productIdentifier'] ?? 'product_identifier';
        if ('' !== $productIdentifierKey) {
            $arr[$productIdentifierKey] = $this->getProductIdentifier();
        }

        $productTaxCodeKey = $keyMap['productTaxCode'] ?? 'product_tax_code';
        if ('' !== $productTaxCodeKey) {
            $arr[$productTaxCodeKey] = $this->getProductTaxCode();
        }

        $unitPriceKey = $keyMap['unitPrice'] ?? 'unit_price';
        if ('' !== $unitPriceKey) {
            $arr[$unitPriceKey] = $this->getUnitPrice();
        }

        $discountKey = $keyMap['discount'] ?? 'discount';
        if ('' !== $discountKey) {
            $arr[$discountKey] = $this->getDiscount();
        }

        $salesTaxKey = $keyMap['salesTax'] ?? 'sales_tax';
        if ('' !== $salesTaxKey) {
            $arr[$salesTaxKey] = $this->getSalesTax();
        }
        return $arr;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
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

    public static function fromArray(array $array): self
    {
        $id = $array['id'] ?? null;
        if (null !== $id) {
            $id = (int)$id;
        }
        $quantity = (int)($array['quantity'] ?? 0);
        $productIdentifier = $array['product_identifier'] ?? $array['productIdentifier'] ?? null;
        $description = $array['description'] ?? '';
        $unitPrice = (float)($array['unit_price'] ?? $array['unitPrice'] ?? 0.00);
        $discount = (float)($array['discount'] ?? 0.00);
        $salesTax = $array['sales_tax'] ?? null;
        if (null !== $salesTax) {
            $salesTax = (float)$salesTax;
        }
        $orderLineItem = new OrderLineItem($id,$quantity,$productIdentifier,$description,$unitPrice,$discount,$salesTax);
        return $orderLineItem;
    }




}