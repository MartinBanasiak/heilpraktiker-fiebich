<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Class WebshopItemDescriptionCollection
 * @package DynCom\dc\dcShop\classes
 */
class WebshopItemDescriptionCollection implements GenericCollectionInterface
{

    use genericCollectionTrait;

    /**
     * @param WebshopItemDescriptionConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct(
        WebshopItemDescriptionConfig $config,
        CriteriaHelperInterface $criteriaValidationService
    ) {
        $this->elements = new \SplObjectStorage();
        $this->config = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName = $config->getModelClassName();
    }


    /**
     * @return WebshopItemDescriptionCollection
     */
    public function getEmptyCollection()
    {
        return new self($this->config, $this->criteriaValidationService);
    }

    /**
     * @param mixed $instance
     * @param bool $idCheck
     *
     * @return bool
     */
    public function add($instance, $idCheck = false)
    {
        return $this->_addWebshopItemDescription($instance, $idCheck);
    }

    /**
     * @param WebshopItemDescription $webshopItemDescription
     * @param bool $idCheck
     *
     * @return bool
     */
    protected function _addWebshopItemDescription(WebshopItemDescription $webshopItemDescription, $idCheck = false)
    {
        return $this->_add($webshopItemDescription, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool $idCheck
     *
     * @return bool
     */
    public function remove($instance, $idCheck = false)
    {
        return $this->_removeWebshopItemDescription($instance, $idCheck);
    }

    /**
     * @param WebshopItemDescription $webshopItemDescription
     * @param bool $idCheck
     *
     * @return bool
     */
    protected function _removeWebshopItemDescription(WebshopItemDescription $webshopItemDescription, $idCheck = false)
    {
        return $this->_remove($webshopItemDescription, $idCheck);
    }

}