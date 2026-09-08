<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\DOMTemplate;
use DynCom\dc\common\interfaces\DOMView;
use DynCom\dc\common\interfaces\ViewModel;
use DynCom\dc\common\traits\genericViewTrait;

/**
 * Created by PhpStorm.
 * User: Michael Bauer
 * Date: 7/14/2015
 * Time: 12:11 AM
 */
class GenericDOMView implements DOMView
{

    use genericViewTrait;


    protected $DOMTemplate;
    protected $DOMDocument;
    protected $viewModelData;
    protected $transformer;
    protected $prefix;

    protected $renderedContent = false;

    /**
     * GenericDOMView constructor.
     * @param DOMTemplate $template
     * @param ViewModel $viewModel
     * @param RenderableStringTransformer $transformer
     */
    public function __construct(DOMTemplate $template, ViewModel $viewModel, RenderableStringTransformer $transformer) {
        $this->DOMTemplate = $template;
        $this->DOMDocument = $template->getDOMDocument();
        $this->viewModelData = $viewModel->getData();
        $this->transformer = $transformer;
        $this->prefix = $template->getInserterTokenPrefix();
        $this->lowestWritableDir = '/htdocs';
        $this->enforceLowestWriteLevelLimit = true;
        $this->classOrSelfReferencePath = 'self';
        $this->canSidestep = false;
    }

    /**
     * @return bool|string
     */
    public function render() {

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
        foreach($this->viewModelData as $key => &$value) {
            if(array_key_exists($key,$nodeArr) && ($node = $nodeArr[$key]) && ($node instanceof \DOMNode)) {
                $stringVal = $this->transformer->toString($value);
                $isXML = (strip_tags($stringVal) !== $stringVal);
                if($isXML) {
                    $tmpDoc = clone $doc;
                    //LoadHTML is needed to correctly format HTML on loading
                    @$tmpDoc->loadHTML($stringVal);
                    //loadHTML creates html and body tags around stringVal, so content is first child of first child
                    $el = $doc->importNode($tmpDoc->documentElement,true);
                } else {
                    $el = $doc->createTextNode($stringVal);
                }
                $node->appendChild($el);
            }
        }
        $this->renderedContent = $doc->saveXML($doc->documentElement);
        return $this->renderedContent;
    }

}