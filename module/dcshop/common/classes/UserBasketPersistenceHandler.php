<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\traits\hookableTrait;
use DynCom\dc\dcShop\interfaces\UserBasket;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 19.10.2015
 * Time: 18:31
 */
class UserBasketPersistenceHandler
{

    use hookableTrait;

    /**
     * @var UserBasketRepository
     */
    private $repository;
    private $initBasketHash;

    /**
     * UserBasketPersistenceHandler constructor.
     * @param UserBasketRepository $basketRepository
     * @param UserBasket $initialBasket
     */
    public function __construct(UserBasketRepository $basketRepository, UserBasket $initialBasket)
    {
        $this->repository = $basketRepository;
        $this->initBasketHash = $initialBasket->getHash();
    }

    /**
     * @param UserBasket $basket
     */
    public function setReferenceBasketHash(UserBasket $basket)
    {
        $this->initBasketHash = $basket->getHash();
    }

    /**
     * @param UserBasket $basket
     */
    public function checkAndPersist(UserBasket $basket) {
        $newHash = $basket->getHash();
        $lastPersistedHash = $basket->getLastPersistedHash();
        $oldHash = &$this->initBasketHash;
        if($newHash !== $oldHash || $newHash !== $lastPersistedHash || $basket->forcePersist()) {
            $this->updateHooks('basket_changed', $basket);
            $this->repository->saveUserBasket($basket);
        } else {
            $this->updateHooks('basket_unchanged',$basket);
        }
    }

}