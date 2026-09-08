<?php
namespace DynCom\dc\dcShop\rma\classes;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;

/**
 * Class ReturnReason
 */
class ReturnReason implements GenericDBModelInterface, Entity
{

    use genericDBModelTrait, universallyGettableTrait;

    protected $id;
    protected $company;
    protected $code;
    protected $description;
    protected $to_delete;

    /**
     * @param ReturnReasonConfig $config
     */
    public function __construct( ReturnReasonConfig $config ) {
        $this->config = $config;
    }

    /**
     * @param $value
     *
     * @return bool
     */
    protected function _setDescription( $value ) {
        if (!(strlen($value) > 0)) {
            return FALSE;
        }
        $this->description = (string)$value;
        $this->updateHooks('changed',$this);
        return TRUE;
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public function setDescription( $value ) {
        return $this->_setDescription($value);
    }
}