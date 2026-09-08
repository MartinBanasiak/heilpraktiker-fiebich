<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\PlainTemplate;
use DynCom\dc\common\interfaces\PlainView;
use DynCom\dc\common\interfaces\ViewModel;
use DynCom\dc\common\traits\genericViewTrait;

/**
 * Created by PhpStorm.
 * User: Michael Bauer
 * Date: 7/14/2015
 * Time: 2:26 AM
 */
class GenericPlainView implements PlainView
{

    use genericViewTrait;

    protected $templateFieldNames;
    protected $templateContentRaw;
    protected $templateFieldDelimiter;
    protected $viewModelData;
    protected $transformer;
    protected $renderedContent = false;

    /**
     * GenericPlainView constructor.
     * @param PlainTemplate $template
     * @param ViewModel $viewModel
     * @param RenderableStringTransformer $transformer
     */
    public function __construct(PlainTemplate $template, ViewModel $viewModel, RenderableStringTransformer $transformer) {
        $this->templateContentRaw = $template->getRawContent();
        $this->templateFieldNames = $template->getFields();
        $this->templateFieldDelimiter = $template->getDelimiter();
        $this->viewModelData = $viewModel->getData();
        $this->transformer = $transformer;
    }

    /**
     * @param bool $cleanup
     * @return bool|mixed|string
     */
    public function render($cleanup = true) {
        if($this->renderedContent) {return $this->renderedContent;}
        $content = $this->templateContentRaw;
        foreach($this->viewModelData as $key => &$value) {
            if(in_array($key,$this->templateFieldNames,false)) {
                $valueStr = $this->transformer->toString($value);
                $token = '{' . $this->templateFieldDelimiter . $key . $this->templateFieldDelimiter . '}';
                $content = str_replace($token,$valueStr,$content);
            }
        }
        unset($value);
        if($cleanup) {
            foreach($this->templateFieldNames as $fieldName) {
                $token = '{' . $this->templateFieldDelimiter . $fieldName . $this->templateFieldDelimiter . '}';
                $content = str_replace($token,'',$content);
            }
        }
        return $content;
    }
}