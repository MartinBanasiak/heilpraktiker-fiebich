<?php
namespace DynCom\dc\dcShop\rma\classes;
use DynCom\dc\common\interfaces\GenericViewInterface;
use DynCom\dc\dcShop\interfaces\TemplatingInterface;
use mysqli;

/**
 * Class RetShipmentView
 */
class RetShipmentView implements GenericViewInterface {

    //use genericViewTrait;

    protected $mappedFields = array('titleString', 'mainMessageString', 'errorString', 'noticeString', 'templating', 'shipment', 'inputEMail', 'inputPostCode', 'inputRequest', 'returnReasonCollection', 'linePrefill');
    protected $templateFile = 'RMARetShipmentTemplate.phtml';

    protected $shipment;
    protected $titleString;
    protected $mainMessageString = '';
    protected $errorString       = '';
    protected $noticeString      = '';
    protected $inputEMail;
    protected $inputPostCode;
    protected $inputRequest;
    protected $returnReasonCollection;
    protected $linePrefill       = array();


    /**
     * RetShipmentView constructor.
     * @param mysqli $mysqli
     * @param TemplatingInterface $templating
     * @param RetShipmentDocument $shipment
     */
    public function __construct(mysqli $mysqli, TemplatingInterface $templating, RetShipmentDocument $shipment ) {

        $this->returnReasonCollection = ReturnReason::getAll($mysqli, $templating);

        $this->templating = $templating;
        if ($shipment->linesSet) {
            $this->shipment = $shipment;
        } else {
            $shipment->setLines();
            $this->shipment = $shipment;
        }
    }

    /**
     * @return array
     */
    protected function _getMappedFields() {
        return $this->mappedFields;
    }

    /**
     * @return array
     */
    public function getMappedFields() {
        return $this->_getMappedFields();
    }

}