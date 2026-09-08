<?php
namespace DynCom\dc\dcShop\rma\classes;
use DynCom\dc\common\interfaces\PHTMLTemplate;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/23/2015
 * Time: 1:53 AM
 */

class RMASearchPHTMLTemplate implements PHTMLTemplate {

    protected $path;
    protected $renderedContent;

    /**
     * RMASearchPHTMLTemplate constructor.
     */
    public function __construct() {
        $this->path =  rtrim(dirname(dirname(dirname(dirname(__DIR__)))),'/') . '/module/dcshop/rma/templates/RMASearchTemplate.phtml';
    }

    /**
     * @return array
     */
    public function getFields() {
        $arr = [
            'titleString',
            'mainMessageString',
            'errorString',
            'noticeString',
            'emailLabel',
            'inputEMail',
            'postCodeLabel',
            'inputPostCode',
            'searchFieldLabel',
            'inputRequest',
            'searchPrefill',
            'submitLabel',
            'showShipmentLabel',
            'shipmentNoLabel',
            'postingDateLabel',
            'yourReferenceLabel',
            'addressLabel',
            'postCodeLabel',
            'cityLabel',
            'retShipmentCollection',
            'scriptletPath',
            'styleFilePath'
        ];
        return $arr;
    }

    /**
     * @return string
     */
    public function getPHTMLPath() {
        return $this->path;
    }

    /**
     * @param $content
     */
    public function setRenderedContent($content) {
        $this->renderedContent = $content;
    }

    /**
     * @return mixed
     */
    public function getRenderedContent() {
        return $this->renderedContent;
    }

}