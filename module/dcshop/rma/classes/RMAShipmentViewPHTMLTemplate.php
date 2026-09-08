<?php
namespace DynCom\dc\dcShop\rma\classes;
use DynCom\dc\common\interfaces\PHTMLTemplate;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/23/2015
 * Time: 3:38 PM
 */

class RMAShipmentViewPHTMLTemplate implements PHTMLTemplate {

    protected $path;
    protected $renderedContent;

    /**
     * RMAShipmentViewPHTMLTemplate constructor.
     */
    public function __construct() {
        $this->path =  rtrim(dirname(dirname(dirname(dirname(__DIR__)))),'/') . '/module/dcshop/rma/templates/RMARetShipmentTemplate.phtml';
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
            'shipmentLabel',
            'shipmentNoLabel',
            'shipment',
            'nameLabel',
            'postingDateLabel',
            'addressLabel',
            'postCodeLabel',
            'cityLabel',
            'inputEMail',
            'inputPostCode',
            'inputRequest',
            'itemNoLabel',
            'descriptionLabel',
            'quantityLabel',
            'qtyReturnedLabel',
            'qtyRemainingLabel',
            'returnQuantityLabel',
            'returnReasonLabel',
            'pleaseChooseText',
            'returnReasonCollection',
            'linePrefill',
            'yourReferenceLabel',
            'linkBack',
            'backButtonLabel',
            'sendButtonLabel',
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