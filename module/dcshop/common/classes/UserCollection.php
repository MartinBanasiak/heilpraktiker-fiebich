<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.01.2015
 * Time: 11:25
 */
class UserCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    /**
     * @param UserConfig                                         $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( UserConfig $config, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  =  new \SplObjectStorage();
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return UserCollection
     */
    public function getEmptyCollection() {
        return new self($this->config,$this->criteriaValidationService);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function add( $instance, $idCheck = FALSE ) {
        return $this->_addUser($instance, $idCheck);
    }

    /**
     * @param User $user
     * @param bool $idCheck
     * @return bool
     */
    protected function _addUser( User $user, $idCheck = FALSE ) {
        return $this->_add($user,$idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeUser($instance,$idCheck);
    }

    /**
     * @param User $user
     * @param bool $idCheck
     * @return bool
     */
    protected function _removeUser( User $user, $idCheck = FALSE ) {
        return $this->_remove($user,$idCheck);
    }

    /**
     * @return User
     */
    public function getNullObject() {
        return new User($this->config);
    }

}