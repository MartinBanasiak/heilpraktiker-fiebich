<?php
namespace DynCom\dc\dcShop\rma\classes;
use DynCom\dc\common\traits\universallyGettableTrait;

/**
 * Class RMAConfirmationRequestView
 */
class RMAGenericRMAPageViewModel {

    use universallyGettableTrait;

    protected $template = '/module/dcshop/rma/templates/RMAGenericRMAPageTemplate.phtml';
    protected $styleFilePath = '';
    protected $titleString;
    protected $mainMessageString;

    /**
     * @param $titleString
     * @param $mainMessageString
     */
    public function __construct( $titleString, $mainMessageString ) {
        $this->titleString = (string)$titleString;
        $this->mainMessageString = (string)$mainMessageString;
    }

    /**
     * @param string $template
     */
    public function setTemplate( $template ) {
        $this->template = $template;
    }

    /**
     * @param string $styleFilePath
     */
    public function setStyleFilePath( $styleFilePath ) {
        $this->styleFilePath = $styleFilePath;
    }

}