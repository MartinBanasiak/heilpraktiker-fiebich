<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;

/**
 * Class WebshopItemFile
 * @package DynCom\dc\dcShop\classes
 */
class WebshopItemFile implements GenericDBModelInterface, Entity
{

    use genericDBModelTrait, universallyGettableTrait;

    public const TYPE_IMAGE = 0;
    public const TYPE_DOCUMENT_FILE = 1;
    public const TYPE_VIDEO_FILE = 2;
    public const TYPE_YOUTUBE_ID = 3;
    public const TYPE_360_DEGREE_IMAGE = 4;

    protected $id;
    protected $company;
    protected $shop_code;
    protected $language_code;
    protected $item_no;
    protected $variant_code;
    protected $type;
    protected $line_no;
    protected $description;
    protected $youtubeVideoID;
    protected $filename;
    protected $all_language_codes;
    protected $mp4;
    protected $webm;
    protected $ogg;
    protected $main_medium;
    protected $customization;
    protected $to_delete;

    /**
     * @param WebshopItemFileConfig $config
     */
    public function __construct(WebshopItemFileConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @return WebshopItemFile
     */
    public function getNullObject()
    {
        return new self($this->config);
    }


}