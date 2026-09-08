<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\classes\PDOQueryWrapper;

/**
 * Low-impact implementation w/o full Objects
 * Created by PhpStorm.
 * User: bauer
 * Date: 13.10.2015
 * Time: 15:41
 */
class UserBasketLoginHandler
{

    private $basketRepository;
    private $db;

    /**
     * UserBasketLoginHandler constructor.
     * @param UserBasketRepository $basketRepository
     * @param PDOQueryWrapper $queryWrapper
     */
    public function __construct(UserBasketRepository $basketRepository, PDOQueryWrapper $queryWrapper)
    {
        $this->basketRepository = $basketRepository;
        $this->db = $queryWrapper;
    }

    /**
     * @param $currentVisitorID
     * @param $lastVisitorID
     * @param $forceLastBasket
     */
    public function handleLogin($currentVisitorID, $lastVisitorID, $forceLastBasket = false)
    {
        if ($forceLastBasket) {
            $this->replaceCurrVisitorMainBasketWithLastVisitorMainBasketIfLastNotEmpty($currentVisitorID,$lastVisitorID);
        } else {
            $this->replaceCurrVisitorMainBasketWithLastVisitorMainBasketIfCurrEmpty($currentVisitorID,$lastVisitorID);
        }
        $this->setVisitorOnSecondaryBaskets($lastVisitorID,$currentVisitorID);
    }

    /**
     * @param $currVisitorID
     * @param $lastVisitorID
     */
    private function removeLastVisitorBasketMainFlagAndSetToCurrVisitor($currVisitorID, $lastVisitorID)
    {
        $currVisitorID = (int)$currVisitorID;
        $lastVisitorID = (int)$lastVisitorID;
        $lastVisitorMainBasketID = $this->basketRepository->getMainBasketIDForVisitor($lastVisitorID);
        if($lastVisitorMainBasketID > 0) {
            $this->basketRepository->removeMainBasketFlag($lastVisitorMainBasketID);
            $this->basketRepository->setBasketVisitorUser($lastVisitorMainBasketID,$currVisitorID);
        }
    }

    /**
     * @param $currVisitorID
     * @param $lastVisitorID
     */
    private function replaceCurrVisitorMainBasketWithLastVisitorMainBasketIfCurrEmpty($currVisitorID, $lastVisitorID)
    {
        $currVisitorID = (int)$currVisitorID;
        $lastVisitorID = (int)$lastVisitorID;

        $lastVisitorMainBasketID = $this->basketRepository->getMainBasketIDForVisitor($lastVisitorID);
        $currVisitorMainBasketID = $this->basketRepository->getMainBasketIDForVisitor($currVisitorID);

        $lastBasketHasContent = $this->basketRepository->basketHasContent($lastVisitorMainBasketID);
        $currBasketHasContent = $this->basketRepository->basketHasContent($currVisitorMainBasketID);

        if($lastBasketHasContent && !$currBasketHasContent) {
            $this->basketRepository->deleteBasketByID($currVisitorMainBasketID);
            $this->basketRepository->setBasketVisitorUser($lastVisitorMainBasketID,$currVisitorID);
            $this->basketRepository->setMainBasketFlag($lastVisitorMainBasketID);
        } else {
            $this->removeLastVisitorBasketMainFlagAndSetToCurrVisitor($currVisitorID,$lastVisitorID);
        }
    }

    /**
     * @param $currVisitorID
     * @param $lastVisitorID
     */
    private function replaceCurrVisitorMainBasketWithLastVisitorMainBasketIfLastNotEmpty($currVisitorID, $lastVisitorID)
    {
        $currVisitorID = (int)$currVisitorID;
        $lastVisitorID = (int)$lastVisitorID;

        $currVisitorMainBasketID = $this->basketRepository->getMainBasketIDForVisitor($currVisitorID);
        $lastVisitorMainBasketID = $this->basketRepository->getMainBasketIDForVisitor($lastVisitorID);

        $lastBasketHasContent = $this->basketRepository->basketHasContent($lastVisitorMainBasketID);

        if($lastBasketHasContent) {
            $this->basketRepository->deleteBasketByID($currVisitorMainBasketID);
            $this->basketRepository->setBasketVisitorUser($lastVisitorMainBasketID,$currVisitorID);
            $this->basketRepository->setMainBasketFlag($lastVisitorMainBasketID);
        } else {
            $this->removeLastVisitorBasketMainFlagAndSetToCurrVisitor($currVisitorID,$lastVisitorID);
        }
    }

    /**
     * @param $lastVisitorID
     * @param $currVisitorID
     */
    private function setVisitorOnSecondaryBaskets($lastVisitorID, $currVisitorID)
    {
        $lastVisitorID = (int)$lastVisitorID;
        $currVisitorID = (int)$currVisitorID;
        $query = 'UPDATE ' . UserBasketRepository::HEADER_TABLE . ' SET shop_visitor_id = ' . $currVisitorID
            . ' WHERE main_basket = 0 AND shop_visitor_id = ' . $lastVisitorID;
        $this->db->setQuery($query)->doQuery();
    }

    /**
     * @param $fromVisitorID
     * @param $toVisitorID
     */
    private function swapMainBasketFromToVisitor($fromVisitorID, $toVisitorID)
    {
        $fromVisitorID = (int)$fromVisitorID;
        $toVisitorID = (int)$toVisitorID;
        $fromBasketHeaderID = $this->basketRepository->getVisitorMainBasketID($fromVisitorID);
        if($fromBasketHeaderID > 0) {
            $toBasketHeaderID = $this->basketRepository->getVisitorMainBasketID($toVisitorID);
            $this->basketRepository->deleteBasketByID($toBasketHeaderID);
            $this->basketRepository->setBasketVisitorUser($fromBasketHeaderID,$toVisitorID);
        }
    }

    /**
     * @param $fromVisitorID
     * @param $toVisitorID
     */
    private function mergeMainBasketFromToVisitor($fromVisitorID, $toVisitorID)
    {
        //@TODO: Implement
    }

    /**
     * @param $fromBasketID
     * @param $toBasketID
     */
    private function mergeBasketFromToBasketID($fromBasketID, $toBasketID)
    {
        //@TODO: Implement
    }

}