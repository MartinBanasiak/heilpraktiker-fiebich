<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\GenericViewInterface;
use DynCom\dc\common\interfaces\Template;
use DynCom\dc\common\interfaces\ViewModel;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 22.01.2015
 * Time: 14:22
 */

class GenericView implements GenericViewInterface {

    /**
     * @var ViewModel
     */
    protected $model;

    /**
     * @var Template
     */
    protected $template;
    /**
     * @var string
     */
    protected $renderedContent;

    /**
     * @var bool
     */
    protected $isRendered = false;

    /**
     * @param Template $template
     * @param ViewModel $model
     */
    public function __construct(Template $template, ViewModel $model) {
        $this->template = $template;
        $this->model = $model;
    }

    /**
     * @param TemplateInserterFactory $inserterFactory
     */
    public function render(TemplateInserterFactory $inserterFactory) {
        $viewModelData = $this->model->getData();
        $inserter = $inserterFactory->getInserter($this->template,$viewModelData);
        if(!isset($inserter)) {throw new \InvalidArgumentException('Template is not of allowed type. Allowed types are default, string, or DOM.');}
        $inserter->insertData();
        $this->renderedContent = $this->template->getRenderedContent();
        $this->isRendered = true;
    }

    /**
     * @return string
     */
    public function getContent() {
        return $this->renderedContent;
    }

    public function echoContent() {
        echo $this->renderedContent;
    }

    /**
     * @return bool
     */
    public function isRendered() {
        return $this->isRendered;
    }

}