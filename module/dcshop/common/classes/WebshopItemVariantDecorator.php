<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\dcShop\abstracts\WebshopItemDecorator;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;
use DynCom\dc\dcShop\interfaces\WebshopItemWithVariants;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 08.10.2015
 * Time: 13:45
 */
class WebshopItemVariantDecorator extends WebshopItemDecorator implements WebshopItemWithVariants
{

    private $variantCollection;

    /**
     * WebshopItemVariantDecorator constructor.
     * @param WebshopItemInterface $webshopItem
     * @param WebshopItemVariantService $variantService
     */
    public function __construct(WebshopItemInterface $webshopItem, WebshopItemVariantService $variantService) {
        parent::__construct($webshopItem);
        $this->variantCollection = $variantService->getAllVariants($webshopItem);
    }

    /**
     * @return array
     */
    public function getAllVariants()
    {
        return $this->variantCollection;
    }

    /**
     * @param $identifier
     * @return mixed
     */
    public function getVariantByIdentifier($identifier)
    {
        if(!array_key_exists($identifier,$this->variantCollection)) {
            throw new \InvalidArgumentException('No such variant exists in variant collection for this item.');
        }
        return $this->variantCollection[$identifier];
    }


}