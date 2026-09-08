<?php
namespace DynCom\dc\dcShop\subscriptions\classes;
use DynCom\dc\common\interfaces\ViewModel;
use DynCom\dc\common\traits\universallyGettableTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/30/2015
 * Time: 1:41 PM
 */
class SubscriptionItemcardButtonViewModel implements ViewModel
{

    use universallyGettableTrait;

    protected $styleFilePath = '/module/dcshop/subscriptions/styles/itemcard_subscription_form.css';
    protected $scriptletPath = '/module/dcshop/subscriptions/scripts/itemcard_subscription.js';
    protected $buttonHTML;

    /**
     * @param string $buttonHTML
     */
    public function __construct($buttonHTML)
    {
        $this->buttonHTML = $buttonHTML;
    }

    /**
     * @return array
     */
    public function getData()
    {
        $arr = [
            'styleFilePath' => $this->styleFilePath,
            'scriptletPath' => $this->scriptletPath,
            'buttonHTML' => $this->buttonHTML
        ];
        return $arr;
    }
}