<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;
/**
 * Class TextModule
 */
class TextModule implements GenericDBModelInterface, Entity {

    use genericDBModelTrait, universallyGettableTrait;

    protected $id;
    protected $company;
    protected $code;
    protected $description;
    protected $content;
    protected $attachment_1;
    protected $attachment_2;
    protected $to_delete;

    /**
     * @param TextModuleConfig $config
     */
    public function __construct( TextModuleConfig $config ) {
        $this->config = $config;
    }

    /**
     * @param array $placeholders
     * @param string $startDelimiter
     * @param string $endDelimiter
     * @return mixed
     */
    public function getContentWithReplacedPlaceholders(array $placeholders, $startDelimiter = '%', $endDelimiter = '%')
    {
        $content = $this->content;
        return $this->replacePlaceholdersInContentWithDelimiters($placeholders,$content,$startDelimiter,$endDelimiter);
    }

    /**
     * @param array $placeholders
     * @param string $startDelimiter
     * @param string $endDelimiter
     * @return mixed
     */
    public function getDescriptionWithReplacedPlaceholders(array $placeholders, $startDelimiter = '%', $endDelimiter = '%')
    {
        $content = $this->description;
        return $this->replacePlaceholdersInContentWithDelimiters($placeholders,$content,$startDelimiter,$endDelimiter);
    }

    /**
     * @param array $placeholders
     * @param $content
     * @param string $startDelimiter
     * @param string $endDelimiter
     * @return mixed
     */
    protected function replacePlaceholdersInContentWithDelimiters(array $placeholders, $content, $startDelimiter = '%', $endDelimiter = '%')
    {
        foreach ($placeholders as $placeholderName => $placeholderValue) {
            $searchString = $startDelimiter . $placeholderName . $endDelimiter;
            $content = str_replace($searchString,$placeholderValue,$content);
        }
        return $content;
    }

}