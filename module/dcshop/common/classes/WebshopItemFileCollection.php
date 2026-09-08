<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Class WebshopItemFileCollection
 * @package DynCom\dc\dcShop\classes
 */
class WebshopItemFileCollection implements GenericCollectionInterface
{

    use genericCollectionTrait;

    /**
     * @param WebshopItemFileConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct(WebshopItemFileConfig $config, CriteriaHelperInterface $criteriaValidationService)
    {
        $this->elements = new \SplObjectStorage();
        $this->config = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName = $config->getModelClassName();
    }


    /**
     * @return WebshopItemFileCollection
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
    public function add($instance, $idCheck = FALSE)
    {
        return $this->_addWebshopItemFile($instance, $idCheck);
    }

    /**
     * @param WebshopItemFile $webshopItemFile
     * @param bool $idCheck
     * @return bool
     */
    protected function _addWebshopItemFile(WebshopItemFile $webshopItemFile, $idCheck = FALSE)
    {
        return $this->_add($webshopItemFile, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool $idCheck
     *
     * @return bool
     */
    public function remove($instance, $idCheck = FALSE)
    {
        return $this->_removeWebshopItemFile($instance, $idCheck);
    }

    /**
     * @param WebshopItemFile $webshopItemFile
     * @param bool $idCheck
     * @return bool
     */
    protected function _removeWebshopItemFile(WebshopItemFile $webshopItemFile, $idCheck = FALSE)
    {
        return $this->_remove($webshopItemFile, $idCheck);
    }

}