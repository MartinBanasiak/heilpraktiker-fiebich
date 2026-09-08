<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\PHTMLTemplate;
use DynCom\dc\common\interfaces\PHTMLView;
use DynCom\dc\common\interfaces\ViewModel;
use DynCom\dc\common\traits\genericViewTrait;

/**
 * Created by PhpStorm.
 * User: Michael Bauer
 * Date: 7/14/2015
 * Time: 2:19 AM
 */
class GenericPHTMLView implements PHTMLView
{

    use genericViewTrait;

    protected $template;
    protected $model;
    protected $renderedContent = false;

    /**
     * GenericPHTMLView constructor.
     * @param PHTMLTemplate $template
     * @param ViewModel $viewModel
     */
    public function __construct(PHTMLTemplate $template, ViewModel $viewModel) {
        $this->template = $template;
        $this->model = (object)$viewModel->getData();
    }

    /**
     * @return bool|string
     */
    public function render() {
        if($this->renderedContent) return $this->renderedContent;
        ob_start();
        include_once($this->template->getPHTMLPath());
        $this->renderedContent = ob_get_clean();
        return $this->renderedContent;
    }

}