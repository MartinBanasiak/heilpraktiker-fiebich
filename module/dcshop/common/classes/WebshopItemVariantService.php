<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\classes\SelectionCriteriaHelper;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 08.10.2015
 * Time: 12:56
 */
class WebshopItemVariantService
{

    const VARIANT_TYPE_SHOP_VARIANTS_WITH_DUMMY = 0;
    const VARIANT_TYPE_SHOP_VARIANTS_NO_DUMMY = 1;
    const VARIANT_TYPE_NAV_VARIANTS = 2;

    private $db;

    /**
     * @var Shop
     */
    private $itemShop;

    /**
     * @var string
     */
    private $shopLanguageCode;

    /**
     * @var WebshopItemRepository
     */
    private $webshopItemRepository;

    /**
     * @var WebshopItemVariantRepository
     */
    private $webshopItemVariantRepository;

    /**
     * WebshopItemVariantService constructor.
     * @param PDOQueryWrapper $db
     * @param ShopRepository $shopRepository
     * @param WebshopItemRepository $webshopItemRepository
     * @param WebshopItemVariantRepository $webshopItemVariantRepository
     */
    public function __construct(
        PDOQueryWrapper $db,
        ShopRepository $shopRepository,
        WebshopItemRepository $webshopItemRepository,
        WebshopItemVariantRepository $webshopItemVariantRepository
    )
    {
        $this->db = $db;
        $this->shopRepository = $shopRepository;
        $this->webshopItemRepository = $webshopItemRepository;
        $this->webshopItemVariantRepository = $webshopItemVariantRepository;
    }

    /**
     * @param WebshopItemInterface $paramItem
     * @return array
     */
    public function getAllVariants(WebshopItemInterface $paramItem)
    {
        return $this->getAllVariantsByPrimary($paramItem->getCompany(),$paramItem->getShopCode(),$paramItem->getLanguageCode(),$paramItem->getItemNo());
    }

    public function getAllVariantsByPrimary($company,$shopCode,$languageCode,$itemNo)
    {
        $itemCriteria = [
            [
                ['company', '=', $company],
                ['shop_code', '=', $shopCode],
                ['language_code', '=', $languageCode],
                ['item_no', '=', $itemNo]
            ]
        ];
        $item = $this->webshopItemRepository->findByCriteria($itemCriteria,0,1)->getFirst();
        if(!($item->getID() > 0)) {
            throw new \InvalidArgumentException('No item with this number exists for shop ' . $item->getShopCode() . ' and language ' . $item->getLanguageCode());
        }
        $itemShop = $this->shopRepository->findByAltPrimary(['company' => $item->getCompany(), 'code' => $item->getShopCode()]);

        $fullVariants = [];
        switch ($itemShop->variant_typ) {
            case self::VARIANT_TYPE_SHOP_VARIANTS_WITH_DUMMY:
                $variants = $this->webshopItemRepository->getWebshopVariants($item);
                foreach ($variants as $variant) {
                    $variantKey = $variant->getItemNo();
                    $fullVariants[$variantKey] = $variant;
                }
                break;
            case self::VARIANT_TYPE_SHOP_VARIANTS_NO_DUMMY:
                $variants = $this->webshopItemRepository->getWebshopVariants($item);
                foreach ($variants as $variant) {
                    $variantKey = $variant->getItemNo();
                    $fullVariants[$variantKey] = $variant;
                }
                break;
            case self::VARIANT_TYPE_NAV_VARIANTS:
                $variants = $this->webshopItemVariantRepository->getAllForItem($item);
                foreach ($variants as $variant) {
                    $variantKey = $variant->code;
                    $fullVariants[$variantKey] = $variant;
                }
                break;
        }
        return $fullVariants;
    }

    /**
     * @param WebshopItemInterface $paramItem
     * @return WebshopItemCollection
     */
    public function getAllVariantsAsCollection(WebshopItemInterface $paramItem)
    {
        $itemCriteria = [
            [
                ['company', '=', $paramItem->getCompany()],
                ['shop_code', '=', $paramItem->getShopCode()],
                ['language_code', '=', $paramItem->getLanguageCode()],
                ['item_no', '=', $paramItem->getItemNo()]
            ]
        ];
        $item = $this->webshopItemRepository->findByCriteria($itemCriteria,0,1)->getFirst();
        if(!($item->getID() > 0)) {
            throw new \InvalidArgumentException('No item with this number exists for shop ' . $item->getShopCode() . ' and language ' . $item->getLanguageCode());
        }
        $itemShop = $this->shopRepository->findByAltPrimary(['company' => $item->getCompany(), 'code' => $item->getShopCode()]);

        $fullVariants = [];
        switch ($itemShop->variant_typ) {
            case self::VARIANT_TYPE_SHOP_VARIANTS_WITH_DUMMY:
                $variants = $this->webshopItemRepository->getWebshopVariants($item);
                $collection = new WebshopItemCollection(new WebshopItemConfig(), new SelectionCriteriaHelper());
                foreach ($variants as $variant) {
                    $collection->add($variant, true);
                }
                break;
            case self::VARIANT_TYPE_SHOP_VARIANTS_NO_DUMMY:
                $variants = $this->webshopItemRepository->getWebshopVariants($item);
                $collection = new WebshopItemCollection(new WebshopItemConfig(), new SelectionCriteriaHelper());
                foreach ($variants as $variant) {
                    $collection->add($variant, true);
                }
                break;
            case self::VARIANT_TYPE_NAV_VARIANTS:
                $variants = $this->webshopItemVariantRepository->getAllForItem($item);
                $collection = new WebshopItemCollection(new WebshopItemConfig(), new SelectionCriteriaHelper());
                foreach ($variants as $variant) {
                    $fullItem = new WebshopItemNAVVariantDecorator($item, $variant);
                    $collection->add($fullItem, false);
                }
                break;
        }
        return $collection;
    }

    /**
     * @param WebshopItemInterface $item
     * @return bool
     */
    public function isVariant(WebshopItemInterface $item) {
        $itemShop = $this->shopRepository->findByAltPrimary(['company' => $item->getCompany(), 'code' => $item->getShopCode()]);

        switch ($itemShop->variant_typ) {
            case self::VARIANT_TYPE_SHOP_VARIANTS_WITH_DUMMY:
                $parentItemNo = $item->getParentItemNo();
                return ($parentItemNo !== null && $parentItemNo !== '');
                break;
            case self::VARIANT_TYPE_SHOP_VARIANTS_NO_DUMMY:
                return true;
                break;
            case self::VARIANT_TYPE_NAV_VARIANTS:
                return ($item instanceof WebshopItemNAVVariantDecorator);
                break;
        }
        return false;
    }

    /**
     * @param WebshopItemInterface $item
     * @return WebshopItemInterface|WebshopItemNAVVariantDecorator|mixed
     */
    public function getFirstVariant(WebshopItemInterface $item)
    {

        $itemShop = $this->shopRepository->findByAltPrimary(['company' => $item->getCompany(), 'code' => $item->getShopCode()]);
        $itemShopVariantType = $itemShop->variant_typ;

        if ($this->isVariant($item) && $item->isActive()) {
            if ($itemShopVariantType == self::VARIANT_TYPE_SHOP_VARIANTS_NO_DUMMY) {
                $variants = $this->webshopItemRepository->getWebshopVariants($item);
                if ((!empty($variants) && ($variants->getFirst()->getID() > 0))) {
                    $item->setHasVariants(true);
                }
            }
            return $item;
        }

        switch ($itemShopVariantType) {
            case self::VARIANT_TYPE_SHOP_VARIANTS_WITH_DUMMY:
                $variants = $this->webshopItemRepository->getWebshopVariants($item);
                if ((empty($variants) || (!$variants->getFirst()->getID() > 0)) && $item->isActive()) {
                    return $item;
                }
                $variantFound = '';
                /*foreach ($variants as $variant) {
                    if ($variant instanceof WebshopItemInterface && $variant->getID() > 0 && $variant->isActive()) {
                        $variantFound = $variant;
                        continue;
                    }
                }*/
                if ($variants->getFirst()->getID() > 0 && $variants->getFirst()->isActive()) {
                    $variantFound = $variants->getFirst();
                }
                if (!empty($variantFound)) {
                    return $variantFound;
                } else {
                    return $item;
                }
                break;
            case self::VARIANT_TYPE_SHOP_VARIANTS_NO_DUMMY:
                if ($item->isActive()) {
                    $variants = $this->webshopItemRepository->getWebshopVariants($item);
                    if ((!empty($variants) && ($variants->getFirst()->getID() > 0))) {
                        $item->setHasVariants(true);
                    }
                    return $item;
                } else {
                    return;
                }
                break;
            case self::VARIANT_TYPE_NAV_VARIANTS:
                $variants = $this->webshopItemVariantRepository->getAllForItem($item);
                $navVariant = $variants->getFirst();

                if ($navVariant->getID() > 0 && $item->isActive()) {
                    return new WebshopItemNAVVariantDecorator($item, $navVariant);
                } else {
                    if ($item->isActive()) {
                        return $item;
                    } else {
                        return;
                    }
                }
                break;
        }
    }

    /**
     * @param WebshopItemInterface $item
     * @return WebshopItemInterface|WebshopItemNAVVariantDecorator|mixed
     */
    public function getFirstActiveVariant(WebshopItemInterface $item)
    {

        $itemShop = $this->shopRepository->findByAltPrimary(['company' => $item->getCompany(), 'code' => $item->getShopCode()]);
        $itemShopVariantType = $itemShop->variant_typ;

        if ($this->isVariant($item) && $item->isActive()) {
            if ($itemShopVariantType == self::VARIANT_TYPE_SHOP_VARIANTS_NO_DUMMY) {
                $variants = $this->webshopItemRepository->getActiveWebshopVariants($item);
                if ((!empty($variants) && ($variants->getFirst()->getID() > 0))) {
                    $item->setHasVariants(true);
                }
            }
            return $item;
        }

        switch ($itemShopVariantType) {
            case self::VARIANT_TYPE_SHOP_VARIANTS_WITH_DUMMY:
                $variants = $this->webshopItemRepository->getActiveWebshopVariants($item);
                if ((empty($variants) || (!$variants->getFirst()->getID() > 0)) && $item->isActive()) {
                    return $item;
                }
                $variantFound = '';
                /*foreach ($variants as $variant) {
                    if ($variant instanceof WebshopItemInterface && $variant->getID() > 0 && $variant->isActive()) {
                        $variantFound = $variant;
                        continue;
                    }
                }*/
                if ($variants->getFirst()->getID() > 0 && $variants->getFirst()->isActive()) {
                    $variantFound = $variants->getFirst();
                }
                if (!empty($variantFound)) {
                    return $variantFound;
                } else {
                    return $item;
                }
                break;
            case self::VARIANT_TYPE_SHOP_VARIANTS_NO_DUMMY:
                if ($item->isActive()) {
                    $variants = $this->webshopItemRepository->getActiveWebshopVariants($item);
                    if ((!empty($variants) && ($variants->getFirst()->getID() > 0))) {
                        $item->setHasVariants(true);
                    }
                    return $item;
                } else {
                    return;
                }
                break;
            case self::VARIANT_TYPE_NAV_VARIANTS:
                $variants = $this->webshopItemVariantRepository->getAllForItem($item);
                $navVariant = $variants->getFirst();

                if ($navVariant->getID() > 0 && $item->isActive()) {
                    return new WebshopItemNAVVariantDecorator($item, $navVariant);
                } else {
                    if ($item->isActive()) {
                        return $item;
                    } else {
                        return;
                    }
                }
                break;
        }
    }

    /**
     * @param WebshopItemInterface $item
     * @return WebshopItemInterface
     */
    public function getParentItem(WebshopItemInterface $item)
    {
        $parentItemNo = (isset($item)) ? $item->getParentItemNo() : null;
        if ((!$this->isVariant($item)) || empty($parentItemNo)) {
            return $item;
        }
        $altPrimary = [
            'company' => $item->getCompany(),
            'shop_code' => $item->getShopCode(),
            'language_code' => $item->getLanguageCode(),
            'item_no' => $item->getParentItemNo()
        ];
        return $this->webshopItemRepository->findByAltPrimary($altPrimary);
    }

    /**
     * @return WebshopItemVariantRepository
     */
    public function getVariantRepository()
    {
        return $this->webshopItemVariantRepository;
    }
}