<?php
namespace DynCom\dc\dcShop\rma\classes;
use DynCom\dc\common\interfaces\ViewModel;
use DynCom\dc\common\traits\universallyGettableTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 29.01.2015
 * Time: 14:28
 */

class RMAConfirmationMailViewModel implements ViewModel {

    use universallyGettableTrait;

    protected $mainContent = '';

    /**
     * RMAConfirmationMailViewModel constructor.
     * @param $mainContent
     */
    public function __construct($mainContent ) {
        $this->mainContent = (string)$mainContent;
    }

    /**
     * @param $content
     */
    public function setMainContent($content) {
        $this->mainContent = (string)$content;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->mainContent;
    }


}