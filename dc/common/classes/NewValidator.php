<?php
namespace DynCom\dc\common\classes;
use ReflectionClass;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/12/2015
 * Time: 3:42 AM
 */
class NewValidator
{

    public const ORDINAL_COMPARATOR_EQ = '=';
    public const ORDINAL_COMPARATOR_LT = '<';
    public const ORDINAL_COMPARATOR_LTE = '<=';
    public const ORDINAL_COMPARATOR_GT = '>';
    public const ORDINAL_COMPARATOR_GTE = '>=';

    public const DECIMAL_SEPARATOR_COMMA = ',';
    public const DECIMAL_SEPARATOR_POINT = '.';

    public const DATE_FORMAT_ISO_8601 = 'yyyy-mm-dd';
    public const DATE_FORMAT_DDMMYY_DASHES = 'dd-mm-yy';
    public const DATE_FORMAT_DDMMYYYY_DASHES = 'dd-mm-yyyy';
    public const DATE_FORMAT_DDMMYY_DOTS = 'dd.mm.yy';
    public const DATE_FORMAT_DDMMYYYY_DOTS = 'dd.mm.yyyy';
    public const DATE_FORMAT_DDMMYY_SLASHES = 'dd/mm/yy';
    public const DATE_FORMAT_DDMMYYYY_SLASHES = 'dd/mm/yyyy';
    public const DATE_FORMAT_MMDDYY_DASHES = 'mm-dd-yy';
    public const DATE_FORMAT_MMDDYYYY_DASHES = 'mm-dd-yyyy';
    public const DATE_FORMAT_MMDDYY_DOTS = 'mm.dd.yy';
    public const DATE_FORMAT_MMDDYYYY_DOTS = 'mm.dd.yyyy';
    public const DATE_FORMAT_MMDDYY_SLASHES = 'mm/dd/yy';
    public const DATE_FORMAT_MMDDYYYY_SLASHES = 'mm/dd/yyyy';

    public const DATETIME_FORMAT_ISO_8601 = 'yyyy-mm-ddThh:mm:ss';
    public const DATETIME_FORMAT_MYSQL = 'yyyy-mm-dd hh:mm:ss';

    public const TIME_FORMAT_COLON = 'hh:mm:ss';

    public const CONF_RULE_SEPARATOR = '|';
    public const CONF_RULE_VALUE_SEPARATOR = ':';

    protected const ERROR_MSG_CONST_SUFFIX = '_ERROR_MSG_CODE';

    public      const RULE_REQUIRED = 'required';
    protected   const RULE_REQUIRED_ERROR_MSG_CODE = 'validation_error_required_not_set';

    public    const RULE_EMAIL = 'email';
    protected const RULE_EMAIL_ERROR_MSG_CODE = 'validation_error_email_invalid';

    public    const RULE_NAME = 'name';
    protected const RULE_NAME_ERROR_MSG_CODE = 'validation_error_name_invalid';

    public    const RULE_ADDR_STREET = 'addrstreet';
    protected const RULE_ADDR_STREET_ERROR_MSG_CODE = 'validation_error_addr_street_invalid';

    public    const RULE_ADDR_STREET_NO = 'addrstreetno';
    protected const RULE_ADDR_STREET_NO_ERROR_MSG_CODE = 'validation_error_addr_street_no_invalid';

    public    const RULE_ADDR_STREET_PLUS_NO = 'addrstreetplusno';
    protected const RULE_ADDR_STREET_PLUS_NO_ERROR_MSG_CODE = 'validation_error_addr_street_plus_no_invalid';

    public    const RULE_ADDR_CITY = 'addrcity';
    protected const RULE_ADDR_CITY_ERROR_MSG_CODE = 'validation_error_addr_city_invalid';

    public    const RULE_ADDR_ZIP = 'addrzip';
    protected const RULE_ADDR_ZIP_ERROR_MSG_CODE = 'validation_error_addr_zip_invalid';

    public    const RULE_STR_WS_DASH_DOT = 'strwsdashdot';
    protected const RULE_STR_WS_DASH_DOT_ERROR_MSG_CODE = 'validation_error_str_ws_dash_dot_invalid';

    public    const RULE_STR_WS_DASH_DOT_PAR = 'strwsdashdotparen';
    protected const RULE_STR_WS_DASH_DOT_PAR_ERROR_MSG_CODE = 'validation_error_str_ws_dash_dot_par_invalid';

    public    const RULE_URL = 'url';
    protected const RULE_URL_ERROR_MSG_CODE = 'validation_error_url_invalid';

    public    const RULE_ALPHA = 'Alpha';
    protected const RULE_ALPHA_ERROR_MSG_CODE = 'validation_error_alpha_invalid';

    public    const RULE_GRAPH = 'graph';
    protected const RULE_GRAPH_ERROR_MSG_CODE = 'validation_error_graph_invalid';

    public    const RULE_ALPHA_CAPS = 'ALPHA';
    protected const RULE_ALPHA_CAPS_ERROR_MSG_CODE = 'validation_error_alpha_caps_invalid';

    public    const RULE_ALPHA_SMALL = 'alpha';
    protected const RULE_ALPHA_SMALL_ERROR_MSG_CODE = 'validation_error_alpha_small_invalid';

    public    const RULE_DIGIT = 'digit';
    protected const RULE_DIGIT_ERROR_MSG_CODE = 'validation_error_alpha_digit_invalid';

    public    const RULE_DECIMAL = 'decimal';
    protected const RULE_DECIMAL_ERROR_MSG_CODE = 'validation_error_decimal_invalid';

    public    const RULE_ALNUM = 'alnum';
    protected const RULE_ALNUM_ERROR_MSG_CODE = 'validation_error_alnum_invalid';

    public    const RULE_GENERAL_TEXT = 'text';
    protected const RULE_GENERAL_TEXT_ERROR_MSG_CODE = 'validation_error_text_general_invalid';

    public    const RULE_DATE = 'date';
    protected const RULE_DATE_ERROR_MSG_CODE  = 'validation_error_date_invalid';

    public    const RULE_DATETIME = 'dateTime';
    protected const RULE_DATETIME_ERROR_MSG_CODE = 'validation_error_datetime_invalid';

    public    const RULE_MIN_LENGTH = 'minlen';
    protected const RULE_MIN_LENGTH_ERROR_MSG_CODE = 'validation_error_minlen_exceeded';

    public    const RULE_MAX_LENGTH = 'maxlen';
    protected const RULE_MAX_LENGTH_ERROR_MSG_CODE = 'validation_error_maxlen_exceeded';

    public    const RULE_MIN_NUMERIC = 'minnum';
    protected const RULE_MIN_NUM_ERROR_MSG_CODE = 'validation_error_minnum_exceeded';

    public    const RULE_MAX_NUMERIC = 'maxnum';
    protected const RULE_MAX_NUM_ERROR_MSG_CODE = 'validation_error_maxnum_exceeded';

    public    const RULE_MIN_DATE = 'mindate';
    protected const RULE_MIN_DATE_ERROR_MSG_CODE = 'validation_error_mindate_exceeded';

    public    const RULE_MAX_DATE = 'maxdate';
    protected const RULE_MAX_DATE_ERROR_MSG_CODE = 'validation_error_max_date_exceeded';

    public    const RULE_CUST_REGEX = 'regex';
    protected const RULE_CUST_REGEX_ERROR_MSG_CODE = 'validation_error_cust_regex_unmatched';

    public    const RULE_REQUIRES = 'requires';
    protected const RULE_REQUIRES_ERROR_MSG_CODE = 'validation_error_required_field_not_set';

    public    const RULE_EXTENDS = 'extends';
    public const RULE_IN = 'in';

    public  const RULE_INSTANCEOF = 'instanceof';
    public  const RULE_FIELD = 'field';
    public  const RULE_METHOD = 'method';

    public const RULE_EMPTY_ALLOWED = 'orempty';

    protected $decimalSeparator = self::DECIMAL_SEPARATOR_POINT;
    protected $dateFormat = self::DATE_FORMAT_DDMMYY_DOTS;
    protected $timeFormat = self::TIME_FORMAT_COLON;
    protected $dateTimeFormat = self::DATETIME_FORMAT_MYSQL;
    protected $dateValidation = array();

    protected $ruleConstants = array();

    protected $data = array();
    protected $rules = array();

    protected $languageCode = 'de';

    /**
     * @param $regex
     * @return bool
     */
    protected function _isValidRegex($regex)
    {
        return (preg_match($regex, null) !== false);
    }

    /**
     * @param $fieldName
     * @param $regex
     */
    public function setCustomRegexRule($fieldName, $regex)
    {
        if (!array_key_exists($fieldName, $this->data) && !property_exists($this->data,$fieldName)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%s is not the name of a data-item.',
                    strip_tags($fieldName)
                )
            );
        }

        if (!$this->_isValidRegex($regex)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%s is not a valid regex.',
                    strip_tags($regex)
                )
            );
        }

        $callable = function ($fieldName, $fieldValue, $validationParam) {
            return (preg_match($validationParam, $fieldValue) > 0);
        };

        $this->rules[$fieldName][self::RULE_CUST_REGEX] = array('callable' => &$callable, 'ruleValue' => $regex);
    }

    protected function _setDefaultDateFormatValidation()
    {
        $this->dateValidation = array(
            self::DATE_FORMAT_ISO_8601 => function ($dataSourceString) {
                return $this->_isValidByDateFormat('Y-m-d', $dataSourceString);
            },
            self::DATE_FORMAT_DDMMYY_DASHES => function ($dataSourceString) {
                return $this->_isValidByDateFormat('d-m-y', $dataSourceString);
            },
            self::DATE_FORMAT_DDMMYYYY_DASHES => function ($dataSourceString) {
                return $this->_isValidByDateFormat('d-m-Y', $dataSourceString);
            },
            self::DATE_FORMAT_DDMMYY_DOTS => function ($dataSourceString) {
                return $this->_isValidByDateFormat('d.m.y', $dataSourceString);
            },
            self::DATE_FORMAT_DDMMYYYY_DOTS => function ($dataSourceString) {
                return $this->_isValidByDateFormat('d.m.Y', $dataSourceString);
            },
            self::DATE_FORMAT_DDMMYY_SLASHES => function ($dataSourceString) {
                return $this->_isValidByDateFormat('d/m/y', $dataSourceString);
            },
            self::DATE_FORMAT_DDMMYYYY_SLASHES => function ($dataSourceString) {
                return $this->_isValidByDateFormat('d/m/Y', $dataSourceString);
            },
            self::DATE_FORMAT_MMDDYY_DASHES => function ($dataSourceString) {
                return $this->_isValidByDateFormat('m-d-y', $dataSourceString);
            },
            self::DATE_FORMAT_MMDDYYYY_DASHES => function ($dataSourceString) {
                return $this->_isValidByDateFormat('m-d-Y', $dataSourceString);
            },
            self::DATE_FORMAT_MMDDYY_DOTS => function ($dataSourceString) {
                return $this->_isValidByDateFormat('m.d.y', $dataSourceString);
            },
            self::DATE_FORMAT_MMDDYYYY_DOTS => function ($dataSourceString) {
                return $this->_isValidByDateFormat('m.d.Y', $dataSourceString);
            },
            self::DATE_FORMAT_MMDDYY_SLASHES => function ($dataSourceString) {
                return $this->_isValidByDateFormat('m/d/y', $dataSourceString);
            },
            self::DATE_FORMAT_MMDDYYYY_SLASHES => function ($dataSourceString) {
                return $this->_isValidByDateFormat('m/d/Y', $dataSourceString);
            },
            self::DATETIME_FORMAT_ISO_8601 => function ($dataSourceString) {
                return $this->_isValidByDateFormat('Y-m-dTH:i:s', $dataSourceString);
            },
            self::DATETIME_FORMAT_MYSQL => function ($dataSourceString) {
                return $this->_isValidByDateFormat('Y-m-d H:i:s', $dataSourceString);
            }
        );
    }

    protected $ruleValidation = array();

    protected function _setDefaultRuleValidation()
    {
        $this->ruleValidation = array(
            self::RULE_REQUIRED => function () {
                $fieldName = func_get_arg(0);
                return array_key_exists($fieldName, $this->data) || property_exists($this->data,$fieldName);
            },
            self::RULE_EMAIL => function () {
                $fieldValue = func_get_arg(1);
                return filter_var($fieldValue, FILTER_VALIDATE_EMAIL);
            },
            self::RULE_NAME => function () {
                $fieldValue = func_get_arg(1);
                return (preg_match('/^[[:alpha:]äöüßÄÖÜ\s\-]+$/u', $fieldValue) > 0);
            },
            self::RULE_ADDR_STREET => function () {
                $fieldValue = func_get_arg(1);
                return (preg_match('/^[[:alpha:]äöüßÄÖÜ\s\-\.]+$/u', $fieldValue) > 0);
            },
            self::RULE_ADDR_STREET_NO => function () {
                $fieldValue = func_get_arg(1);
                return (preg_match(
                        '/^[[:digit:]]+[[:alpha:]]{0,1}(?:\s*\-\s*[[:digit:]]{0,}[[:alpha:]]*){0,1}$/',
                        $fieldValue
                    ) > 0);
            },
            self::RULE_ADDR_STREET_PLUS_NO => function () {
                $fieldValue = func_get_arg(1);
                return (preg_match(
                        '/^[[:alpha:]äöüßÄÖÜ\s\-\.]+\s+[[:digit:]]+[[:alpha:]]{0,1}(?:\s*\-\s*[[:digit:]]{0,}[[:alpha:]]*){0,1}$/u',
                        $fieldValue
                    ) > 0);
            },
            self::RULE_ADDR_CITY => function () {
                $fieldValue = func_get_arg(1);
                return (preg_match('/^[[:alpha:]äöüßÄÖÜ\s\-\.]+$/u', $fieldValue) > 0);
            },
            self::RULE_ADDR_ZIP => function () {
                $fieldValue = func_get_arg(1);
                return (preg_match('/^[[:digit:]]{5}$/u', $fieldValue) > 0);
            },
            self::RULE_STR_WS_DASH_DOT => function () {
                $fieldValue = func_get_arg(1);
                return (preg_match('/^[[:alpha:]äöüßÄÖÜ\-\.]+$/u', $fieldValue) > 0);
            },
            self::RULE_STR_WS_DASH_DOT_PAR => function () {
                $fieldValue = func_get_arg(1);
                return (preg_match('/^[[:alpha:]äöüßÄÖÜ\-\.\(\)]+$/u', $fieldValue) > 0);
            },
            self::RULE_URL => function () {
                $fieldValue = func_get_arg(1);
                return filter_var($fieldValue, FILTER_VALIDATE_URL);
            },
            self::RULE_ALPHA => function () {
                $fieldValue = func_get_arg(1);
                return (preg_match('/^[[:alpha:]äöüßÄÖÜ]+$/u', $fieldValue) > 0);
            },
            self::RULE_ALPHA_CAPS => function () {
                $fieldValue = func_get_arg(1);
                return (preg_match('/^[[:upper:]ÄÖÜ]+$/u', $fieldValue) > 0);
            },
            self::RULE_ALPHA_SMALL => function () {
                $fieldValue = func_get_arg(1);
                return (preg_match('/^[[:lower:]äöüß]+$/u', $fieldValue) > 0);
            },
            self::RULE_DIGIT => function () {
                $fieldValue = func_get_arg(1);
                return (preg_match('/^[[:digit:]]+$/', $fieldValue) > 0);
            },
            self::RULE_DECIMAL => function () {
                $fieldValue = func_get_arg(1);
                return (preg_match(
                        '/^[[:digit:]]+(\\' . $this->decimalSeparator . '[[:digit:]]{1,})|[[:digit:]]$/',
                        $fieldValue
                    ) > 0);
            },
            self::RULE_ALNUM => function () {
                $fieldValue = func_get_arg(1);
                return (preg_match('/^[[:alnum:]äöüßÄÖÜ]+$/u', $fieldValue) > 0);
            },
            self::RULE_GENERAL_TEXT => function () {
                $fieldValue = func_get_arg(1);
                return (strip_tags($fieldValue) === $fieldValue);
            },
            self::RULE_DATE => function () {
                $fieldValue = func_get_arg(1);
                return ($this->dateValidation[$this->dateFormat]($fieldValue));
            },
            self::RULE_DATETIME => function () {
                $fieldValue = func_get_arg(1);
                return ($this->dateValidation[$this->dateTimeFormat]($fieldValue));
            },
            self::RULE_MIN_LENGTH => function () {
                $fieldValue = func_get_arg(1);
                $validationParam = func_get_arg(2);
                return (mb_strlen($fieldValue, 'UTF-8') >= $validationParam);
            },
            self::RULE_MAX_LENGTH => function () {
                $fieldValue = func_get_arg(1);
                $validationParam = func_get_arg(2);
                return (mb_strlen($fieldValue, 'UTF-8') <= $validationParam);
            },
            self::RULE_MIN_NUMERIC => function () {
                $fieldValue = func_get_arg(1);
                $validationParam = func_get_arg(2);
                return ((float)$fieldValue >= (float)$validationParam);
            },
            self::RULE_MAX_NUMERIC => function () {
                $fieldValue = func_get_arg(1);
                $validationParam = func_get_arg(2);
                return ((float)$fieldValue <= (float)$validationParam);
            },
            self::RULE_MIN_DATE => function () {
                $fieldValue = func_get_arg(1);
                $validationParam = func_get_arg(2);
                return $this->_dateStrHasOrdinalRelToRefDateStr(
                    $fieldValue,
                    $validationParam,
                    self::ORDINAL_COMPARATOR_GTE
                );
            },
            self::RULE_MAX_DATE => function () {
                $fieldValue = func_get_arg(1);
                $validationParam = func_get_arg(2);
                return $this->_dateStrHasOrdinalRelToRefDateStr(
                    $fieldValue,
                    $validationParam,
                    self::ORDINAL_COMPARATOR_LTE
                );
            },
            self::RULE_REQUIRES => function () {
                $validationParam = func_get_arg(2);
                $dataExists = array_key_exists($validationParam, $this->data) || property_exists($this->data,$validationParam);
                if (!$dataExists) { return false; }
                $fieldData = $this->data[$validationParam];
                $isValid = true;
                $dummyFieldStatusArr = [];
                //If rules exist for other field, they have to be valid;
                $this->_validateField($validationParam, $fieldData, $dummyFieldStatusArr, $isValid);
                return $isValid;
            },
            self::RULE_EXTENDS => function ($fieldName, $fieldValue, $validationParam, array &$fieldStatusArr) {
                $isValid = true;
                $extendedRulesExists = array_key_exists($validationParam, $this->rules);
                if (!$extendedRulesExists) { return $isValid; }
                //Validate the data of this field against the rules of the extended Field
                $this->_validateField($validationParam, $fieldValue, $fieldStatusArr, $isValid);
                return $isValid;
            },
            self::RULE_IN => function () {
                $fieldValue = func_get_arg(1);
                $validationParam = func_get_arg(2);
                $validValues = json_decode($validationParam, true);
                $isValid = (\is_array($validValues) && \in_array($fieldValue, $validValues, true));
                return $isValid;
            },
            self::RULE_INSTANCEOF => function () {
                $obj = func_get_arg(1);
                $objName = func_get_arg(2);
                return (is_object($obj) && ($obj instanceof $objName));
            },
            self::RULE_FIELD => function () {
                $obj = func_get_arg(1);
                $nameValueStringArr = explode('=', func_get_arg(2), 2);
                $fieldName = $nameValueStringArr[0];
                $fieldValue = (array_key_exists(1, $nameValueStringArr) && strlen($nameValueStringArr[1]) > 0) ?
                    $nameValueStringArr[1] : true;
                $objValue = false;
                if (is_object($obj)) {
                    @$objValue = $obj->$fieldName;
                }
                return ($objValue == $fieldValue);
            },
            self::RULE_METHOD => function () {
                $obj = func_get_arg(1);
                $nameValueStringArr = explode('=', func_get_arg(2), 2);
                $methodName = $nameValueStringArr[0];
                $fieldValue = (array_key_exists(1, $nameValueStringArr) && strlen($nameValueStringArr[1]) > 0) ?
                    $nameValueStringArr[1] : true;
                $objValue = false;
                if (is_object($obj)) {
                    @$objValue = $obj->$methodName();
                }
                return ($objValue == $fieldValue);
            },
            self::RULE_EMPTY_ALLOWED => function () {
                return true;
            },
            self::RULE_GRAPH => function () {
                $fieldValue = func_get_arg(1);
                return (preg_match('/^[[:graph:]]+$/u', $fieldValue) > 0);
            },
        );
    }

    protected $errorMessages = array();

    protected function _setDefaultErrorMsgCallables()
    {
        $this->errorMessages = array(
            self::RULE_REQUIRED => function ($fieldName, $fieldValue, $validationParam) {
                return (array_key_exists($fieldName, $this->data)) || property_exists($this->data,$validationParam);
            },
            self::RULE_EMAIL => function ($fieldName, $fieldValue, $validationParam) {
                return filter_var($fieldValue, FILTER_VALIDATE_EMAIL);
            },
            self::RULE_NAME => function ($fieldName, $fieldValue, $validationParam) {
                return (preg_match('/^[[:alpha:]�������\s\-]+$/', $fieldValue) > 0);
            },
            self::RULE_ADDR_STREET => function ($fieldName, $fieldValue, $validationParam) {
                return (preg_match('/^[[:alpha:]�������\s\-\.]+$/', $fieldValue) > 0);
            },
            self::RULE_ADDR_STREET_NO => function ($fieldName, $fieldValue, $validationParam) {
                return (preg_match('/^[[:digit:]]+[[:alpha:]]{0,1}(?:\s*\-\s*[[:digit:]]{0,}[[:alpha:]]*){0,1}$/', $fieldValue) > 0);
            },
            self::RULE_ADDR_STREET_PLUS_NO => function ($fieldName, $fieldValue, $validationParam) {
                return (preg_match('/^[[:alpha:]�������\s\-\.]+\s+[[:digit:]]+[[:alpha:]]{0,1}(?:\s*\-\s*[[:digit:]]{0,}[[:alpha:]]*){0,1}$/', $fieldValue) > 0);
            },
            self::RULE_ADDR_CITY => function ($fieldName, $fieldValue, $validationParam) {
                return (preg_match('/^[[:alpha:]�������\s\-\.]+$/', $fieldValue) > 0);
            },
            self::RULE_ADDR_ZIP => function ($fieldName, $fieldValue, $validationParam) {
                return (preg_match('/^[0-9]{5}$/', $fieldValue) > 0);
            },
            self::RULE_STR_WS_DASH_DOT => function ($fieldName, $fieldValue, $validationParam) {
                return (preg_match('/^[[:alpha:]�������\-\.]+$/', $fieldValue) > 0);
            },
            self::RULE_STR_WS_DASH_DOT_PAR => function ($fieldName, $fieldValue, $validationParam) {
                return (preg_match('/^[[:alpha:]�������\-\.\(\)]+$/', $fieldValue) > 0);
            },
            self::RULE_URL => function ($fieldName, $fieldValue, $validationParam) {
                return filter_var($fieldValue, FILTER_VALIDATE_URL);
            },
            self::RULE_ALPHA => function ($fieldName, $fieldValue, $validationParam) {
                return (preg_match('/^[[:alpha:]�������]+$/', $fieldValue) > 0);
            },
            self::RULE_ALPHA_CAPS => function ($fieldName, $fieldValue, $validationParam) {
                return (preg_match('/^[[:upper:]���]+$/', $fieldValue) > 0);
            },
            self::RULE_ALPHA_SMALL => function ($fieldName, $fieldValue, $validationParam) {
                return (preg_match('/^[[:lower:]����]+$/', $fieldValue) > 0);
            },
            self::RULE_DIGIT => function ($fieldName, $fieldValue, $validationParam) {
                return (preg_match('/^[[:digit:]]+$/', $fieldValue) > 0);
            },
            self::RULE_DECIMAL => function ($fieldName, $fieldValue, $validationParam) {
                return (preg_match('/^[[:digit:]]+(\\' . $this->decimalSeparator . '[[:digit:]]{1,})|[[:digit:]]$/', $fieldValue) > 0);
            },
            self::RULE_ALNUM => function ($fieldName, $fieldValue, $validationParam) {
                return (preg_match('/^[[:alnum:]�������]+$/', $fieldValue) > 0);
            },
            self::RULE_GENERAL_TEXT => function ($fieldName, $fieldValue, $validationParam) {
                return (strip_tags($fieldValue) === $fieldValue);
            },
            self::RULE_DATE => function ($fieldName, $fieldValue, $validationParam) {
                return ($this->dateValidation[$this->dateFormat]($fieldValue));
            },
            self::RULE_DATETIME => function ($fieldName, $fieldValue, $validationParam) {
                return $this->dateValidation[$this->dateTimeFormat]($fieldValue);
            },
            self::RULE_MIN_LENGTH => function ($fieldName, $fieldValue, $validationParam) {
                return (mb_strlen($fieldValue, 'UTF-8') >= $validationParam);
            },
            self::RULE_MAX_LENGTH => function ($fieldName, $fieldValue, $validationParam) {
                return (mb_strlen($fieldValue, 'UTF-8') <= $validationParam);
            },
            self::RULE_MIN_NUMERIC => function ($fieldName, $fieldValue, $validationParam) {
                return ((float)$fieldValue >= (float)$validationParam);
            },
            self::RULE_MAX_NUMERIC => function ($fieldName, $fieldValue, $validationParam) {
                return ((float)$fieldValue <= (float)$validationParam);
            },
            self::RULE_MIN_DATE => function ($fieldName, $fieldValue, $validationParam) {
                return $this->_dateStrHasOrdinalRelToRefDateStr($fieldValue, $validationParam, self::ORDINAL_COMPARATOR_GTE);
            },
            self::RULE_MAX_DATE => function ($fieldName, $fieldValue, $validationParam) {
                return $this->_dateStrHasOrdinalRelToRefDateStr($fieldValue, $validationParam, self::ORDINAL_COMPARATOR_LTE);
            }
        );
    }

    protected function _setRuleConstants()
    {
        $ref = new ReflectionClass($this);
        $constants = $ref->getConstants();
        foreach ($constants as $constName => $constVal) {
            if (static::strStartsWith($constName, 'RULE_')) {
                $this->ruleConstants[$constName] = $constVal;
            }
        }
    }

    /**
     * @param $ruleName
     * @return bool
     */
    public function isValidRuleName($ruleName)
    {
        if (!(count($this->ruleConstants) > 0)) {
            $this->_setRuleConstants();
        }
        return in_array($ruleName, $this->ruleConstants, true);
    }

    /**
     * @param $ruleName
     * @return bool
     */
    public static function isValidDefaultInlineRuleName($ruleName)
    {
        $arr = array(
            self::RULE_REQUIRED,
            self::RULE_EMAIL,
            self::RULE_NAME,
            self::RULE_ADDR_STREET,
            self::RULE_ADDR_STREET_NO,
            self::RULE_ADDR_STREET_PLUS_NO,
            self::RULE_ADDR_CITY,
            self::RULE_ADDR_ZIP,
            self::RULE_STR_WS_DASH_DOT,
            self::RULE_STR_WS_DASH_DOT_PAR,
            self::RULE_URL,
            self::RULE_ALPHA,
            self::RULE_ALPHA_CAPS,
            self::RULE_ALPHA_SMALL,
            self::RULE_DIGIT,
            self::RULE_DECIMAL,
            self::RULE_ALNUM,
            self::RULE_GENERAL_TEXT,
            self::RULE_DATE,
            self::RULE_DATETIME,
            self::RULE_MIN_LENGTH,
            self::RULE_MAX_LENGTH,
            self::RULE_MIN_NUMERIC,
            self::RULE_MAX_NUMERIC,
            self::RULE_MIN_DATE,
            self::RULE_MAX_DATE,
            self::RULE_CUST_REGEX
        );
        return in_array($ruleName, $arr, 1);
    }

    /**
     * @param $dateFormatString
     * @param $dataSourceString
     * @return bool
     */
    protected function _isValidByDateFormat($dateFormatString, $dataSourceString)
    {
        $dateTime = \DateTime::createFromFormat($dateFormatString, $dataSourceString);
        $dateTimeString = $dateTime->format($dateFormatString);
        return ($dateTimeString === $dataSourceString);
    }


    /**
     * NewValidator constructor.
     * @param array $dataRules
     * @param array|null $data
     */
    public function __construct(array $dataRules, array $data = null)
    {
        $this->_setDefaultRuleValidation();
        $this->_setDefaultDateFormatValidation();
        if(null === $data) { $data = []; }
        $this->setData($data);
        if (is_array($dataRules)) {
            foreach ($dataRules as $fieldName => $ruleStr) {
                $this->_parseRuleString($fieldName, $ruleStr);
            }
        }
    }

    /**
     * @param array $data
     */
    public function setData(array $data) {
        foreach($data as $dataKey => $dataVal) {
            $this->data[$dataKey] = $dataVal;
        }
    }

    /**
     * @param $fieldName
     * @param $ruleString
     */
    protected function _parseRuleString($fieldName, $ruleString)
    {
        if (!array_key_exists($fieldName, $this->data) && !property_exists($this->data,$fieldName)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%s is not the name of a data-item.',
                    strip_tags($fieldName)
                )
            );
        }

        $rules = explode(self::CONF_RULE_SEPARATOR, $ruleString);
        if (!is_array($rules) || count($rules) < 1) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%s is not a string of rules split by %s.',
                    strip_tags($rules),
                    self::CONF_RULE_SEPARATOR
                )
            );
        }

        foreach ($rules as $rule) {
            $ruleParts = explode(self::CONF_RULE_VALUE_SEPARATOR, $rule, 2);
            $ruleName = $ruleParts[0];
            $ruleValue = (isset($ruleParts[1]) ? $ruleParts[1] : '');
            if (self::RULE_CUST_REGEX !== $ruleName && !array_key_exists($ruleName, $this->ruleValidation)) {
                throw new \InvalidArgumentException(
                    sprintf(
                        '%s is not a valid rule.',
                        strip_tags($ruleName)
                    )
                );
            }
            if (self::RULE_CUST_REGEX === $ruleName) {
                throw new \InvalidArgumentException('A custom regex-rule can only be set via method.');
            }
            $this->rules[$fieldName][$ruleName] = array('callable' => &$this->ruleValidation[$ruleName], 'ruleValue' => $ruleValue);
        }
    }


    /**
     * @param $dateStr
     * @param $refDateStr
     * @param $comparator
     * @return bool
     */
    protected function _dateStrHasOrdinalRelToRefDateStr($dateStr, $refDateStr, $comparator)
    {
        $dt = new \DateTime(strtotime($dateStr));
        $dtRef = new \DateTime(strtotime($refDateStr));
        switch ($comparator) {
            case self::ORDINAL_COMPARATOR_EQ:
                $isValid = ($dt->getTimestamp() === $dtRef->getTimestamp());
                break;
            case self::ORDINAL_COMPARATOR_LT:
                $isValid = ($dt->getTimestamp() < $dtRef->getTimestamp());
                break;
            case self::ORDINAL_COMPARATOR_LTE:
                $isValid = ($dt->getTimestamp() <= $dtRef->getTimestamp());
                break;
            case self::ORDINAL_COMPARATOR_GT:
                $isValid = ($dt->getTimestamp() > $dtRef->getTimestamp());
                break;
            case self::ORDINAL_COMPARATOR_GTE:
                $isValid = ($dt->getTimestamp() >= $dtRef->getTimestamp());
                break;
            default:
                $isValid = ($dt->getTimestamp() === $dtRef->getTimestamp());
                break;
        }
        return $isValid;
    }

    /**
     * After validation, $fieldStatusArr will contain detailed information about the validation result, including rule-mismatch error-messages
     * @param array|null $fieldStatusArr
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function isValid(array &$fieldStatusArr)
    {
        if (null === $this->data) {
            throw new \InvalidArgumentException(
                'Method \'Cannot check validity while data is not set\''
            );
        }
        $isValid = true;
        $statusArr = array();

        foreach ($this->data as $fieldName => $fieldValue) {
            $singleFieldStatusArr = [];
            $this->_validateField($fieldName,$fieldValue,$singleFieldStatusArr,$isValid);
            $statusArr[$fieldName] = $singleFieldStatusArr;
        }
        $fieldStatusArr = $statusArr;
        return $isValid;
    }

    /**
     * @param $fieldName
     * @param $fieldValue
     * @param $singleFieldStatusArr
     * @param $isValidTotal
     * @return bool
     */
    protected function _validateField($fieldName, $fieldValue, &$singleFieldStatusArr, &$isValidTotal) {
        $isValidField = true;
        $unmatchedRules = [];
        $appliedRulesString = '';
        $i = 0;
        if (isset($this->rules[$fieldName])) {
            $emptyAllowed = (array_key_exists(self::RULE_EMPTY_ALLOWED,$this->rules[$fieldName]) || !array_key_exists(self::RULE_REQUIRED,$this->rules[$fieldName]));
            if (!$emptyAllowed) {
                foreach ($this->rules[$fieldName] as $ruleName => $ruleArr) {
                    //$fieldValue = $this->data[$fieldName];
                    $ruleCallable = $ruleArr['callable'];
                    $ruleValue = $ruleArr['ruleValue'];
                    if ($i > 0) {
                        $appliedRulesString .= self::CONF_RULE_SEPARATOR;
                    }
                    $appliedRulesString .= $ruleName;
                    $fieldStatusArrTMP = [];
                    if (!$ruleCallable($fieldName, $fieldValue, $ruleValue, $fieldStatusArrTMP)) {
                        $isValidField = false;
                        $unmatchedRules = (isset($fieldStatusArrTMP['unmatchedRules']) ? $fieldStatusArrTMP['unmatchedRules'] : []);
                        $unmatchedRules[] = array('ruleName' => $ruleName, 'ruleValue' => $ruleValue, 'ruleError' => $this->getRuleErrorMessage($ruleName));
                        $isValidTotal = false;
                    }
                    ++$i;
                }
            }
            $singleFieldStatusArr = array('value' => $fieldValue, 'valid' => $isValidField, 'appliedRules' => $appliedRulesString, 'unmatchedRules' => $unmatchedRules);
        }
        return $isValidField;
    }

    /**
     * @return bool
     */
    public function isInvalid()
    {
        $dummyArr = [];
        return !($this->isValid($dummyArr));
    }

    /**
     * @param array $rules
     */
    public function setRules(array $rules)
    {
        if (null === $this->data) {
            throw new \BadMethodCallException(
                'Method \'setRules cannot be called while data is not set\''
            );
        }
        foreach ($rules as $fieldName => $ruleStr) {
            $this->_parseRuleString($fieldName, $ruleStr);
        }
    }

    /**
     * @param $ruleName
     * @return mixed|string
     */
    public function getRuleErrorMessage($ruleName ) {
        $errorMsgConstName = $ruleName . self::ERROR_MSG_CONST_SUFFIX;
        $fqn = 'static::' . $errorMsgConstName;
        if (defined($fqn)) {
            return constant($fqn);
        } else {
            return '';
        }
    }


    /**
     * @param $haystack
     * @param $needle
     * @return bool
     */
    public static function strStartsWith($haystack, $needle)
    {
        // search backwards starting from haystack length characters from the end
        return $needle === '' || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
    }

    /**
     * @param $haystack
     * @param $needle
     * @return bool
     */
    public static function strEndsWith($haystack, $needle)
    {
        // search forward starting from end minus needle length characters
        return $needle === '' || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
    }

    /**
     * @param $dataItem
     * @param $ruleString
     * @return array
     */
    public static function evaluateDataItemAgainstRuleString($dataItem,$ruleString) {
        $dataArr = array(
            'dataItem' => $dataItem
        );
        $ruleArr = array(
            'dataItem' => $ruleString
        );
        $validator = new self($ruleArr, $dataArr);
        $fieldStatusArr = array();
        $validator->isValid($fieldStatusArr);
        unset($validator);
        return $fieldStatusArr;
    }
}