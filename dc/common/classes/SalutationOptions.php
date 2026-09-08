<?php
namespace DynCom\dc\common\classes;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 14.07.2015
 * Time: 16:54
 */
class SalutationOptions
{

    /**
     * @param Templating $templating
     * @return array
     */
    public function getOptionsArray(Templating $templating) {
        return [
            $templating->getText('mr') => $templating->getText('mr'),
            $templating->getText('mrs') => $templating->getText('mrs'),
            $templating->getText('salutation_company') => $templating->getText('salutation_company')
        ];
    }

}