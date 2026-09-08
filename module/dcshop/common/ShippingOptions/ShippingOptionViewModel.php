<?php
/**
 * Created by PhpStorm.
 * User: lorenz
 * Date: 06.03.2018
 * Time: 10:58
 */

namespace DynCom\dc\dcShop\ShippingOptions;


use DynCom\dc\common\classes\SelectionCriteriaHelper;
use DynCom\dc\dcShop\classes\CurrShopConfiguration;
use DynCom\dc\regionalization\RegionalizedTextProvider;
use function foo\func;

class ShippingOptionViewModel
{

    /** @var ShippingOption  */
    public $shippingOption;

    /** @var ShippingOptionCollection */
    public $shippingOptionCollection;

    private $headLine;

    private $selectedLineNo;

    private $isSelected;

    private $isSelectedOption;

    private $isChecked;

    private $count;

    private $counter;

    private $numberFormatter;

    private $formatAmount;
    private $currency_code;

    /**
     * ShippingOptionViewModel constructor.
     * @param RegionalizedTextProvider $regionalizedTextProvider
     * @param $locale_code
     * @param $currency_code
     */
    public function __construct(RegionalizedTextProvider $regionalizedTextProvider, $locale_code, $currency_code)
    {
        $config = new ShippingOptionConfig();
        $shippingOption = new ShippingOption($config);
        $shippingOptionCollection = new ShippingOptionCollection($config, new SelectionCriteriaHelper());
        $this->shippingOption = $shippingOption;
        $this->shippingOptionCollection = $shippingOptionCollection;
        $this->setHeadLine($regionalizedTextProvider);
        $this->currency_code = $currency_code === '' ? 'EUR' : $currency_code;

        $this->counter = 0;
        $this->count = function() {
            return $this->counter++;
        };

        $this->numberFormatter = \NumberFormatter::create($locale_code,\NumberFormatter::CURRENCY);

        $this->formatAmount = function ($amount, \Mustache_LambdaHelper $helper){
            $amount = $helper->render($amount);
            $amountFormatted = $this->numberFormatter->formatCurrency($amount,$this->currency_code);
            return $amountFormatted;
        };
    }

    protected function setHeadLine(RegionalizedTextProvider $regionalizedTextProvider) :void
    {
        $this->headLine = $regionalizedTextProvider->getRegionalizedText('shipping_options');
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        return $this->shippingOption->$name;
    }

    public function __set($name, $value)
    {

    }

    public function __isset($name)
    {
        return isset($this->$name) || isset($this->shippingOption->$name);
    }

    /**
     * @param mixed $selectedLineNo
     */
    public function setSelectedLineNo($selectedLineNo): void
    {
        $this->selectedLineNo = $selectedLineNo;
    }

    public function setIsSelected(): void
    {
        $this->isSelected = function($lineNo, \Mustache_LambdaHelper $helper){
            if ((int)$helper->render($lineNo) === (int)$this->selectedLineNo) {
                return 'selected';
            }
            return '';
        };
    }

    public function setIsSelectedOption(): void
    {
        $this->isSelectedOption = function($lineNo, \Mustache_LambdaHelper $helper){
            if ((int)$helper->render($lineNo) === (int)$this->selectedLineNo) {
                return 'selected="selected"';
            }
            return '';
        };
    }

    public function setIsChecked(): void
    {
        $this->isChecked = function($lineNo, \Mustache_LambdaHelper $helper){
            if ((int)$helper->render($lineNo) === (int)$this->selectedLineNo) {
                return 'checked="checked"';
            }
            return '';
        };
    }

}