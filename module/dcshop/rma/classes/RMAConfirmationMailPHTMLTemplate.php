<?php
namespace DynCom\dc\dcShop\rma\classes;
use DynCom\dc\common\interfaces\PHTMLTemplate;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 27.08.2015
 * Time: 10:45
 */
class RMAConfirmationMailPHTMLTemplate implements PHTMLTemplate
{
    protected $path = '';
    protected $renderedContent = '';

    /**
     * RMAConfirmationMailPHTMLTemplate constructor.
     */
    public function __construct() {
        $this->path = rtrim(dirname(dirname(dirname(dirname(__DIR__)))),'/') . '/module/dcshop/rma/templates/RMAConfirmationMailTemplate.phtml';
    }
    /**
     * @return string
     */
    public function getPHTMLPath()
    {
        return $this->path;
    }

    /**
     * @param string $contentString
     */
    public function setRenderedContent($contentString)
    {
        $this->renderedContent = $contentString;
    }

    /**
     * @return string
     */
    public function getRenderedContent() {
        return $this->renderedContent;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return [
            'mainContent'
        ];
    }
}