<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\dcShop\interfaces\UserBasket;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 18.12.2015
 * Time: 09:41
 */
class BasketReader
{

    private $basket = null;

    /**
     * BasketReader constructor.
     * @param UserBasket $basket
     */
    public function __construct(UserBasket $basket)
    {
        $this->basket = $basket;
    }

    /**
     * @return mixed
     */
    public function getBasketTotal()
    {
        return $this->basket->getBasketTotal();
    }

    /**
     * @return mixed
     */
    public function getBasketNoOfPos()
    {
        return $this->basket->getTotalNoOfPos();
    }

    public function getBasketItemInfo()
    {
        $infoArr = [];
        $i = 0;
        foreach ($this->basket as $entity) {
            if ($entity instanceof BasketEntity) {
                $infoArr[$i]['id'] = $entity->getEntityID();
                $infoArr[$i]['item_no'] = $entity->getIdentifier();
                $infoArr[$i]['var_code'] = $entity->getSubIdentifier();
                $infoArr[$i]['item_key'] = $this->basket->getKey($entity);
                $infoArr[$i]['quantity'] = $entity->getQuantity();
                $infoArr[$i]['unit_price'] = $entity->getUnitPrice();
                $infoArr[$i]['line_amount'] = $entity->getLineAmount();
                $i++;
            }
        }
    }

    public function getItemCollectionClone()
    {

    }

}