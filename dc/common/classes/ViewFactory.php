<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\DOMTemplate;
use DynCom\dc\common\interfaces\PHTMLTemplate;
use DynCom\dc\common\interfaces\PlainTemplate;
use DynCom\dc\common\interfaces\Template;
use DynCom\dc\common\interfaces\ViewModel;

/**
 * Created by PhpStorm.
 * User: Michael Bauer
 * Date: 7/14/2015
 * Time: 2:43 AM
 */
class ViewFactory
{

    protected $transformer;

    /**
     * @param RenderableStringTransformer $transformer
     */
    public function __construct(RenderableStringTransformer $transformer) {
        $this->transformer = $transformer;
    }

    /**
     * @param Template $template
     * @param ViewModel $viewModel
     * @return GenericDOMView|GenericPHTMLView|GenericPlainView
     * @throws \InvalidArgumentException
     */
    public function getView(Template $template, ViewModel $viewModel) {
        if($template instanceof PHTMLTemplate) {
            return $this->getPHTMLView($template,$viewModel);
        } elseif($template instanceof PlainTemplate) {
            return $this->getPlainView($template,$viewModel);
        } elseif($template instanceof DOMTemplate) {
            return $this->getDOMView($template,$viewModel);
        }
        throw new \InvalidArgumentException(
          "Temlate must be either PHTMLTemplate, PlainTemplate or DOMTemplate"
        );
    }

    /**
     * @param DOMTemplate $template
     * @param ViewModel $viewModel
     * @return GenericDOMView
     */
    protected function getDOMView(DOMTemplate $template, ViewModel $viewModel) {
        return new GenericDOMView($template,$viewModel,$this->transformer);
    }

    /**
     * @param PlainTemplate $template
     * @param ViewModel $viewModel
     * @return GenericPlainView
     */
    protected function getPlainView(PlainTemplate $template, ViewModel $viewModel) {
        return new GenericPlainView($template,$viewModel,$this->transformer);
    }

    /**
     * @param PHTMLTemplate $template
     * @param ViewModel $viewModel
     * @return GenericPHTMLView
     */
    protected function getPHTMLView(PHTMLTemplate $template, ViewModel $viewModel) {
        return new GenericPHTMLView($template,$viewModel);
    }

}