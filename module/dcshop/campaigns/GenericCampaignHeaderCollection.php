<?php
namespace DynCom\dc\dcShop\campaigns;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Class GenericCampaignElementCollection
 */
class GenericCampaignHeaderCollection implements GenericCollectionInterface
{

    use genericCollectionTrait;

    /**
     * GenericCampaignHeaderCollection constructor.
     * @param GenericCampaignHeaderConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct(GenericCampaignHeaderConfig $config, CriteriaHelperInterface $criteriaValidationService)
    {
        $this->elements = new \SplObjectStorage();
        $this->config = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName = $config->getModelClassName();
    }

    /**
     * @return GenericCampaignElementCollection
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
        return $this->_addGenericCampaignHeader($instance, $idCheck);
    }

    /**
     * @param GenericCampaign $genericCampaign
     * @param bool $idCheck
     * @return bool
     */
    protected function _addGenericCampaignHeader(GenericCampaign $genericCampaign, $idCheck = FALSE)
    {
        return $this->_add($genericCampaign, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool $idCheck
     *
     * @return bool
     */
    public function remove($instance, $idCheck = FALSE)
    {
        return $this->_removeGenericCampaignHeader($instance, $idCheck);
    }

    /**
     * @param GenericCampaign $genericCampaign
     * @param bool $idCheck
     * @return bool
     */
    protected function _removeGenericCampaignHeader(GenericCampaign $genericCampaign, $idCheck = FALSE)
    {
        return $this->_remove($genericCampaign, $idCheck);
    }

    public function getNullObject() {
        $coll = new GenericCampaignElementCollection(new GenericCampaignElementConfig(),$this->criteriaValidationService);
        return new GenericCampaign($this->config,$coll,$coll);
    }

}