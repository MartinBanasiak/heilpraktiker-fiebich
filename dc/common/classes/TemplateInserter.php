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
 * Date: 7/13/2015
 * Time: 11:45 PM
 */
class TemplateInserter
{
    /**
     * @var RenderableElementResolver
     */
    protected $resolver;

    /**
     * TemplateInserter constructor.
     * @param RenderableElementResolver $resolver
     */
    public function __construct(RenderableElementResolver $resolver) {
        $this->resolver = $resolver;
    }

    /**
     * @param Template $template
     * @param ViewModel $viewModel
     */
    public function insertViewModelIntoTemplate(Template $template, ViewModel $viewModel) {
        $targetType = $this->getTargetType($template);
        $renderedFields = $this->resolveFields($template,$viewModel,$targetType);
        switch($targetType) {
            case RenderableElementResolver::TARGET_TYPE_STRING:
                $rawContent = '';
                break;
            case RenderableElementResolver::TARGET_TYPE_PHTML:

                break;
            case RenderableElementResolver::TARGET_TYPE_DOMELEMENT:

                break;
            default:
                throw new \InvalidArgumentException('Template must be either of type plain, PHTML or DOM');
                break;
        }
    }

    /**
     * @param Template $template
     * @return string
     */
    protected function getTargetType(Template $template) {
        $targetType = RenderableElementResolver::TARGET_TYPE_STRING;
        if($template instanceof PlainTemplate) {
            $targetType = RenderableElementResolver::TARGET_TYPE_STRING;
        } elseif($template instanceof PHTMLTemplate) {
            $targetType = RenderableElementResolver::TARGET_TYPE_PHTML;
        } elseif($template instanceof DOMTemplate) {
            $targetType = RenderableElementResolver::TARGET_TYPE_DOMELEMENT;
        }
        return $targetType;
    }

    /**
     * @param Template $template
     * @param ViewModel $viewModel
     * @param $targetType
     * @return array
     */
    protected function resolveFields(Template $template, ViewModel $viewModel, $targetType) {
        $renderedTemplateFields = [];
        $templateFields = $template->getFields();
        $viewModelFields = $viewModel->getData();
        foreach($templateFields as $templateFieldName) {
            if(array_key_exists($templateFieldName,$viewModelFields)) {
                $renderedTemplateFields[$templateFieldName] = $this->resolver->resolveElement($viewModelFields[$templateFieldName],$targetType);
            }
        }
        return $renderedTemplateFields;
    }




}