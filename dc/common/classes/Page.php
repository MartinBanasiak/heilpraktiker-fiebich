<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;


/**
 * Class Page
 * @package DynCom\dc\common\classes
 */
class Page implements GenericDBModelInterface, Entity
{

    use genericDBModelTrait, universallyGettableTrait;

    protected $id;
    protected $title;
    protected $subtitle;
    protected $meta_keywords;
    protected $meta_description;
    protected $main_language_id;
    protected $active;
    protected $modified_date;
    protected $modified_user;
    protected $validity_from;
    protected $validity_to;

    /**
     * @param PageConfig $config
     */
    public function __construct(PageConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @return Page
     */
    public function getNullObject()
    {
        return new self($this->config);
    }


}