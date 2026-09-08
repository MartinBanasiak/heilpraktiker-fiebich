<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\dcShop\interfaces\ItemInventoryStrategyInterface;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 10.07.2015
 * Time: 08:44
 */
class NAVVariantsInventoryStrategy implements ItemInventoryStrategyInterface
{

    /**
     * @var PDOQueryWrapper
     */
    protected $db;

    /**
     * @var WebshopItemRepository
     */
    protected $repository;

    /**
     * @var WebshopItemVariantRepository
     */
    protected $variantRepository;

    /**
     * NAVVariantsInventoryStrategy constructor.
     * @param PDOQueryWrapper $db
     * @param WebshopItemRepository $itemRepository
     * @param WebshopItemVariantRepository $variantRepository
     */
    public function __construct(PDOQueryWrapper $db, WebshopItemRepository $itemRepository, WebshopItemVariantRepository $variantRepository) {
        $this->db = $db;
        $this->repository = $itemRepository;
        $this->variantRepository = $variantRepository;
    }

    /**
     * @param WebshopItemInterface $item
     * @return float
     * @throws \ErrorException
     */
    public function getItemInventory(WebshopItemInterface $item) {
        if($item->getVariantCode() !== '') return $item->getInventory();
        $firstVariant = $this->variantRepository->getFirstForItem($item);
        $hasVariants = ($firstVariant->id > 0);
        if(!$hasVariants) return $item->getInventory();
        $db = $this->db;
        $querySuccess =
            $db->select('SUM(inventory)')
                ->from('shop_item_variant')
                ->where('company','=',$item->getCompany())
                ->andWhere('item_no','=',$item->getItemNo())
                ->andWhere('to_delete','=',0)
                ->setConstructedQuery()->doQuery();
        if(!$querySuccess) return 0.00;
        $resArr = $db->getResultArray();
        return isset($resArr[0][0]) ? $resArr[0][0] : 0.00;
    }

    /**
     * @param $itemID
     * @return float
     */
    public function getItemInventoryByID($itemID)
    {
        $item = $this->repository->findByID($itemID);
        if ($item instanceof WebshopItemInterface) {
            return $this->getItemInventory($item);
        }
        return 0.00;
    }

    /**
     * @param array $altPrimary
     * @return float
     */
    public function getItemInventoryByAltPrimary(array $altPrimary)
    {
        $item = $this->repository->findByAltPrimary($altPrimary);
        if ($item instanceof WebshopItemInterface) {
            return $this->getItemInventory($item);
        }
        return 0.00;
    }


}