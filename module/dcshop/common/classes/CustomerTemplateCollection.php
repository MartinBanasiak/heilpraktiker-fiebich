<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Class CustomerTemplateCollection
 */
class CustomerTemplateCollection implements GenericCollectionInterface {

    use genericCollectionTrait;

    /**
     * @param CustomerTemplateConfig                                     $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct( CustomerTemplateConfig $config, CriteriaHelperInterface $criteriaValidationService ) {
        $this->elements                  =  new \SplObjectStorage();
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName            = $config->getModelClassName();
    }

    /**
     * @return CustomerTemplateCollection
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
        return $this->_addCustomerTemplate($instance, $idCheck);
    }

    /**
     * @param CustomerTemplate $customerTemplate
     * @param bool      $idCheck
     *
     * @return bool
     */
    protected function _addCustomerTemplate( CustomerTemplate $customerTemplate, $idCheck = FALSE ) {
        return $this->_add($customerTemplate, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool  $idCheck
     *
     * @return bool
     */
    public function remove( $instance, $idCheck = FALSE ) {
        return $this->_removeCustomerTemplate($instance,$idCheck);
    }

    /**
     * @param CustomerTemplate $customerTemplate
     * @param bool      $idCheck
     *
     * @return bool
     */
    protected function _removeCustomerTemplate( CustomerTemplate $customerTemplate, $idCheck = FALSE ) {
        return $this->_remove($customerTemplate,$idCheck);
    }

}