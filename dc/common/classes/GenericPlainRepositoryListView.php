<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\PlainRepositoryListView;
use DynCom\dc\common\interfaces\PlainTemplate;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryListViewTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 25.07.2015
 * Time: 12:14
 */
class GenericPlainRepositoryListView implements PlainRepositoryListView
{
    use genericRepositoryListViewTrait;

    protected $templateFieldNames;
    protected $templateContentRaw;
    protected $templateFieldDelimiter;
    protected $repository;
    protected $transformer;
    protected $renderedContent = false;

    /**
     * GenericPlainRepositoryListView constructor.
     * @param PlainTemplate $listElementTemplate
     * @param Repository $repository
     * @param RenderableStringTransformer $transformer
     */
    public function __construct(PlainTemplate $listElementTemplate, Repository $repository, RenderableStringTransformer $transformer) {
        $this->templateContentRaw = $listElementTemplate->getRawContent();
        $this->templateFieldNames = $listElementTemplate->getFields();
        $this->templateFieldDelimiter = $listElementTemplate->getDelimiter();
        $this->repository = $repository;
        $this->transformer = $transformer;
    }

    /**
     * @param array $criteria
     * @param int $offset
     * @param int $limit
     * @param bool $cleanup
     * @return bool|string
     */
    public function render(array $criteria, $offset = 0, $limit = 0, $cleanup = true) {
        if($this->renderedContent) {return $this->renderedContent;}

        $collection = $this->repository->findByCriteria($criteria,$offset,$limit);
        $renderedString = '';
        $content = '';
        foreach($collection as $listElement) {
            $content = $this->templateContentRaw;
            foreach($this->templateFieldNames as $fieldName) {
                if(property_exists($listElement,$fieldName)) {
                    $valueStr = $this->transformer->toString($listElement->$fieldName);
                    $token = '{' . $this->templateFieldDelimiter . $fieldName . $this->templateFieldDelimiter . '}';
                    $content = str_replace($token,$valueStr,$content);
                }
            }
        }
        if($cleanup) {
            foreach($this->templateFieldNames as $fieldName) {
                $token = '{' . $this->templateFieldDelimiter . $fieldName . $this->templateFieldDelimiter . '}';
                $content = str_replace($token,'',$content);
            }
        }
        $renderedString .= $content;
        $this->renderedContent = $renderedString;
        return $this->renderedContent;
    }

}