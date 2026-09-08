<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;

/**
 * Class PageCollection
 * @package DynCom\dc\common\classes
 */
class PageCollection implements GenericCollectionInterface
{

    use genericCollectionTrait;

    /**
     * @param PageConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct(PageConfig $config, CriteriaHelperInterface $criteriaValidationService)
    {
        $this->elements = new \SplObjectStorage();
        $this->config = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName = $config->getModelClassName();
    }


    /**
     * @return PageCollection
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
        return $this->_addPage($instance, $idCheck);
    }

    /**
     * @param Page $page
     * @param bool $idCheck
     * @return bool
     */
    protected function _addPage(Page $page, $idCheck = FALSE)
    {
        return $this->_add($page, $idCheck);
    }

    /**
     * @param mixed $instance
     * @param bool $idCheck
     *
     * @return bool
     */
    public function remove($instance, $idCheck = FALSE)
    {
        return $this->_removePage($instance, $idCheck);
    }

    /**
     * @param Page $page
     * @param bool $idCheck
     * @return bool
     */
    protected function _removePage(Page $page, $idCheck = FALSE)
    {
        return $this->_remove($page, $idCheck);
    }

}