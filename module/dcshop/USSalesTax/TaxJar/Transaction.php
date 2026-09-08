<?php
namespace DynCom\dc\dcShop\USSalesTax\TaxJar;

use DynCom\dc\dcShop\USSalesTax\Address;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 06.06.2017
 * Time: 11:38
 */
class Transaction implements \JsonSerializable
{
    /**
     * @var string
     */
    protected $transactionID;

    /**
     * @var ?int
     */
    protected $userID;

    /**
     * @var DateTimeImmutable
     */
    protected $transactionDate;

    /**
     * @var ?string
     */
    protected $transactionReferenceID;

    /**
     * @var Address
     */
    protected $fromAddress;

    /**
     * @var Address
     */
    protected $toAddress;

    /**
     * @var float
     */
    protected $amount;

    /**
     * @var float
     */
    protected $shipping;

    /**
     * @var float
     */
    protected $salesTax;

    /**
     * @var array[OrderLineItems]
     */
    protected $lineItems;

    /**
     * Transaction constructor.
     * @param string $transactionID
     * @param int|null $userID
     * @param \DateTimeImmutable $transactionDate
     * @param null|string $transactionReferenceID
     * @param Address $fromAddress
     * @param Address $toAddress
     * @param float $amount
     * @param float $shipping
     * @param float $salesTax
     * @param array $lineItems
     */
    public function __construct(
        string $transactionID,
        ?int $userID,
        \DateTimeImmutable $transactionDate,
        ?string $transactionReferenceID,
        Address $fromAddress,
        Address $toAddress,
        float $amount,
        float $shipping,
        float $salesTax,
        array $lineItems
    ) {
        $this->transactionID = $transactionID;
        $this->userID = $userID;
        $this->transactionDate = $transactionDate;
        $this->transactionReferenceID = $transactionReferenceID;
        $this->fromAddress = $fromAddress;
        $this->toAddress = $toAddress;
        $this->amount = $amount;
        $this->shipping = $shipping;
        $this->salesTax = $salesTax;
        $this->lineItems = $lineItems;
    }

    /**
     * @return string
     */
    public function getTransactionID(): string
    {
        return $this->transactionID;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getTransactionDate(): \DateTimeImmutable
    {
        return $this->transactionDate;
    }

    /**
     * @return Address
     */
    public function getFromAddress(): Address
    {
        return $this->fromAddress;
    }

    /**
     * @return Address
     */
    public function getToAddress(): Address
    {
        return $this->toAddress;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
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
    public function getSalesTax(): float
    {
        return $this->salesTax;
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
        $keyTransactionID = $keyMap['transactionID'] ?? 'transaction_id';
        if ('' !== $keyTransactionID) {
            $arr[$keyTransactionID] = $this->getTransactionID();
        }

        $keyUserID = $keyMap['userID'] ?? 'user_id';
        if ('' !== $keyUserID) {
            $arr[$keyUserID] = $this->getUserID();
        }

        $keyTransactionDate = $keyMap['transactionDate'] ?? 'transaction_date';
        if ('' !== $keyTransactionDate) {
            $arr[$keyTransactionDate] = $this->getTransactionDate()->format('Y-m-d\TH:i:s\Z');
        }
        
        
        $keyTransactionReferenceID = $keyMap['transactionReferenceID'] ?? 'transaction_reference_id';
        if ('' !== $keyTransactionReferenceID) {
            $arr[$keyTransactionReferenceID] = $this->getTransactionReferenceID();
        }
        
        $keyFromCountry = $keyMap['fromCountry'] ?? 'from_country';
        if ('' !== $keyFromCountry) {
            $arr[$keyFromCountry] = $this->fromAddress->getCountryCode();
        }
        
        $keyFromZip = $keyMap['fromZip'] ?? 'from_zip';
        if ('' !== $keyFromZip) {
            $arr[$keyFromZip] = $this->fromAddress->getZip();
        }
        
        $keyFromState = $keyMap['fromState'] ?? 'from_state';
        if ('' !== $keyFromState) {
            $arr[$keyFromState] = $this->fromAddress->getState();
        }
        
        $keyFromCity = $keyMap['fromCity'] ?? 'from_city';
        if ('' !== $keyFromCity) {
            $arr[$keyFromCity] = $this->fromAddress->getCity();
        }
        
        $keyFromStreet = $keyMap['fromStreet'] ?? 'from_street';
        if ('' !== $keyFromStreet) {
            $arr[$keyFromStreet] = $this->fromAddress->getStreetAndNo();
        }


        $keyToCountry = $keyMap['toCountry'] ?? 'to_country';
        if ('' !== $keyToCountry) {
            $arr[$keyToCountry] = $this->toAddress->getCountryCode();
        }

        $keyToZip = $keyMap['toZip'] ?? 'to_zip';
        if ('' !== $keyToZip) {
            $arr[$keyToZip] = $this->toAddress->getZip();
        }

        $keyToState = $keyMap['toState'] ?? 'to_state';
        if ('' !== $keyToState) {
            $arr[$keyToState] = $this->toAddress->getState();
        }

        $keyToCity = $keyMap['toCity'] ?? 'to_city';
        if ('' !== $keyToCity) {
            $arr[$keyToCity] = $this->toAddress->getCity();
        }

        $keyToStreet = $keyMap['toStreet'] ?? 'to_street';
        if ('' !== $keyToStreet) {
            $arr[$keyToStreet] = $this->toAddress->getStreetAndNo();
        }

        $keyAmount = $keyMap['amount'] ?? 'amount';
        if ('' !== $keyAmount) {
            $arr[$keyAmount] = $this->getAmount();
        }

        $keyShipping = $keyMap['shipping'] ?? 'shipping';
        if ('' !== $keyShipping) {
            $arr[$keyShipping] = $this->getShipping();
        }

        $keySalesTax = $keyMap['salesTax'] ?? 'sales_tax';
        if ('' !== $keySalesTax) {
            $arr[$keySalesTax] = $this->getSalesTax();
        }

        $lineItemKeyMap = is_array($keyMap['lineItems']) ? $keyMap['lineItems'] : null;
        $lineItemKey = array_key_exists('lineItems',$keyMap) && is_string($keyMap['lineItems']) ? $keyMap['lineItems'] : 'line_items';
        $lineItemArr = [];
        if ('' !== $lineItemKey) {
            /**
             * @var $lineItem OrderLineItem
             */
            foreach ($this->lineItems as $lineItem) {
                $lineItemArr[] = $lineItem->toArray($lineItemKeyMap);
            }
            $arr[$lineItemKey] = $lineItemArr;
        }

        return $arr;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public static function fromArray(array $arr): self
    {
        if (count($arr) === 1 && array_key_exists('order',$arr)) {
            $arr = $arr['order'];
        }
        $transID = (string)($arr['transaction_id'] ?? $arr['transactionID'] ?? $arr['transactionId'] ?? '');
        $transactionDateStr = (string)($arr['transaction_date'] ?? $arr['transactionDate'] ?? '');
        if ($transactionDateStr) {
            $transactionDate = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s\Z');
            if (!$transactionDate) {
                $transactionDate = \DateTimeImmutable::createFromFormat('Y-m-dTH:i:sZ');
                if (!$transactionDate) {
                    $transactionDate = new \DateTimeImmutable();
                }
            }
        } else {
            $transactionDate = new \DateTimeImmutable();
        }
        $userID = $arr['user_id'] ?? $arr['userID'] ?? null;
        if (null !== $userID) {
            $userID = (int)$userID;
        }
        $transactionReferenceID = $arr['transaction_reference_id'] ?? $arr['transactionReferenceID'] ?? $arr['transactionReferenceId'] ?? null;
        if (null !== $transactionReferenceID) {
            $transactionReferenceID = (string)$transactionReferenceID;
        }

        $fromCountry = $arr['from_country'] ?? $arr['fromCountry'] ?? '';
        $fromZip = $arr['from_zip'] ?? $arr['fromZip'] ?? '';
        $fromState = $arr['from_state'] ?? $arr['fromState'] ?? '';
        $fromCity = $arr['from_city'] ?? $arr['fromCity'] ?? '';
        $fromStreet = $arr['from_street'] ?? $arr['fromStreet'] ?? '';
        $fromAddress = new Address($fromCountry, $fromZip, $fromState, $fromCity, $fromStreet);

        $toCountry  = $arr['to_country']    ?? $arr['toCountry'] ?? '';
        $toZip      = $arr['to_zip']        ?? $arr['toZip'] ?? '';
        $toState    = $arr['to_state']      ?? $arr['toState'] ?? '';
        $toCity     = $arr['to_city']       ?? $arr['toCity'] ?? '';
        $toStreet   = $arr['to_street']     ?? $arr['toStreet'] ?? '';
        $toAddress  = new Address($toCountry, $toZip, $toState, $toCity, $toStreet);

        $amount = (float)($arr['amount'] ?? 0.00);
        $shipping = (float)($arr['shipping'] ?? 0.00);
        $salesTax = (float)($arr['sales_tax'] ?? $arr['salesTax'] ?? 0.00);

        $lineItems = [];
        $lineItemsArr = $arr['line_items'] ?? $arr['lineItems'] ?? [];
        foreach ($lineItemsArr as $lineItemArr) {
            $lineItems[] = OrderLineItem::fromArray($lineItemArr);
        }

        $transaction = new Transaction($transID,$userID,$transactionDate,$transactionReferenceID,$fromAddress,$toAddress,$amount,$shipping,$salesTax,$lineItems);
        return $transaction;
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

    /**
     * @return null|int
     */
    public function getUserID(): ?int
    {
        return $this->userID;
    }

    /**
     * @return null|string
     */
    public function getTransactionReferenceID(): ?string
    {
        return $this->transactionReferenceID;
    }


}