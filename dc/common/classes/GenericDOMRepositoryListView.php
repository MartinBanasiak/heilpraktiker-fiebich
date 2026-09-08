<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\DOMRepositoryListView;
use DynCom\dc\common\interfaces\DOMTemplate;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryListViewTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 25.07.2015
 * Time: 11:15
 */
class GenericDOMRepositoryListView implements DOMRepositoryListView
{


    use genericRepositoryListViewTrait;


    protected $DOMTemplate;
    protected $DOMDocument;
    protected $repository;
    protected $transformer;
    protected $prefix;

    protected $renderedContent = false;

    /**
     * GenericDOMRepositoryListView constructor.
     * @param DOMTemplate $listElementTemplate
     * @param Repository $repository
     * @param RenderableStringTransformer $transformer
     */
    public function __construct(DOMTemplate $listElementTemplate, Repository $repository, RenderableStringTransformer $transformer) {

        $domImpl = new \DOMImplementation();
        $this->DOMDocument = $domImpl->createDocument(null, 'html',
            $domImpl->createDocumentType("html",
                "-//W3C//DTD XHTML 1.0 Transitional//EN",
                "/dc/common/xhtml11.dtd"));
        $this->DOMDocument->formatOutput = true;
        $this->DOMTemplate = $this->DOMDocument->importNode($listElementTemplate->getDOMDocument()->documentElement, true);
        $this->repository = $repository;
        $this->transformer = $transformer;
        $this->prefix = $listElementTemplate->getInserterTokenPrefix();
        $this->lowestWritableDir = '/htdocs';
        $this->enforceLowestWriteLevelLimit = true;
        $this->classOrSelfReferencePath = 'self';
        $this->canSidestep = false;
    }

    /**
     * @param array $criteria
     * @param int $offset
     * @param int $limit
     * @return bool|string
     */
    public function render(array $criteria, $offset = 0, $limit = 0) {

        if($this->renderedContent) {
            return $this->renderedContent;
        }

        $doc = $this->DOMDocument;
        $XPath = new \DOMXPath($doc);
        $query = "//@*[starts-with(name(),'{$this->prefix}')]";
        $nodeList = $XPath->query($query);
        $nodeArr = [];
        $len = $nodeList->length;
        for($i = 0;$i < $len;++$i) {
            $node = $nodeList->item($i);
            $attrName = $node->nodeName;
            $fieldName = str_replace($this->prefix,'',$attrName);
            $owner = &$node->ownerElement;
            $owner->removeAttribute($attrName);
            $nodeArr[$fieldName] = &$owner;
        }

        $collection = $this->repository->findByCriteria($criteria,$offset,$limit);
        $renderedString = '';
        foreach($collection as $listElement) {
            $tmpTemplate = clone $this->DOMTemplate;
            $tmpDoc = $tmpTemplate->ownerDocument;
            $XPath = new \DOMXPath($tmpDoc);
            $nodeList = $XPath->query($query);
            $nodeArr = [];
            $len = $nodeList->length;
            for($i = 0;$i < $len;++$i) {
                $node = $nodeList->item($i);
                $attrName = $node->nodeName;
                $fieldName = str_replace($this->prefix,'',$attrName);
                $owner = &$node->ownerElement;
                $owner->removeAttribute($attrName);
                $nodeArr[$fieldName] = &$owner;
            }
            $fieldNames = array_keys($nodeArr);
            foreach($fieldNames as $fieldName) {
                if(property_exists($listElement,$fieldName)) {
                    $node = $nodeArr[$fieldName];
                    $stringVal = $this->transformer->toString($listElement->$fieldName);
                    $isXML = (strip_tags($stringVal) !== $stringVal);
                    if($isXML) {
                        $tmpDoc = clone $doc;
                        //LoadHTML is needed to correctly format HTML on loading
                        @$tmpDoc->loadHTML($stringVal);
                        //loadHTML creates html and body tags around stringVal, so content is first child of first child
                        $el = $doc->importNode($tmpDoc->documentElement->firstChild->firstChild,true);
                    } else {
                        $el = $doc->createTextNode($stringVal);
                    }
                    $node->appendChild($el);
                }
            }
            $renderedString .= $tmpDoc->saveHTML();
        }

        $this->renderedContent = $renderedString;
        return $this->renderedContent;
    }

}