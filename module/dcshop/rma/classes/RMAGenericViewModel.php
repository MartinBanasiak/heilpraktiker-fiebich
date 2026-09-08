<?php
namespace DynCom\dc\dcShop\rma\classes;
use DynCom\dc\common\interfaces\ViewModel;
use DynCom\dc\common\traits\universallyGettableTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 25.08.2015
 * Time: 15:36
 */
class RMAGenericViewModel implements ViewModel
{
    use universallyGettableTrait;

    protected $styleFilePath = '/module/dcshop/rma/styles/rma.css';
    protected $templateEngine;

    protected $titleString;
    protected $mainMessageString;
    protected $errors            = array();
    protected $errorString       = '';
    protected $noticeString      = '';

    /**
     * @param string $titleString
     * @param string $mainMessageString
     */
    public function __construct($titleString,$mainMessageString) {
        $this->titleString = (string)$titleString;
        $this->mainMessageString = (string)$mainMessageString;
    }

    /**
     * @param string $styleFilePath
     */
    public function setStyleFilePath($styleFilePath)
    {
        $this->styleFilePath = $styleFilePath;
    }

    /**
     * @param mixed $templateEngine
     */
    public function setTemplateEngine($templateEngine)
    {
        $this->templateEngine = $templateEngine;
    }

    /**
     * @param mixed $titleString
     */
    public function setTitleString($titleString)
    {
        $this->titleString = $titleString;
    }

    /**
     * @param mixed $mainMessageString
     */
    public function setMainMessageString($mainMessageString)
    {
        $this->mainMessageString = $mainMessageString;
    }

    /**
     * @param array $errors
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
    }

    /**
     * @param string $errorString
     */
    public function setErrorString($errorString)
    {
        $this->errorString = $errorString;
    }

    /**
     * @param string $noticeString
     */
    public function setNoticeString($noticeString)
    {
        $this->noticeString = $noticeString;
    }



    /**
     * @return array
     */
    public function getData()
    {
        return [
            'styleFilePath'     => $this->styleFilePath,
            'titleString'       => $this->titleString,
            'mainMessageString' => $this->mainMessageString,
            'errors'            => $this->errors,
            'errorString'       => $this->errorString,
            'noticeString'      => $this->noticeString
        ];
    }

}