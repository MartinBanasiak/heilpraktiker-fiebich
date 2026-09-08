<?php
namespace DynCom\dc\dcShop\ShippingOptions;

use DynCom\dc\dcShop\classes\CurrShopConfiguration;
use DynCom\dc\dcShop\interfaces\CustomerInterface;
use DynCom\dc\dcShop\interfaces\IVATManager;
use DynCom\dc\dcShop\interfaces\OrderableEntityInterface;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;
use DynCom\dc\RuleEngine\ActiveActionItemRuleDiscountRepository;
use DynCom\dc\RuleEngine\ActiveActionItemRuleDiscountRepositoryInterface;

/**
 * Created by PhpStorm.
 * User: Lorenz
 * Date: 01.03.2018
 * Time: 13:12
 */
class ShippingClassPriorityProvider
{
    private const DEFAULT_SHIPPING_CLASS_CODE = 'DEFAULT';
    /**
     * @var ShippingClassRepository
     */
    protected $shippingCLassRepository;
    /**
     * @var CurrShopConfiguration
     */
    private $currShopConfiguration;

    /**
     * ShippingClassPriorityProvider constructor.
     * @param ShippingClassRepository $shippingClassRepository
     * @param CurrShopConfiguration $currShopConfiguration
     */
    public function __construct(
        ShippingClassRepository $shippingClassRepository, CurrShopConfiguration $currShopConfiguration
    )
    {
        $this->shippingCLassRepository = $shippingClassRepository;
        $this->currShopConfiguration = $currShopConfiguration;
    }

    /**
     * @param WebshopItemInterface $item
     * @return Int
     */
    public function getItemShippingClassAndPriority(
        WebshopItemInterface $item
    ): array
    {

        $itemShippingClass = $item->getShippingClass();

        static $memo;
        if (null === $memo) {
            $memo = [];
        }

        if (array_key_exists($itemShippingClass, $memo)) {
            return $memo[$itemShippingClass];
        }

        $shippingClass = $this->shippingCLassRepository->findByAltPrimary(['company' => $item->getCompany(), 'code' => $itemShippingClass]);

        if (null === $shippingClass->id) {
            $defaultPriority = $this->currShopConfiguration->getDefaultShippingClassPriority();
            $memo[$itemShippingClass] = ['class' => self::DEFAULT_SHIPPING_CLASS_CODE, 'priority' => $defaultPriority];
            return ['class' => self::DEFAULT_SHIPPING_CLASS_CODE, 'priority' => $defaultPriority];
        }
        $memo[$itemShippingClass] = ['class' => $itemShippingClass, 'priority' => $shippingClass->priority];
        return ['class' => $itemShippingClass, 'priority' => $shippingClass->priority];
    }
}