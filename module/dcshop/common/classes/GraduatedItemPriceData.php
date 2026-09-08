<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\dcShop\interfaces\ItemPriceDataInterface;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 16.10.2015
 * Time: 14:38
 */
class GraduatedItemPriceData
{

    private $priceDataArr = [];

    /**
     * @param $minQty
     * @param ItemPriceDataInterface $priceData
     */
    public function addPrice($minQty,ItemPriceDataInterface $priceData)
    {
        $minQty = (float)$minQty;
        $minQtyStr = (string)$minQty;
        $this->priceDataArr[$minQtyStr] = $priceData;
        ksort($this->priceDataArr,SORT_NUMERIC);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $arr = [];
        $i = 0;
        foreach($this->priceDataArr as $qtyStr => $priceData) {
            $qty = (float)$qtyStr;
            if (!($qty > 0)) {
                //@TODO: Respect multiplier / unit per package
                $qty = 1;
            }
            $price = $priceData->getPrice();
            $arr[$i]['minimum_quantity'] = $qty;
            $arr[$i]['your_price'] = $price;
            $arr[$i]['your_price_per_unit'] = $price / $qty;
            $arr[$i]['currency_code'] = $priceData->getCurrencyCode();

            ++$i;
        }
        return $arr;
    }

}