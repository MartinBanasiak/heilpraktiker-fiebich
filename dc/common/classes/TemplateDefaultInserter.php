<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\abstracts\TemplateInserterBase;
use DynCom\dc\common\interfaces\PHTMLTemplate;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/22/2015
 * Time: 11:23 PM
 */

class TemplateDefaultInserter extends TemplateInserterBase {

    protected $model;

    /**
     * TemplateDefaultInserter constructor.
     * @param PHTMLTemplate $template
     * @param array $viewModel
     * @param bool $allowTags
     */
    public function __construct(PHTMLTemplate $template, $viewModel, $allowTags = false) {
        $this->template = $template;
        $this->model = $viewModel;
        $this->allowTags = (bool)$allowTags;
    }

    /**
     * @param array $fieldValues
     * @return bool
     */
    protected function _validateFieldData(array $fieldValues) {
        return true;
    }

    public function insertData() {
        $path = $this->template->getPHTMLPath();
        if(file_exists(realpath($path))) {
            ob_start();
            include($path);
            $content = ob_get_clean();
        } else {
            throw new \InvalidArgumentException("No file under '" . $path . "'.");
        }
        $this->template->setRenderedContent($content);
    }

}