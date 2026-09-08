<?php
namespace DynCom\dc\dcShop\subscriptions\classes;
use DynCom\dc\common\interfaces\PHTMLTemplate;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/30/2015
 * Time: 1:39 PM
 */
class SubscriptionItemcardButtonViewPHTMLTemplate implements PHTMLTemplate {

    protected $path;
    protected $renderedContent;

    /**
     * SubscriptionItemcardButtonViewPHTMLTemplate constructor.
     */
    public function __construct() {
        $this->path =  rtrim(dirname(dirname(dirname(dirname(__DIR__)))),'/') . '/module/dcshop/subscriptions/templates/SubscriptionItemcardButtonTemplate.phtml';
    }

    /**
     * @return array
     */
    public function getFields() {
        $arr = [
            'buttonHTML',
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