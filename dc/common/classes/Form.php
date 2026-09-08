<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\ViewModel;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/13/2015
 * Time: 12:56 AM
 */

class Form {

    /**
     * @var \DOMNode
     */
    protected $el;
    protected $elText;
    protected $isValid = null;
    protected $fieldValidity = array();
    protected $prefillFromSuperglobal = false;
    protected $id = null;
    protected $fields = null;
    protected $fieldNames = null;
    protected $fieldLabels = array();
    protected $fieldRules = array();
    protected $IDWrapperIDMap;

    protected $isRendered = false;
    protected $rendered;
    protected $elementsByID = [];

    /**
     * @param \DOMElement $el
     */
    public function __construct( \DOMElement $el ) {

        if($el->tagName !== 'form') {
            throw new \InvalidArgumentException('Parameter must be \DOMElement with tagName \'form\'.');
        }
        $this->el = $el;
        $this->el->ownerDocument->validateOnParse = true;
        $this->id = $el->getAttribute('id');
    }

    /**
     * @param boolean $prefillFromSuperglobal
     */
    public function setPrefillFromSuperglobal($prefillFromSuperglobal)
    {
        $this->prefillFromSuperglobal = (bool)$prefillFromSuperglobal;
    }

    /**
     * @param FormElement $formElement
     * @param null $parentElementID
     * @param null $inputLabelText
     */
    public function addElement(FormElement $formElement, $parentElementID = null, $inputLabelText = null) {
        $newEl = $formElement->getEl();
        $doc = &$this->el->ownerDocument;
        @$doc->validate();
        $formMethod = $this->el->getAttribute('method');
        $formMethodArray = array();

        $fieldType = $newEl->tagName;
        $fieldName = $newEl->getAttribute('name');
        $fieldValue = $newEl->getAttribute('value');

        if(null !== $inputLabelText && $fieldName) {
            $this->fieldLabels[$fieldName] = $inputLabelText;
        }
        $fieldID = $newEl->getAttribute('id');
        $fieldIsHidden = ($newEl->getAttribute('type') === 'hidden');
        $parent = null;
        if($parentElementID) {
            $parent = $this->elementsByID[$parentElementID];
        }

        if($this->prefillFromSuperglobal) {

            if(strcasecmp($formMethod,'post') === 0) { $formMethod = 'post'; $formMethodArray = &$_POST;}
            if(strcasecmp($formMethod,'get') === 0) { $formMethod = 'get'; $formMethodArray = &$_GET;}

            if (($fieldType === 'input' || $fieldType === 'textarea') && isset($formMethodArray[$fieldName]) && !(mb_strlen($fieldValue, 'UTF-8') > 0)) {
                $fieldValue = $formMethodArray[$fieldName];
                $newEl->setAttribute('value', $fieldValue);
            }

            if ($fieldType === 'select' && isset($formMethodArray[$fieldName]) && !(mb_strlen($fieldValue, 'UTF-8') > 0)) {
                $optionValue = $formMethodArray[$fieldName];
                $this->setSelectedOptionByValue($newEl,$optionValue);
            }
        }

        if(($fieldType === 'input' || $fieldType === 'textarea' || $fieldType === 'select') && !$fieldIsHidden) {
            $this->fields[$fieldName] = $newEl->ownerDocument->saveXML($newEl);
            $this->fieldNames[] = $fieldName;
            if(null !== $inputLabelText) {
                $label = $doc->createElement('label');
                $label->setAttribute('for',$fieldName);
                $textNode = $doc->createTextNode($inputLabelText);
                $label->appendChild($textNode);
            }
        }

        $fieldEL = $newEl;
        if(($fieldType === 'input' || $fieldType === 'textarea' || $fieldType === 'select' || $fieldType === 'submit') && !$fieldIsHidden) {
            $elWrappingDiv = $doc->createElement('div');
            $totalID = '';
            if($fieldName !== '') {
                $totalID = 'input_' . $fieldName . '_wrapper';
            } elseif($fieldID !== '') {
                $totalID = 'input_' . $fieldID . '_wrapper';
            }
            $elWrappingDiv->setAttribute('id',$totalID);
            $elWrappingDiv->setIdAttribute('id',true);
            $elWrappingDiv->setAttribute('class','form_input_wrapper');
            $this->el->appendChild($elWrappingDiv);
            $elWrappingDiv->appendChild($newEl);
            $fieldEL = $elWrappingDiv;
            if($totalID !== '' && $fieldID !== '') {
                $this->IDWrapperIDMap[$fieldID] = $totalID;
            }
        }

        if(isset($label)) {
            $labelWrappingDiv = $doc->createElement('div');
            $labelWrappingDiv->setAttribute('class','form_label_wrapper');
            $totalID = '';
            if($fieldName !== '') {
                $totalID = 'combined_' . $fieldName . '_wrapper';
            } elseif($fieldID !== '') {
                $totalID = 'combined_' . $fieldID . '_wrapper';
            }
            $labelWrappingDiv->setAttribute('id',$totalID);
            $labelWrappingDiv->setIdAttribute('id',true);
            $labelWrappingDiv->appendChild($label);
            $totalWrappingDiv = $doc->createElement('div');
            @$totalWrappingDiv->setAttribute('id',$totalID);
            @$totalWrappingDiv->setIdAttribute('id',true);
            $totalWrappingDiv->appendChild($labelWrappingDiv);
            $totalWrappingDiv->appendChild($fieldEL);
            $fieldEL = $totalWrappingDiv;
            if($totalID !== '' && $fieldID !== '') {
                $this->IDWrapperIDMap[$fieldID] = $totalID;
            }
        }
        if(null !== $parent) {
            $finalEl = $parent->appendChild($fieldEL);
        } else {
            $finalEl = $this->el->appendChild($fieldEL);
        }
        if($fieldID) {
            $this->elementsByID[$fieldID] = &$finalEl;
        }
    }

    /**
     * @param \DOMElement $el
     * @param $value
     */
    public function setSelectedOptionByValue(\DOMElement $el, $value) {
        foreach($el->getElementsByTagName('option') as $option) {
            if($option instanceof \DOMElement) {
                $currValue = $option->getAttribute('value');
                $option->removeAttribute('selected');
                if ($currValue === $value) {
                    $option->setAttribute('selected', 'selected');
                }
            }
        }
    }

    /**
     * @param \DOMText $textNode
     * @param $id
     */
    public function setElementTextNodeByID(\DOMText $textNode, $id ) {
        $doc = $this->el->ownerDocument;
        $el = $doc->getElementById($id);
        if($el) {
            $el->insertBefore($textNode,$el->firstChild);
        }
    }

    /**
     * @param $id
     * @return bool
     */
    public function removeElementByID($id ) {
        $removalID = ($this->IDWrapperIDMap[$id]?:$id);
        $doc = $this->el->ownerDocument;
        $removalEl = $doc->getElementById($removalID);
        if($removalEl) {
            $removalParent = $removalEl->parentNode;
            return ($removalParent->removeChild($removalEl) === $removalEl);
        }
        return false;
    }

    /**
     * @param array $rulesArr
     */
    public function setFieldRules(array $rulesArr ) {
        foreach($rulesArr as $fieldName => $rulesString) {
            $sep = Validator::CONF_RULE_SEPARATOR;
            $rules = explode($sep,$rulesString);
            foreach($rules as $rule) {
                $this->setFieldRule($fieldName,$rule);
            }
        }
    }

    /**
     * @param $fieldName
     * @param $rule
     */
    public function setFieldRule($fieldName, $rule ) {
        $ruleArr = explode(':',$rule,2);
        $ruleName = $ruleArr[0];
        if(!array_key_exists($fieldName,$this->fields)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%s is not the name of an input-Field in this form. Available fields are %s',
                    $fieldName,
                    print_r(array_keys($this->fields),1)
                )
            );
        }

        if(!Validator::isValidDefaultInlineRuleName($ruleName)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '\'%s\' is not the name of a validation-rule.',
                    $ruleName
                )
            );
        }
        $this->fieldRules[$fieldName][$ruleName] = $rule;
    }

    public function render() {
        $doc = $this->el->ownerDocument;
        $this->addRequiredFieldClass();
        $this->setInvalidFieldClass();
        $this->rendered =  $doc->saveHTML();
        $this->isRendered = true;
    }

    public function getRenderedContent() {
        $this->render();
        return $this->rendered;
    }

    protected function addRequiredFieldClass() {
        $doc = $this->getDOMDocument();
        if(is_array($this->fieldRules)) {
            $XPath = new \DOMXPath($doc);
            $inputNodeList = $XPath->query('//input[@type!="hidden"] | //select | //textarea');
            $nodeArr = [];
            $inputCount = $inputNodeList->length;
            for ($i = 0; $i < $inputCount; ++$i) {
                $currNode = $inputNodeList->item($i);
                $name = $currNode->attributes->getNamedItem('name');
                if($name) {
                    $fieldName = $name->nodeValue;
                    $nodeArr[$fieldName] = $currNode;
                }
            }
            if(count($nodeArr) > 0) {
                foreach ($this->fieldRules as $fieldName => $ruleArr) {
                    foreach ($ruleArr as $ruleName => $rule) {
                        if ($ruleName === 'required') {
                            $labelNodeList = $XPath->query('//label[@for="' . $fieldName . '"]');
                            $labelCount = $labelNodeList->length;
                            if($labelCount === 1) {
                                $label = $labelNodeList->item(0);
                                $labelParent = $label->parentNode;
                                $labelClassAttr = $label->attributes->getNamedItem('class');
                                $labelParentClassAttr = $labelParent->attributes->getNamedItem('class');
                                if(!$labelClassAttr) {
                                    $labelClassAttr = $label->appendChild(new \DOMAttr('class'));
                                }
                                $value = ($labelClassAttr->nodeValue) ?: '';
                                if(strpos($value,' required_input_label') === false) {
                                    $value .= ' required_input_label';
                                }
                                $labelClassAttr->nodeValue = $value;
                                $parentValue = $labelParentClassAttr->nodeValue ?: '';
                                if(strpos($parentValue,' required_input_label_wrapper') === false) {
                                    $parentValue .= ' required_input_label_wrapper';
                                }
                                $labelParentClassAttr->nodeValue = $parentValue;
                            }
                            $currNode = $nodeArr[$fieldName];
                            $classAttr = $currNode->attributes->getNamedItem('class');
                            if(!$classAttr) {
                                $classAttr = $currNode->appendChild(new \DOMAttr('class'));
                            }
                            $value = $classAttr->nodeValue ?: '';
                            if(strpos($value,' required_input') === false) {
                                $value .= ' required_input';
                            }
                            $classAttr->nodeValue = $value;
                            $parent = $currNode->parentNode;
                            $parentClassAttr = $parent->attributes->getNamedItem('class');
                            if (stristr($parentClassAttr->nodeValue, 'form_input_wrapper') &&
                                (strpos($parentClassAttr->nodeValue,' required_input_wrapper') === false)) {
                                $parentClassAttr->nodeValue .= ' required_input_wrapper';
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @return \DOMDocument
     */
    public function getDOMDocument() {
        return $this->el->ownerDocument;
    }

    /**
     * @return \DOMElement
     */
    public function getEl() {
        return $this->el;
    }

    /**
     * @return \DOMDocument
     */
    public function getOwnerDocument() {
        return $this->el->ownerDocument;
    }

    /**
     * @return null
     */
    public function getFieldNames() {
        return $this->fieldNames;
    }

    /**
     * @return array
     */
    public function getNamesValidatedFields() {
        return array_keys($this->fieldRules);
    }

    /**
     * @return array
     */
    public function __sleep() {
        $doc = $this->el->ownerDocument;
        $this->elText = $doc->saveXML($this->el);
        $this->el = null;
        $propertiesToSerialize = array(
            'elText',
            'fields',
            'fieldNames',
            'fieldLabels',
            'fieldRules',
            'IDWrapperIDMap'
        );
        return $propertiesToSerialize;
    }

    public function __wakeup() {
        $tmpDoc = new \DOMDocument();
        @$tmpDoc->loadXML($this->elText);
        $el = $tmpDoc->documentElement;
        $this->el = $el;
    }

    /**
     * @return array
     */
    public function validateRequestAgainstRules() {
        $method = $this->el->getAttribute('method');
        $sourceArr = &$_REQUEST;
        if(strcasecmp($method,'post') === 0) {
            $sourceArr = &$_POST;
        } elseif(strcasecmp($method,'get') === 0) {
            $sourceArr = &$_GET;
        }
        $fieldNames = array_keys($this->fields);
        $fieldData = array();
        foreach($fieldNames as $name) {
            if(isset($sourceArr[$name])) {
                $fieldData[$name] = $sourceArr[$name];
            }
        }
        $rules = $this->getFieldRules();
        $ruleFields = array_keys($rules);
        $validator = new Validator($rules, $fieldData);
        $fieldStatusArr = array();
        $isValid = $validator->isValid($fieldStatusArr);

        foreach($ruleFields as $name) {
            $fieldLabel = (array_key_exists($name,$this->fieldLabels) ? $this->fieldLabels[$name] : '');
            $fieldStatusArr[$name]['fieldLabel'] = $fieldLabel;
        }

        $this->fieldValidity = $fieldStatusArr;
        $this->setInvalidFieldClass();
        $this->isValid = $isValid;

        return $fieldStatusArr;
    }

    /**
     * @return array
     */
    public function getFieldRules() {
        $returnArr = array();
        foreach($this->fieldRules as $fieldName => $ruleArr) {
            $ruleStr = '';
            $i = 0;
            foreach($ruleArr as $ruleName) {
                if($i > 0) {
                    $ruleStr .= Validator::CONF_RULE_SEPARATOR;
                }
                $ruleStr .= $ruleName;
                if($ruleStr !== '') {
                    $returnArr[$fieldName] = $ruleStr;
                }
                ++$i;
            }
        }
        return $returnArr;
    }

    /**
     * @return null
     */
    public function isRequestValid() {
        if(null === $this->isValid) {
            throw new \LogicException(
                sprintf(
                    '%s needs to be called before %s can be called.',
                    '"validateRequestAgainstRules"',
                    __METHOD__
                )
            );
        }
        return $this->isValid;
    }

    /**
     * @return null|string
     */
    public function getID() {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getFieldStatusArr() {
        return $this->fieldValidity;
    }

    public function validatePrefillAndAddErrors() {
        $statusArr = $this->validateInputValuesAgainstRules();
        $fields = array_keys($statusArr);
        foreach($fields as $fieldName) {
            $errorMsgs = $this->getFieldErrorMsgs($fieldName);
            foreach($errorMsgs as $msg) {
                $this->addUserFormError($msg);
            }
        }
    }

    /**
     * @return array
     */
    public function validateInputValuesAgainstRules() {
        $inputs = $this->el->getElementsByTagName('input');
        $nodeCount = $inputs->length;
        $rules = $this->getFieldRules();
        $ruleFields = array_keys($rules);
        $fieldData = array();
        for($i = 0;$i < $nodeCount;++$i) {
            $currEl = $inputs->item($i);
            $name = $currEl->getAttribute('name');
            $value = $currEl->getAttribute('value');
            if($name && $value && in_array($name,$ruleFields)) {
                $fieldData[$name] = $value;
            }
        }
        if((count($fieldData) > 0) && (count($rules) > 0)) {
            $validator = new Validator($rules, $fieldData);
            $fieldStatusArr = array();
            $isValid = $validator->isValid($fieldStatusArr);
            $this->fieldValidity = $fieldStatusArr;
            $this->setInvalidFieldClass();
            $this->isValid = $isValid;

            return $fieldStatusArr;
        } else {
            return array();
        }
    }

    protected function setInvalidFieldClass() {
        if($this->fieldValidity) {
            $doc = $this->getOwnerDocument();
            $XPath = new \DOMXPath($doc);
            $inputNodeList = $XPath->query('//input[@type!="hidden"] | //select | //textarea');
            $nodeArr = [];
            $inputCount = $inputNodeList->length;
            for ($i = 0; $i < $inputCount; ++$i) {
                $currNode = $inputNodeList->item($i);
                $name = $currNode->attributes->getNamedItem('name');
                if($name) {
                    $fieldName = $name->nodeValue;
                    $nodeArr[$fieldName] = $currNode;
                }
            }
            foreach($this->fieldValidity as $fieldName => $fieldStatus) {
                if(isset($fieldStatus['unmatchedRules'])) {
                    $currNode = $nodeArr[$fieldName];
                    $labelNodeList = $XPath->query('//label[@for="' . $fieldName . '"]');
                    $labelCount = $labelNodeList->length;
                    if($labelCount === 1) {
                        $label = $labelNodeList->item(0);
                        $labelParent = $label->parentNode;
                        $labelClassAttr = $label->attributes->getNamedItem('class');
                        if(!$labelClassAttr) {
                            $labelClassAttr = $label->appendChild(new \DOMAttr('class'));
                        }
                        $labelParentClassAttr = $labelParent->attributes->getNamedItem('class');
                        if(!$labelParentClassAttr) {
                            $labelParentClassAttr = $labelParent->appendChild(new \DOMAttr('class'));
                        }
                        $labelValue = $labelClassAttr->nodeValue ?: '';
                        $labelValue .= 'invalid_input_label';
                        $labelClassAttr->nodeValue = $labelValue;
                        $labelParentValue = $labelParentClassAttr->nodeValue ?: '';
                        $labelParentValue .= ' invalid_input_label_wrapper';
                        $labelParentClassAttr->nodeValue = $labelParentValue;
                    }
                    $classAttr = $currNode->attributes->getNamedItem('class');
                    $classAttr->nodeValue .= ' invalid_input';
                    $parent = $currNode->parentNode;
                    $parentClassAttr = $parent->attributes->getNamedItem('class');
                    if (stristr($parentClassAttr->nodeValue, 'form_input_wrapper')) {
                        $parentClassAttr->nodeValue .= ' invalid_input_wrapper';
                    }
                }
            }
        }
    }

    /**
     * @param $fieldName
     * @return array
     */
    public function getFieldErrorMsgs($fieldName ) {
        $msgArr = array();
        if(isset($this->fieldValidity[$fieldName]['unmatchedRules'])) {
            $label = ((isset($this->fieldLabels[$fieldName])) ? $this->fieldLabels[$fieldName]:$fieldName);
            foreach($this->fieldValidity[$fieldName]['unmatchedRules'] as $ruleArr) {
                $errorMsgRaw = $ruleArr['ruleError'];
                $errorMsg = str_replace('%fieldname%','<strong>'.$label.'</strong>',$errorMsgRaw);
                $errorMsg = str_replace('%validationparam%','<strong>'.$ruleArr['ruleValue'].'</strong>',$errorMsg);
                $msgArr[] = $errorMsg;
            }
        }
        return $msgArr;
    }

    /**
     * @param $errorText
     */
    public function addUserFormError($errorText) {
        $doc = $this->el->ownerDocument;
        $errorDiv = $doc->createElement('div');
        $errorDiv->setAttribute('class','form_error form_error_' . $this->id);
        $errorDivText = $doc->createTextNode(strip_tags($errorText));
        $errorDiv->appendChild($errorDivText);
        $this->el->insertBefore($errorDiv,$this->el->firstChild);
    }

    /**
     * @param array $arr
     */
    public function doPrefillFromArray(array &$arr) {
        $XPath = new \DOMXPath($this->el->ownerDocument);
        $inputNodeList = $XPath->query('//input[@type!=\'button\' and @type!=\'submit\' and not(starts-with(@name,\'fmd\'))] | //textarea | //select');
        $inputCount = $inputNodeList->length;
        for($i=0;$i<$inputCount;++$i) {
            $currNode = $inputNodeList->item($i);
            $nameAttr = $currNode->attributes->getNamedItem('name');
            $nameAttr = $nameAttr ?: $currNode->attributes->getNamedItem('id');
            $currNodeName = $nameAttr->nodeValue;
            $newVal = (array_key_exists($currNodeName,$arr)) ? $arr[$currNodeName] : null;
            if(isset($newVal) && mb_strlen($currNodeName,'UTF-8') > 0) {
                if($currNode->nodeName === 'select') {
                    $this->setSelectedOptionByValue($currNode,$newVal);
                } else {
                    $valueAttr = $currNode->attributes->getNamedItem('value');
                    $valueAttr = $valueAttr ?: $currNode->appendChild(new \DOMAttr('value'));
                    $prevVal = $valueAttr->nodeValue;
                    $newVal = $newVal ?: $prevVal;
                    $valueAttr->nodeValue = $newVal;
                }
            }
        }
    }

    /**
     * @param ViewModel $viewModel
     */
    public function doPrefillFromViewModel(ViewModel $viewModel) {
        $arr = $viewModel->getData();
        $this->doPrefillFromArray($arr);
    }

    public function doPrefillFromSuperglobal() {
        $formMethod = $this->el->getAttribute('method');
        $formMethodArray = array();
        if(strcasecmp($formMethod,'post') === 0) { $formMethod = 'post'; $formMethodArray = &$_POST;}
        if(strcasecmp($formMethod,'get') === 0) { $formMethod = 'get'; $formMethodArray = &$_GET;}
        $this->doPrefillFromArray($formMethodArray);
    }

    /**
     * @return bool
     */
    public function removeMetaDataFields() {
        return $this->removeElementsByXPathQuery('//input[starts-with(@name,\'fmd\')]');
    }

    /**
     * @param $query
     * @return bool
     */
    public function removeElementsByXPathQuery($query) {
        $xpath = new \DOMXPath($this->el->ownerDocument);
        $somethingRemoved = false;
        foreach($xpath->query($query) as $e ) {
            // Delete this node
            $e->parentNode->removeChild($e);
            $somethingRemoved = true;
        }
        return $somethingRemoved;
    }

    /**
     * @param $id
     * @param $class
     */
    public function wrapInDIV($id, $class) {
        $div = $this->el->ownerDocument->createElement('div');
        $div->setAttribute('id',strip_tags($id));
        $div->setAttribute('class',strip_tags($class));
        $this->el->ownerDocument->appendChild($div);
        $div->appendChild($this->el);
    }

    /**
     * @var array
     */
    protected $registeredViewModels = [];

    /**
     * @param ViewModel $viewModel
     * @param array $fieldInputMappings
     * @throws \InvalidArgumentException
     */
    public function registerViewModel(ViewModel $viewModel, array $fieldInputMappings) {
        $viewModelClassName = get_class($viewModel);
        $viewModelInitValues = $viewModel->getData();
        //$this->viewModelInitValues[$viewModelClassName] = $viewModel->getData();
        $viewModelFields = array_keys($viewModelInitValues);
        $formFields = array_keys($this->fields);
        $mappingFormFields = array_keys($fieldInputMappings);
        $mappingViewModelFields = array_values($fieldInputMappings);
        $allFormFieldsValid = count(array_intersect($mappingFormFields, $formFields)) === count($mappingFormFields);
        $allViewModelFieldsValid = count(array_intersect($mappingViewModelFields,$viewModelFields)) === count($mappingViewModelFields);
        if(!$allFormFieldsValid) {
            throw new \InvalidArgumentException('The keys of paramter fieldInputMappings must all be inputs registered with the form.');
        }
        if(!$allViewModelFieldsValid) {
            throw new \InvalidArgumentException('The values of paramter fieldInputMappings must all be fields of the viewModel.');
        }
        $this->registeredViewModels[$viewModelClassName]['initValuesArray'] = $viewModelInitValues;
        $this->registeredViewModels[$viewModelClassName]['fieldMappings'] = $fieldInputMappings;
    }

    /**
     * @param ViewModel $viewModel
     * @param array $fieldFilter
     */
    public function prefillFromViewModel(ViewModel $viewModel, array $fieldFilter = []) {
        $viewModelClassName = get_class($viewModel);
        if(!array_key_exists('fieldMappings',$this->registeredViewModels[$viewModelClassName])) {
            throw new \InvalidArgumentException("ViewModel $viewModelClassName has not already been registered successfully.");
        }
        $viewModelFieldMappings = $this->registeredViewModels[$viewModelClassName]['fieldMappings'];
        $mapToFields = array_keys($viewModelFieldMappings);
        $mapFromViewModelFields = array_values($viewModelFieldMappings);
        $mappingData = [];
        $viewModelData = $viewModel->getData();
        foreach($mapToFields as $fieldName) {
            $mappingData[$fieldName] = $viewModelData[$fieldName];
        }
        $XPath = new \DOMXPath($this->el->ownerDocument);
        $inputNodeList = $XPath->query('//input[not(starts-with(@name,\'fmd\'))] | //textarea | //select');
        $inputCount = $inputNodeList->length;
        for($i=0;$i<$inputCount;++$i) {
            $currNode = $inputNodeList->item($i);
            $currNodeName = '';
            @$currNodeName = $currNode->attributes->getNamedItem('name')->nodeValue;
            if(array_key_exists($currNodeName,$mappingData) && isset($formMethodArray[$currNodeName]) && mb_strlen($currNodeName, 'UTF-8') > 0) {                
                if ($currNode->nodeName === 'select') {
                    $val = $mappingData[$currNodeName];
                    $this->setSelectedOptionByValue($currNode, $val);
                } else {
                    @$currNode->setAttribute('value', $mappingData[$currNodeName]);
                }                
            }
        }

    }

    /**
     * @return bool
     */
    public function isFormRendered() {
        return $this->isRendered;
    }

    /**
     * @param $fieldName
     * @param $fieldValue
     */
    public function setFieldData($fieldName, $fieldValue) {
        if(!in_array($fieldName,$this->fieldNames,false)) {
            throw new \InvalidArgumentException(
                'No field of name ' . strip_tags($fieldName) . ' is registered in the current form.'
            );
        }
        $XPath = new \DOMXPath(($this->el->ownerDocument));
        $nodeList = $XPath->query('//input[@name=\'' . $fieldName . '\']');
        if(!$nodeList->length > 0) {
            $nodeList = $XPath->query('//select[@name=\'' . $fieldName . '\']');
            if(!$nodeList->length > 0) {
                $nodeList = $XPath->query('//textarea[@name=\'' . $fieldName . '\']');
            }
        }
        if($nodeList->length === 1) {
            $field = $nodeList->item(0);
            $tagName = $field->nodeName;
            switch($tagName) {
                case 'input':
                    @$field->setAttribute('value', strip_tags($fieldValue));
                    break;
                case 'select':
                    $this->setSelectedOptionByValue($field,$fieldValue);
                    break;
                case 'textarea':
                    @$field->setAttribute('value', strip_tags($fieldValue));
                    break;
                default: break;
            }
        }
    }

    /**
     * @return array
     */
    public function getAllFieldData() {
        $XPath = new \DOMXPath(($this->el->ownerDocument));
        $nodeList = $XPath->query('//input | //select | //textarea');
        $arr = [];
        $len = $nodeList->length;
        for($i=0;$i < $len;++$i) {
            $currItem = $nodeList->item($i);
            @$nameAttr = $currItem->attributes->getNamedItem('name');
            @$valueAttr = $currItem->attributes->getNamedItem('value');
            @$nameVal = $nameAttr->nodeValue;
            @$valueVal = $valueAttr->nodeValue;
            if($nameVal && $valueVal) {
                $arr[$nameVal] = $valueVal;
            }
        }
        return $arr;
    }



}