<?php
namespace DynCom\dc\dcShop\rma\classes;
use DynCom\dc\common\interfaces\PHTMLTemplate;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 25.08.2015
 * Time: 15:36
 */
class RMAGenericViewPHTMLTemplate implements PHTMLTemplate
{
    protected $path = '';
    protected $renderedContent = '';

    /**
     * RMAGenericViewPHTMLTemplate constructor.
     */
    public function __construct() {
        $this->path = rtrim(dirname(dirname(dirname(dirname(__DIR__)))),'/') . '/module/dcshop/rma/templates/RMAGenericRMAPageTemplate.phtml';
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
            'titleString',
            'mainMessageString'
        ];
    }

}