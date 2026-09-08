<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\classes\Address;
use DynCom\dc\common\classes\FormBuilder;
use DynCom\dc\common\classes\SalutationOptions;
use DynCom\dc\common\classes\Templating;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 14.07.2015
 * Time: 12:50
 */
class AddressFormBuilder
{
    const FORM_CLASS_ADDRESS_FORMS = 'address_form';

    protected $buildSalutation = true;
    protected $buildFirstName = true;
    protected $buildLastName = true;
    protected $buildCompanyName = true;
    protected $buildStreetName = true;
    protected $buildStreetNo = true;
    protected $buildStreetPlusNo = false;
    protected $buildZIP = true;
    protected $buildCity = true;
    protected $buildCountry = true;

    protected $formBuilder;
    protected $salutationOptions;
    protected $templating;
    protected $countryCollection;

    protected $buildForm;

    /**
     * AddressFormBuilder constructor.
     * @param FormBuilder $formBuilder
     * @param Templating $templating
     * @param CountryCollection $countryCollection
     */
    public function __construct(FormBuilder $formBuilder, Templating $templating, CountryCollection $countryCollection ) {
        $this->formBuilder = $formBuilder;
        $this->templating = $templating;
        $so = new SalutationOptions();
        $this->salutationOptions = $so->getOptionsArray($this->templating);
        $this->countryCollection = $countryCollection;
    }

    /**
     * @param $formNameID
     * @param string $action
     * @param string $method
     * @param string $headline
     * @return \DynCom\dc\common\classes\Form
     */
    public function getAddressForm($formNameID, $action = '', $method = 'post', $headline = '') {
        $fb = $this->formBuilder;
        $addr = new Address('dummy');

        $superGlobalRef = &$_POST;
        if($method !== 'post') {
            $superGlobalRef = &$_GET;
            $method = 'get';
        }
        $options = [
            'method' => $method,
            'action' => $action,
            'class' => self::FORM_CLASS_ADDRESS_FORMS
        ];
        $fb->reset($formNameID,$options);
        if($headline !== '') {
            $wrapperID = $formNameID . '_headline';
            $innerID = $formNameID . '_headline_inner';
            $fb->addDIV($formNameID . '_form_wrapper','form_wrapper');
            $fb->addDIV($wrapperID,'address_form_headline');
            $fb->addSpan($innerID,null,$wrapperID,$headline);
        }
        $defaultValidationRules = $addr->getFieldValidationRules();
        $newValidationRules = [];

        $salutationNameID = $formNameID . '_input_salutation';
        $fb->addSelect($salutationNameID,$salutationNameID,'select_input',
            null,$superGlobalRef[$salutationNameID],$this->templating->getText('salutation'),$this->salutationOptions);

        $firstNameNameID = $formNameID . '_input_first_name';
        $newValidationRules[$firstNameNameID] = $defaultValidationRules['surname'];
        $fb->addTextInput($firstNameNameID,$firstNameNameID,'text_input',null,$superGlobalRef[$firstNameNameID],$this->templating->getText('first_name'));

        $lastNameNameID = $formNameID . '_input_last_name';
        $newValidationRules[$lastNameNameID] = $defaultValidationRules['lastname'];
        $fb->addTextInput($lastNameNameID,$lastNameNameID,'text_input',null,$superGlobalRef[$lastNameNameID],$this->templating->getText('last_name'));

        $companyNameNameID = $formNameID . '_input_company_name';
        $newValidationRules[$companyNameNameID] = $defaultValidationRules['company_name'];
        $fb->addTextInput($companyNameNameID,$companyNameNameID,'text_input',null,$superGlobalRef[$companyNameNameID],$this->templating->getText('company_name'));

        $streetNameNameID = $formNameID . '_input_street_name';
        $newValidationRules[$streetNameNameID] = $defaultValidationRules['address_street'];
        $fb->addTextInput($streetNameNameID,$streetNameNameID,'text_input',null,$superGlobalRef[$streetNameNameID],$this->templating->getText('street_street_no'));

        $streetNoNameID = $formNameID . '_input_street_no';
        $newValidationRules[$streetNoNameID] = $defaultValidationRules['address_no'];
        $fb->addTextInput($streetNoNameID,$streetNoNameID,'text_input',null,$superGlobalRef[$streetNoNameID]);

        $postCodeNameID = $formNameID . '_input_post_code';
        $newValidationRules[$postCodeNameID] = $defaultValidationRules['post_code'];
        $fb->addTextInput($postCodeNameID,$postCodeNameID,'text_input',null,$superGlobalRef[$postCodeNameID],$this->templating->getText('post_code_city'));

        $cityNameID = $formNameID . '_input_city';
        $newValidationRules[$cityNameID] = $defaultValidationRules['city'];
        $fb->addTextInput($cityNameID,$cityNameID,'text_input',null,$superGlobalRef[$cityNameID]);

        $countryCodeNameID = $formNameID . '_input_country_code';
        $newValidationRules[$countryCodeNameID] = $defaultValidationRules['country'];
        $countryCodeOptions = [];
        for($this->countryCollection->rewind();$this->countryCollection->isCurrValid();$this->countryCollection->next()) {
            $curr = $this->countryCollection->current();
            $countryCodeOptions[$curr->code] = $curr->description;
        }

        $fb->addSelect($countryCodeNameID,$countryCodeNameID,'input_select',null,$superGlobalRef[$countryCodeNameID],
                        $this->templating->getText('country'),$countryCodeOptions);

        $fb->setFieldRulesWithRuleString($newValidationRules);
        $fb->render('dcshop');
        return $fb->getForm();
    }

}