<?php
namespace DynCom\dc\common\traits;
use DynCom\dc\common\classes\Form;

/**
 * Created by PhpStorm.
 * User: Michael Bauer
 * Date: 7/13/2015
 * Time: 7:22 PM
 */
trait genericViewModelTrait
{

    use arrayGettableTrait;

    /**
     * @return bool
     */
    public function allFormsValid() {
        $arr = $this->getAllFieldsAsArray();
        foreach($arr as $fieldName => $fieldValue) {
            if($fieldValue instanceof Form) {
                $fieldValue->validateRequestAgainstRules();
                if (!$fieldValue->isRequestValid()) {
                    return false;
                }
            }
        }
        return true;
    }

}