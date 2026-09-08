<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\ViewModel;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/13/2015
 * Time: 12:16 AM
 */
class FormBuilder
{
    const FORMBUILDER_ENCRYPTION_ALGORITHM = MCRYPT_TWOFISH;

    const ELEMENT_HIDDEN = 'hidden'; //input
    const ELEMENT_TEXT = 'text'; //input
    const ELEMENT_TEXTAREA = 'textarea';
    const ELEMENT_SELECT = 'select';
    const ELEMENT_OPTION = 'option';
    const ELEMENT_PASSWORD = 'password'; //input
    const ELEMENT_EMAIL = 'email'; //input
    const ELEMENT_FILE = 'file'; //input
    const ELEMENT_SUBMIT = 'submit'; //input
    const ELEMENT_FIELDSET = 'fieldset';
    const ELEMENT_BUTTON = 'button'; //input
    const ELEMENT_COLOR = 'color'; //input
    const ELEMENT_DATE = 'date'; //input
    const ELEMENT_DATETIME = 'datetime'; //input
    const ELEMENT_DATETIME_LOCAL = 'local'; //input
    const ELEMENT_IMAGE = 'image'; //input
    const ELEMENT_NUMBER = 'number'; //input
    const ELEMENT_RADIO = 'radio'; //input
    const ELEMENT_SEARCH = 'search'; //input
    const ELEMENT_TEL = 'tel'; //input
    const ELEMENT_TIME = 'time'; //input
    const ELEMENT_WEEK = 'week'; //input
    const ELEMENT_MONTH = 'month'; //input
    const ELEMENT_CHECKBOX = 'checkbox'; //input
    const ELEMENT_LEGEND = 'legend';
    const ELEMENT_RANGE = 'range'; //input
    const ELEMENT_DIV = 'div';
    const ELEMENT_SPAN = 'span';
    const ELEMENT_ANCHOR = 'anchor';
    const ELEMENT_H1 = 'h1';
    const ELEMENT_H2 = 'h2';
    const ELEMENT_H3 = 'h3';
    const ELEMENT_HR = 'hr';
    const ELEMENT_TEXTNODE = 'textnode';


    /**
     * @var array
     */
    private $constants = array(
        self::ELEMENT_HIDDEN   => array('tagname' => 'input', 'attributes' => array('type' => 'hidden')),
        self::ELEMENT_TEXT     => array('tagname' => 'input', 'attributes' => array('type' => 'text')),
        self::ELEMENT_TEXTAREA => array('tagname' => 'input', 'attributes' => array()),
        self::ELEMENT_SELECT => array('tagname' => 'select', 'attributes' => array('autocomplete' => 'off')),
        self::ELEMENT_OPTION => array('tagname' => 'option', 'attributes' => array()),
        self::ELEMENT_PASSWORD => array('tagname' => 'input','attributes' => array('type' => 'password')),
        self::ELEMENT_EMAIL => array('tagname' => 'input','attributes' => array('type' => 'email')),
        self::ELEMENT_FILE => array('tagname' => 'input','attributes' => array('type' => 'file')),
        self::ELEMENT_SUBMIT => array('tagname' => 'input','attributes' => array('type' => 'submit')),
        self::ELEMENT_FIELDSET => array('tagname' => 'fieldset','attributes' => array()),
        self::ELEMENT_BUTTON => array('tagname' => 'input','attributes' => array('type' => 'button')),
        self::ELEMENT_COLOR => array('tagname' => 'input','attributes' => array('type' => 'color')),
        self::ELEMENT_DATE => array('tagname' => 'input','attributes' => array('type' => 'date')),
        self::ELEMENT_DATETIME => array('tagname' => 'input','attributes' => array('type' => 'datetime')),
        self::ELEMENT_DATETIME_LOCAL => array('tagname' => 'input','attributes' => array('type' => 'datetime-local')),
        self::ELEMENT_IMAGE => array('tagname' => 'input', 'attributes' => array('type' => 'image')),
        self::ELEMENT_NUMBER => array('tagname' => 'input', 'attributes' => array('type' => 'number')),
        self::ELEMENT_RADIO => array('tagname' => 'input', 'attributes' => array('type' => 'radio')),
        self::ELEMENT_SEARCH => array('tagname' => 'input', 'attributes' => array('type' => 'search')),
        self::ELEMENT_TEL => array('tagname' => 'input', 'attributes' => array('type' => 'tel')),
        self::ELEMENT_TIME => array('tagname' => 'input', 'attributes' => array('type' => 'time')),
        self::ELEMENT_WEEK => array('tagname' => 'input', 'attributes' => array('type' => 'week')),
        self::ELEMENT_MONTH => array('tagname' => 'input', 'attributes' => array('type' => 'month')),
        self::ELEMENT_CHECKBOX => array('tagname' => 'input', 'attributes' => array('type' => 'checkbox')),
        self::ELEMENT_LEGEND => array('tagname' => 'legend', 'attributes' => array()),
        self::ELEMENT_RANGE => array('tagname' => 'input', 'attributes' => array('type' => 'range')),
        self::ELEMENT_DIV => array('tagname' => 'div', 'attributes' => array()),
        self::ELEMENT_SPAN => array('tagname' => 'span', 'attributes' => array()),
        self::ELEMENT_H1 => array('tagname' => 'h1', 'attributes' => array()),
        self::ELEMENT_H2 => array('tagname' => 'h2', 'attributes' => array()),
        self::ELEMENT_H3 => array('tagname' => 'h3', 'attributes' => array()),
        self::ELEMENT_HR => array('tagname' => 'hr', 'attributes' => array()),
        self::ELEMENT_TEXTNODE => array('tagname' => '', 'attributes' => array()),
        self::ELEMENT_ANCHOR => array('tagname' => 'a', 'attributes' => array())
    );


    /**
     * see setAttributes method for usage example
     * @var array
     */
    protected $bannedAttributes = array(
        'type'    => 'type is set by FormBuilder',
        'style'   => 'Front-end is not the job of a builder',
        'onclick' => 'Learn JS'//add other on* attributes
    );

    /**
     * @var Form
     */
    protected $form;//the actual form we're building
    protected $id;
    protected $isRendered = false;

    protected $processedPostForms = array();
    protected $processedGetForms = array();
    protected $processedRequestForms = array();

    /**
     * @param $id
     * @param array $options
     */
    public function __construct($id, array $options = null)
    {//allow for user to pass specifics, like form attributes
        $this->id = strip_tags($id);
        $domImpl = new \DOMImplementation();
        $doc = $domImpl->createDocument(null, 'html',
            $domImpl->createDocumentType('html',
                '-//W3C//DTD XHTML 1.0 Transitional//EN',
                '/dc/common/xhtml11.dtd'));
        $doc->formatOutput = true;
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;
        $doc->validateOnParse = true;
        $formNode = $doc->createElement('form');
        $formNode->setAttribute('id',$this->id);
        $formNode->setIdAttribute('id',true);
        if ($options && isset($options))
        {
            $formNode = $this->setAttributes(
                $formNode,
                $options
            );
        }
        $html = $doc->getElementsByTagName('html')->item(0);
        $formNode = $html->appendChild($formNode);
        $this->form = new Form($formNode);//create Form wrapper...
    }

    /**
     * @param $newID
     * @param array|null $options
     */
    public function reset($newID, array $options = null) {
        $this->form = null;
        $this->id = null;
        $this->isRendered = false;

        $this->processedPostForms = [];
        $this->processedGetForms = [];
        $this->processedRequestForms = [];
        $this->id = strip_tags($newID);
        $doc = new \DOMDocument('1.0', 'UTF-8');
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;
        $doc->validateOnParse = true;
        $doc->validateOnParse = true;
        $formNode = $doc->createElement('form');
        $formNode->setAttribute('id',$this->id);
        $formNode->setIdAttribute('id',true);
        if ($options && isset($options))
        {
            $formNode = $this->setAttributes(
                $formNode,
                $options
            );
        }
        $this->form = new Form($formNode);//create Form wrapper...
    }

    /**
     * @param $type
     * @return array
     */
    protected function _getDefaultAttrs($type) {
        return($this->constants[$type]['attributes'])?:array();
    }

    /**
     * @param $type
     * @return string
     */
    protected function _getTagnameForType($type) {
        return($this->constants[$type]['tagname'])?:'';
    }

    /**
     * @param $type
     * @param array $attributes
     * @param null $parentElementID
     * @param null $labelText
     * @param array $selectOptions
     * @return \DOMElement|FormElement
     * @throws \InvalidArgumentException
     */
    public function addField($type, array $attributes = array(), $parentElementID = null, $labelText = null, array $selectOptions = array())
    {
        $locAttributes = $attributes;
        if (!array_key_exists($type,$this->constants))
        {//optionally allow for direct \DOMElement injection here, but I wouldn't
            throw new \InvalidArgumentException(
                sprintf(
                    '%s is not a valid type (use %s::ELEMENT_* constants)',
                    $type,
                    __CLASS__
                )
            );
        }

        $constantsArr = $this->constants[$type];
        $tagname = $constantsArr['tagname'];
        $defaultAttributes = $constantsArr['attributes'];

        $formEl = $this->form->getOwnerDocument();
        $test = $formEl->createElement('div');
        $doc = $formEl;

        foreach($locAttributes as $attrName => $attrVal) {
            if (array_key_exists($attrName,$this->bannedAttributes))
            {
                throw new \InvalidArgumentException(
                    sprintf(
                        '%s attribute not allowed: %s',
                        $attrName,
                        $this->bannedAttributes[$attrName]
                    )
                );
            }
        }

        //Set default attributes, overwriting potential user-set value for default(/constant)-attributes
        foreach ($defaultAttributes as $name => $val) {
            $locAttributes[$name] = $val;
        }
        //Set id from name if latter is set while former is not
        if(!array_key_exists('id',$locAttributes) && array_key_exists('name',$locAttributes)) {
            $locAttributes['id'] = $this->id . '_' . $locAttributes['name'];
        }

        if($type !== self::ELEMENT_TEXTNODE) {
            $el = $doc->createElement($tagname);
            $el = $this->setAttributes(
                $el,
                $locAttributes
            );
            if($type === self::ELEMENT_SELECT) {
                foreach($selectOptions as $value => $textContent) {
                    $opt = $doc->createElement('option');
                    $textEl = $doc->createTextNode($textContent);
                    $opt->appendChild($textEl);
                    $opt->setAttribute('value',strip_tags($value));
                    $el->appendChild($opt);
                }
            }
            $el = new FormElement($el,$labelText);//set in wrapper
            $this->form->addElement($el, $parentElementID, $labelText);
        } elseif($type === self::ELEMENT_TEXTNODE) {
            $el = $doc->createTextNode($labelText);
            $this->form->setElementTextNodeByID($el,$parentElementID);
        }
        return $el;
    }

    /**
     * Set attributes for el
     * @param \DOMElement $el
     * @param array $attributes
     * @return \DOMElement
     */
    protected function setAttributes(\DOMElement $el, array $attributes)
    {

        foreach ($attributes as $name => $val)
        {
            //strip_tags to make sure
            $attr = new \DOMAttr($name, strip_tags($val));
            $el->setAttributeNode($attr);
            if($name === 'id') {
                $el->setIdAttribute('id',false);
            }
        }
        return $el;
    }

    /**
     * @param $id
     * @return bool
     */
    public function removeFieldByID($id) {
        return $this->form->removeElementByID($id);
    }

    /**
     * @param $fieldName
     * @param $rule
     */
    public function setFieldRule($fieldName, $rule ) {
        $this->form->setFieldRule($fieldName,$rule);
    }

    /**
     * @param array $rulesArr
     */
    public function setFieldRulesWithRuleString(array $rulesArr ) {
        $this->form->setFieldRules($rulesArr);
    }

    /**
     * @return array
     */
    public function getFieldRules() {
        return $this->form->getFieldRules();
    }

    /**
     * @param array $arr
     * @param $key
     * @return array
     */
    public function encodeArrayForHTTP(array $arr , $key ) {
        $encryption = new Encryption(self::FORMBUILDER_ENCRYPTION_ALGORITHM, MCRYPT_MODE_CBC);
        $arrStr = serialize($arr);
        $arrLen = strlen($arrStr);
        //$BF = new ctBlowfish();
        //$encoded = $BF->ctEncrypt($arrStr,$arrLen,$key);
        $encoded = urlencode(base64_encode($encryption->encrypt($arrStr,$key)));
        return array('encodedData' => $encoded,'length' => $arrLen);
    }

    /**
     * @param $str
     * @param $len
     * @param $key
     * @return mixed
     */
    public function decodeArrayString($str, $len, $key ) {
        //$BF = new ctBlowfish();
        //$decoded = $BF->ctDecrypt($str,(int)$len,$key);
        $encryption = new Encryption(self::FORMBUILDER_ENCRYPTION_ALGORITHM, MCRYPT_MODE_CBC);
        $decoded = $encryption->decrypt(base64_decode(urldecode($str)),$key);
        $arr = unserialize($decoded);
        return $arr;
    }

    /**
     * @param null $metaDataEncryptionKey
     * @return $this
     */
    public function render($metaDataEncryptionKey = null) {
        $this->form->removeMetaDataFields();
        if($metaDataEncryptionKey) {
            $rulesArray = $this->form->getFieldRules();
            $formMetaDataArray = array(
                'fields' => $this->form->getFieldNames(),
                'rules' => $rulesArray,
                'serializedFormObj' => serialize(clone $this->form)
            );
            $metaDataEncodingArr = $this->encodeArrayForHTTP($formMetaDataArray, $metaDataEncryptionKey);
            $metaDataEncoded = $metaDataEncodingArr['encodedData'];
            $metaDataLength = $metaDataEncodingArr['length'];
            $fieldNameData = 'fmd_' . $this->id;
            $fieldNameLen = 'fmdl_' . $this->id;
            $this->addField(self::ELEMENT_HIDDEN,array('id' => $fieldNameData, 'name' => $fieldNameData, 'value' => $metaDataEncoded));
            $this->addField(self::ELEMENT_HIDDEN,array('id' => $fieldNameLen, 'name' => $fieldNameLen, 'value' => $metaDataLength));
        }
        if(!$this->form->isFormRendered()) {
            $this->form->render();
            $this->isRendered = true;
        }
        return $this;
    }

    public function getRenderedString() {
        if (!$this->isRendered) {
            throw new \BadMethodCallException("'getRenderedString' can only be called after 'render' has been called");
        }
        return $this->form->getRenderedContent();
    }


    /**
     * @param $metaDataEncryptionKey
     * @param string $postGetRequest
     * @return array
     */
    public function getFormsFromRequest($metaDataEncryptionKey, $postGetRequest = 'request') {

        if(!(mb_strlen($metaDataEncryptionKey,'UTF-8') > 0)) {
            throw new \InvalidArgumentException(
                'Parameter metaDataEncryptionKey must not be empty.'
            );
        }

        $sourceArrRef = &$_REQUEST;
        $targetArrRef = &$this->processedRequestForms;
        if(strcasecmp('post',$postGetRequest) === 0) {
            $sourceArrRef = &$_POST;
            $targetArrRef = &$this->processedPostForms;
        } elseif (strcasecmp('get',$postGetRequest) === 0) {
            $sourceArrRef = &$_GET;
            $targetArrRef = &$this->processedGetForms;
        }

        $forms = array();
        $formData = array();
        $arrKeys = array_keys($sourceArrRef);

        foreach($arrKeys as $key) {
            $fid = '';
            $fmd = '';
            $fmdl = 0;
            if(Validator::strStartsWith($key,'fmd_')) {
                $fid = explode('_',$key,2)[1];
                $fmd = $sourceArrRef[$key];
                $currSourceArrIndex = 'fmdl_' . $fid;
                $fmdl = (isset($sourceArrRef[$currSourceArrIndex]) ? (int)$sourceArrRef[$currSourceArrIndex]:0);
                if($fid !== '' && $fmd !== '' && $fmdl !== 0) {
                    $formData[] = array('id' => $fid, 'encryptedData' => $fmd, 'length' => $fmdl);
                }
            }
        }
        foreach($formData as $formDataArr) {
            $arr = $this->decodeArrayString($formDataArr['encryptedData'],$formDataArr['length'],$metaDataEncryptionKey);
            if(isset($arr['serializedFormObj'])) {
                try{
                    $locForm = unserialize($arr['serializedFormObj']);
                    $locForm->validateRequestAgainstRules();
                    $forms[] = $locForm;
                }catch(\Exception $e) {
                    //Do nothing
                }
            }
        }

        $targetArrRef = $forms;
        return $forms;
    }

    /**
     * @param string|null $id
     * @param string|null $class
     * @param string|null $parentId
     * @param string|null $text
     * @param array $additionalOptions
     * @return \DOMElement|FormElement
     */
    public function addDIV($id = null, $class = null, $parentId = null, $text = null, array $additionalOptions = array()) {
        if(null !== $id) {
            $additionalOptions['id'] = (string)$id;
        }
        if(null !== $class) {
            $additionalOptions['class'] = (string)$class;
        }
        $el = $this->addField(self::ELEMENT_DIV,$additionalOptions,$parentId);
        $domEl = $el->getEl();
        $text = strip_tags($text);
        if(mb_strlen($text) > 0) {
            $textNode = $domEl->ownerDocument->createTextNode($text);
            $domEl->appendChild($textNode);
        }
        return $el;
    }

    /**
     * @param null $id
     * @param null $class
     * @param string $href
     * @param null $parentId
     * @param null $text
     * @param array $additionalOptions
     * @return \DOMElement|FormElement
     */
    public function addAnchor($id = null, $class = null, $href = '', $parentId = null, $text = null, array $additionalOptions = array()) {
        if(null !== $id) {
            $additionalOptions['id'] = (string)$id;
        }
        if(null !== $class) {
            $additionalOptions['class'] = (string)$class;
        }
        $additionalOptions['href'] = filter_var($href,FILTER_SANITIZE_URL);
        $el = $this->addField(self::ELEMENT_ANCHOR,$additionalOptions,$parentId);
        $domEl = $el->getEl();
        $text = strip_tags($text);
        if(mb_strlen($text) > 0) {
            $textNode = $domEl->ownerDocument->createTextNode($text);
            $domEl->appendChild($textNode);
        }
        return $el;
    }

    /**
     * @param null $id
     * @param null $class
     * @param null $parentId
     * @param string $text
     * @param array $additionalOptions
     * @return \DOMElement|FormElement
     * @throws \InvalidArgumentException
     */
    public function addSpan($id = null, $class = null, $parentId = null, $text = null, array $additionalOptions = array()) {
        if(null !== $id) {
            $additionalOptions['id'] = (string)$id;
        }
        if(null !== $class) {
            $additionalOptions['class'] = (string)$class;
        }
        $el = $this->addField(self::ELEMENT_SPAN,$additionalOptions,$parentId);
        $domEl = $el->getEl();
        $text = strip_tags($text);
        if(mb_strlen($text) > 0) {
            $textNode = $domEl->ownerDocument->createTextNode($text);
            $domEl->appendChild($textNode);
        }
        return $el;
    }

    /**
     * @param $name
     * @param null $id
     * @param null $class
     * @param null $parentId
     * @param null $value
     * @param null $labelText
     * @param array $additionalOptions
     * @return \DOMElement|FormElement
     */
    public function addTextInput($name, $id = null, $class = null, $parentId = null, $value = null, $labelText = null, array $additionalOptions = array()) {

        if(mb_strlen($name) > 0) {
            $additionalOptions['name'] = $name;
        }
        if(null !== $id) {
            $additionalOptions['id'] = (string)$id;
        }
        if(null !== $class) {
            $additionalOptions['class'] = (string)$class;
        }
        if(null !== $value) {
            $additionalOptions['value'] = (string)$value;
        }
        $text = htmlentities(strip_tags($value), ENT_QUOTES, 'UTF-8');
        if(mb_strlen($text) > 0) {
            $additionalOptions['value'] = $text;
        }
        $el = $this->addField(self::ELEMENT_TEXT,$additionalOptions,$parentId,$labelText);
        return $el;
    }

    /**
     * @param $name
     * @param null $id
     * @param null $class
     * @param null $parentId
     * @param null $value
     * @param null $labelText
     * @param array $additionalOptions
     * @return \DOMElement|FormElement
     */
    public function addTextArea($name, $id = null, $class = null, $parentId = null, $value = null, $labelText = null, array $additionalOptions = array()) {
        if(mb_strlen($name) > 0) {
            $additionalOptions['name'] = $name;
        }
        if(null !== $id) {
            $additionalOptions['id'] = (string)$id;
        }
        if(null !== $class) {
            $additionalOptions['class'] = (string)$class;
        }
        $text = htmlentities(strip_tags($labelText), ENT_QUOTES, 'UTF-8');
        if(mb_strlen($text) > 0) {
            $additionalOptions['value'] = $text;
        }
        $el = $this->addField(self::ELEMENT_TEXTAREA,$additionalOptions,$parentId,$labelText);
        return $el;
    }

    /**
     * @param $name
     * @param null $value
     * @param array $additionalOptions
     * @return \DOMElement|FormElement
     */
    public function addHidden($name, $value = null, array $additionalOptions = array()) {
        if(mb_strlen($name) > 0) {
            $additionalOptions['name'] = $name;
        }

        $text = strip_tags($value);
        if(mb_strlen($text) > 0) {
            $additionalOptions['value'] = $text;
        }
        $el = $this->addField(self::ELEMENT_HIDDEN,$additionalOptions);
        return $el;
    }

    /**
     * @param $name
     * @param null $id
     * @param null $class
     * @param null $parentId
     * @param null $value
     * @param null $labelText
     * @param array $additionalOptions
     * @return \DOMElement|FormElement
     */
    public function addNumberInput($name, $id = null, $class = null, $parentId = null, $value = null, $labelText = null, array $additionalOptions = array()) {
        if(mb_strlen($name) > 0) {
            $additionalOptions['name'] = $name;
        }
        if(null !== $id) {
            $additionalOptions['id'] = (string)$id;
        }
        if(null !== $class) {
            $additionalOptions['class'] = (string)$class;
        }

        $text = htmlentities(strip_tags($value), ENT_QUOTES, 'UTF-8');
        if(mb_strlen($text) > 0) {
            $additionalOptions['value'] = $value;
        }
        $el = $this->addField(self::ELEMENT_NUMBER,$additionalOptions,$parentId,$labelText);
        return $el;
    }

    /**
     * @param $name
     * @param null $id
     * @param null $class
     * @param null $parentId
     * @param null $value
     * @param null $labelText
     * @param array $additionalOptions
     * @return \DOMElement|FormElement
     */
    public function addDateInput($name, $id = null, $class = null, $parentId = null, $value = null, $labelText = null, array $additionalOptions = array()) {
        if(mb_strlen($name) > 0) {
            $additionalOptions['name'] = $name;
        }
        if(null !== $id) {
            $additionalOptions['id'] = (string)$id;
        }
        if(null !== $class) {
            $additionalOptions['class'] = (string)$class;
        }
        $text = htmlentities(strip_tags($labelText), ENT_QUOTES, 'UTF-8');
        if(mb_strlen($text) > 0) {
            $additionalOptions['value'] = $text;
        }
        $el = $this->addField(self::ELEMENT_DATE,$additionalOptions,$parentId,$labelText);
        return $el;
    }

    /**
     * @param $name
     * @param null $id
     * @param null $class
     * @param null $parentId
     * @param null $preselectVal
     * @param null $labelText
     * @param array $selectOptions
     * @param array $additionalOptions
     * @return \DOMElement|FormElement
     */
    public function addSelect($name, $id = null, $class = null, $parentId = null, $preselectVal = null, $labelText = null, array $selectOptions = array(), array $additionalOptions = array()) {
        if(mb_strlen($name) > 0) {
            $additionalOptions['name'] = $name;
        }
        if(null !== $id) {
            $additionalOptions['id'] = (string)$id;
        }
        if(null !== $class) {
            $additionalOptions['class'] = (string)$class;
        }
        $el = $this->addField(self::ELEMENT_SELECT,$additionalOptions,$parentId,$labelText,$selectOptions);
        if(null !== $preselectVal) {
            $this->form->setSelectedOptionByValue($el->getEl(), $preselectVal);
        }
        return $el;
    }

    /**
     * @param $name
     * @param null $id
     * @param null $class
     * @param null $parentId
     * @param null $value
     * @param bool $isChecked
     * @param null $labelText
     * @param array $additionalOptions
     * @return \DOMElement|FormElement
     */
    public function addRadioInput($name, $id = null, $class = null, $parentId = null, $value = null, $isChecked = false, $labelText = null, array $additionalOptions = array()) {
        if(mb_strlen($name) > 0) {
            $additionalOptions['name'] = $name;
        }
        if(null !== $id) {
            $additionalOptions['id'] = (string)$id;
        }
        if(null !== $class) {
            $additionalOptions['class'] = (string)$class;
        }
        $text = htmlentities(strip_tags($labelText), ENT_QUOTES, 'UTF-8');
        if(mb_strlen($text) > 0) {
            $additionalOptions['value'] = $text;
        }
        if($isChecked) {
            $additionalOptions['checked'] = 'checked';
        }
        $el = $this->addField(self::ELEMENT_RADIO,$additionalOptions,$parentId,$labelText);
        return $el;
    }

    /**
     * @param $name
     * @param null $id
     * @param null $class
     * @param null $parentId
     * @param null $value
     * @param bool $isChecked
     * @param array|null $labelText
     * @param array $additionalOptions
     * @return \DOMElement|FormElement
     */
    public function addCheckboxInput($name, $id = null, $class = null, $parentId = null, $value = null, $isChecked = false, array $labelText = null, array $additionalOptions = array()) {
        if(mb_strlen($name) > 0) {
            $additionalOptions['name'] = $name;
        }
        if(null !== $id) {
            $additionalOptions['id'] = (string)$id;
        }
        if(null !== $class) {
            $additionalOptions['class'] = (string)$class;
        }
        $text = htmlentities(strip_tags($labelText), ENT_QUOTES, 'UTF-8');
        if(mb_strlen($text) > 0) {
            $additionalOptions['value'] = $text;
        }
        if($isChecked) {
            $additionalOptions['checked'] = 'checked';
        }
        $el = $this->addField(self::ELEMENT_CHECKBOX,$additionalOptions,$parentId,$labelText);
        return $el;
    }

    /**
     * @param $name
     * @param null $id
     * @param null $class
     * @param null $parentId
     * @param null $value
     * @param null $labelText
     * @param array $additionalOptions
     * @return \DOMElement|FormElement
     */
    public function addEmailInput($name, $id = null, $class = null, $parentId = null, $value = null, $labelText = null, array $additionalOptions = array()) {
        if(mb_strlen($name) > 0) {
            $additionalOptions['name'] = $name;
        }
        if(null !== $id) {
            $additionalOptions['id'] = (string)$id;
        }
        if(null !== $class) {
            $additionalOptions['class'] = (string)$class;
        }
        $text = htmlentities(strip_tags($labelText), ENT_QUOTES, 'UTF-8');
        if(mb_strlen($text) > 0) {
            $additionalOptions['value'] = $text;
        }
        $el = $this->addField(self::ELEMENT_EMAIL,$additionalOptions,$parentId,$labelText);
        return $el;
    }

    /**
     * @param $name
     * @param null $id
     * @param null $class
     * @param null $parentId
     * @param null $value
     * @param null $labelText
     * @param array $additionalOptions
     * @return \DOMElement|FormElement
     */
    public function addTelInput($name, $id = null, $class = null, $parentId = null, $value = null, $labelText = null, array $additionalOptions = array()) {
        if(mb_strlen($name) > 0) {
            $additionalOptions['name'] = $name;
        }
        if(null !== $id) {
            $additionalOptions['id'] = (string)$id;
        }
        if(null !== $class) {
            $additionalOptions['class'] = (string)$class;
        }
        $text = htmlentities(strip_tags($labelText), ENT_QUOTES, 'UTF-8');
        if(mb_strlen($text) > 0) {
            $additionalOptions['value'] = $text;
        }
        $el = $this->addField(self::ELEMENT_TEL,$additionalOptions,$parentId,$labelText);
        return $el;
    }

    public function validatePrefillAndAddErrors() {
        $this->form->validatePrefillAndAddErrors();
    }

    /**
     * @return Form
     */
    public function getForm() {
        return $this->form;
    }

    /**
     * @return \DOMDocument
     */
    public function getDOM() {
        return $this->form->getDOMDocument();
    }

    /**
     * @return \DOMElement
     */
    public function getMainDOMNode() {
        return $this->form->getEl();
    }

    public function prefillFromSuperglobal() {
        $this->form->doPrefillFromSuperglobal();
    }

    /**
     * @param array $arr
     */
    public function prefillFromArray(array &$arr) {
        $this->form->doPrefillFromArray($arr);
    }

    /**
     * @param ViewModel $viewModel
     * @param array $fieldInputMappings
     */
    public function registerViewModel(ViewModel $viewModel, array $fieldInputMappings) {
        $this->form->registerViewModel($viewModel,$fieldInputMappings);
    }

    /**
     * @param ViewModel $viewModel
     */
    public function prefillFromViewModel(ViewModel $viewModel) {
        $this->form->doPrefillFromViewModel($viewModel);
    }

    /**
     * @param $key
     * @param $value
     */
    public function addData($key, $value) {
        $fieldNames = $this->form->getFieldNames();
        if(in_array($key,$fieldNames,false)) {
            throw new \InvalidArgumentException(
                "Cannot set data. There is already an input with the name " . strip_tags($key) . "."
            );
        }
        $this->addHidden($key, $value);
    }

    /**
     * @param array $arr
     */
    public function addDataArray(array $arr) {
        foreach($arr as $key => $value) {
            $this->addData($key,$value);
        }
    }

    /**
     * @param $id
     * @param $classname
     * @param $text
     * @param $parentId
     * @return \DOMElement|FormElement
     */
    public function addSubmitButton($id, $classname, $text, $parentId)
    {
        $el = $this->addField(self::ELEMENT_SUBMIT,['value' => $text],$parentId,null);
        return $el;
    }

}