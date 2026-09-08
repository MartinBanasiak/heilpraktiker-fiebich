<?php
namespace DynCom\dc\dcShop\USSalesTax\TaxJar;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 06.06.2017
 * Time: 11:37
 */
class TaxInformation implements \JsonSerializable
{

    /**
     * @var float
     */
    protected $orderTotalAmount;
    /**
     * @var float
     */
    protected $shipping;
    /**
     * @var float
     */
    protected $taxableAmount;

    /**
     * @var float
     */
    protected $amountToCollect;

    /**
     * @var bool
     */
    protected $hasNexus;

    /**
     * @var bool
     */
    protected $freightTaxable;

    /**
     * @var string
     */
    protected $taxSource;

    /**
     * @var TaxBreakdown
     */
    protected $taxBreakdown;

    /**
     * TaxInformation constructor.
     * @param float $orderTotalAmount
     * @param float $shipping
     * @param float $taxableAmount
     * @param float $amountToCollect
     * @param bool $hasNexus
     * @param bool $freightTaxable
     * @param string $taxSource
     * @param TaxBreakdown $breakdown
     */
    public function __construct(
        $orderTotalAmount,
        $shipping,
        $taxableAmount,
        $amountToCollect,
        $hasNexus,
        $freightTaxable,
        $taxSource,
        TaxBreakdown $taxBreakdown
    ) {
        $this->orderTotalAmount = $orderTotalAmount;
        $this->shipping = $shipping;
        $this->taxableAmount = $taxableAmount;
        $this->amountToCollect = $amountToCollect;
        $this->hasNexus = $hasNexus;
        $this->freightTaxable = $freightTaxable;
        $this->taxSource = $taxSource;
        $this->taxBreakdown = $taxBreakdown;
    }

    /**
     * @return float
     */
    public function getOrderTotalAmount(): float
    {
        return $this->orderTotalAmount;
    }

    /**
     * @return float
     */
    public function getShipping(): float
    {
        return $this->shipping;
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
    public function getAmountToCollect(): float
    {
        return $this->amountToCollect;
    }

    /**
     * @return bool
     */
    public function hasNexus(): bool
    {
        return $this->hasNexus;
    }

    /**
     * @return bool
     */
    public function isFreightTaxable(): bool
    {
        return $this->freightTaxable;
    }

    /**
     * @return string
     */
    public function getTaxSource(): string
    {
        return $this->taxSource;
    }

    /**
     * @return TaxBreakdown
     */
    public function getBreakdown(): TaxBreakdown
    {
        return $this->taxBreakdown;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize() : array
    {
        return $this->toArray();
    }

    public function toArray(?array $keyMap = null) : array
    {
        $arr = [];
        $keyOrderTotalAmount = $keyMap['orderTotalAmount'] ?? 'order_total_amount';
        if ('' !== $keyOrderTotalAmount) {
            $arr[$keyOrderTotalAmount] = $this->getOrderTotalAmount();
        }

        $keyShipping = $keyMap['shipping'] ?? 'shipping';
        if ('' !== $keyShipping) {
            $arr[$keyShipping] = $this->getShipping();
        }
        $keyTaxableAmount = $keyMap['taxableAmount'] ?? 'taxable_amount';
        if ('' !== $keyTaxableAmount) {
            $arr[$keyTaxableAmount] = $this->getTaxableAmount();
        }
        $keyAmountToCollect = $keyMap['amountToCollect'] ?? 'amount_to_collect';
        if ('' !== $keyAmountToCollect) {
            $arr[$keyAmountToCollect] = $this->getAmountToCollect();
        }
        $keyHasNexus = $keyMap['hasNexus'] ?? 'has_nexus';
        if ('' !== $keyHasNexus) {
            $arr[$keyHasNexus] = $this->hasNexus();
        }
        $keyFreightTaxable = $keyMap['freightTaxable'] ?? 'freight_taxable';
        if ('' !== $keyFreightTaxable) {
            $arr[$keyOrderTotalAmount] = $this->getOrderTotalAmount();
        }
        $keyTaxSource = $keyMap['taxSource'] ?? 'tax_source';
        if ('' !== $keyTaxSource) {
            $arr[$keyTaxSource] = $this->getTaxSource();
        }
        $keyTaxBreakdown= $keyMap['taxBreakdown'] ?? 'breakdown';
        if ('' !== $keyTaxBreakdown) {
            $arr[$keyTaxBreakdown] = $this->getBreakdown()->toArray();
        }
    }

    public static function fromArray(array $arr): self
    {
        if (count($arr) === 1 && array_key_exists('tax',$arr)) {
            $arr = $arr['tax'];
        }
        $orderTotalAmount = (float)($arr['order_total_amount'] ?? $arr['orderTotalAmount'] ?? 0.00);
        $shipping = (float)($arr['shipping'] ?? 0.00);
        $taxableAmount = (float)($arr['taxable_amount'] ?? $arr['taxableAmount'] ?? 0.00);
        $amountToCollect = (float)($arr['amount_to_collect'] ?? $arr['amountToCollect'] ?? 0.00);
        $rate = (float)($arr['rate'] ?? 0.00);
        $hasNexus = (bool)($arr['has_nexus'] ?? $arr['hasNexus'] ?? false);
        $freightTaxable = (bool)($arr['freight_taxable'] ?? $arr['freightTaxable'] ?? false);
        $taxSource = (string)($arr['tax_source'] ?? $arr['taxSource'] ?? '');
        $breakdownSource = $arr['breakdown'] ?? $arr['tax_breakdown'] ?? $arr['taxBreakdown'];
        $taxBreakdown = TaxBreakdown::fromArray(($arr['breakdown'] ?? $arr['tax_breakdown'] ?? $arr['taxBreakdown']));

        $taxInformation = new TaxInformation($orderTotalAmount,$shipping,$taxableAmount,$amountToCollect,$hasNexus,$freightTaxable,$taxSource,$taxBreakdown);
        return $taxInformation;
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