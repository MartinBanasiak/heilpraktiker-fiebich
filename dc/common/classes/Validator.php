<?php
namespace DynCom\dc\common\classes;
use ReflectionClass;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/12/2015
 * Time: 3:42 AM
 */
class Validator
{

    const ORDINAL_COMPARATOR_EQ = '=';
    const ORDINAL_COMPARATOR_LT = '<';
    const ORDINAL_COMPARATOR_LTE = '<=';
    const ORDINAL_COMPARATOR_GT = '>';
    const ORDINAL_COMPARATOR_GTE = '>=';

    const DECIMAL_SEPARATOR_COMMA = ',';
    const DECIMAL_SEPARATOR_POINT = '.';

    const DATE_FORMAT_ISO_8601 = 'yyyy-mm-dd';
    const DATE_FORMAT_DDMMYY_DASHES = 'dd-mm-yy';
    const DATE_FORMAT_DDMMYYYY_DASHES = 'dd-mm-yyyy';
    const DATE_FORMAT_DDMMYY_DOTS = 'dd.mm.yy';
    const DATE_FORMAT_DDMMYYYY_DOTS = 'dd.mm.yyyy';
    const DATE_FORMAT_DDMMYY_SLASHES = 'dd/mm/yy';
    const DATE_FORMAT_DDMMYYYY_SLASHES = 'dd/mm/yyyy';
    const DATE_FORMAT_MMDDYY_DASHES = 'mm-dd-yy';
    const DATE_FORMAT_MMDDYYYY_DASHES = 'mm-dd-yyyy';
    const DATE_FORMAT_MMDDYY_DOTS = 'mm.dd.yy';
    const DATE_FORMAT_MMDDYYYY_DOTS = 'mm.dd.yyyy';
    const DATE_FORMAT_MMDDYY_SLASHES = 'mm/dd/yy';
    const DATE_FORMAT_MMDDYYYY_SLASHES = 'mm/dd/yyyy';

    const DATETIME_FORMAT_ISO_8601 = 'yyyy-mm-ddThh:mm:ss';
    const DATETIME_FORMAT_MYSQL = 'yyyy-mm-dd hh:mm:ss';

    const TIME_FORMAT_COLON = 'hh:mm:ss';

    const CONF_RULE_SEPARATOR = '|';
    const CONF_RULE_VALUE_SEPARATOR = ':';

    const RULE_REQUIRED = 'required';
    const ERRORMSG_RULE_REQUIRED_DE = 'Bitte füllen Sie das Feld %fieldname% aus';
    const ERRORMSG_RULE_REQUIRED_EN = 'Please fill in the field %fieldname%.';
    const RULE_EMAIL = 'email';
    const ERRORMSG_RULE_EMAIL_DE = 'Bitte geben Sie eine gültige E-Mail Adresse in das Feld %fieldname% ein.';
    const ERRORMSG_RULE_EMAIL_EN = 'Please enter a valid email-address into field %fieldname%.';
    const RULE_NAME = 'name';
    const ERRORMSG_RULE_NAME_DE = 'Bitte geben Sie einen gültigen Namen in das Feld %fieldname% ein.';
    const ERRORMSG_RULE_NAME_EN = 'Please enter a valid name into field %fieldname%.';
    const RULE_ADDR_STREET = 'addrstreet';
    const ERRORMSG_RULE_ADDR_STREET_DE = 'Bitte geben Sie einen gültigen Straßennamen in das Feld %fieldname% ein.';
    const ERRORMSG_RULE_ADDR_STREET_EN = 'Please enter a valid street-name into field %fieldname%.';
    const RULE_ADDR_STREET_NO = 'addrstreetno';
    const ERRORMSG_RULE_ADDR_STREET_NO_DE = 'Bitte geben Sie eine gültige Hausnummer in das Feld %fieldname% ein.';
    const ERRORMSG_RULE_ADDR_STREET_NO_EN = 'Please enter a valid street-number into field %fieldname%.';
    const RULE_ADDR_STREET_PLUS_NO = 'addrstreetplusno';
    const ERRORMSG_RULE_ADDR_STREET_PLUS_NO_DE = 'Bitte geben Sie eien gültige Kombination aus Straßennamen und Hausnummer in das Feld %fieldname% ein.';
    const ERRORMSG_RULE_ADDR_STREET_PLUS_NO_EN = 'Please enter a valid combination of street-name and street-number into field %fieldname%.';
    const RULE_ADDR_CITY = 'addrcity';
    const ERRORMSG_RULE_ADDR_CITY_DE = 'Bitte geben Sie eine gültige Stadt im Feld %fieldname% an,';
    const ERRORMSG_RULE_ADDR_CITY_EN = 'Please enter a valid city into field %fieldname%,';
    const RULE_ADDR_ZIP = 'addrzip';
    const ERRORMSG_RULE_ADDR_ZIP_DE = 'Bitte geben Sie eine gültige Postleitzahl in das Feld %fieldname% ein.';
    const ERRORMSG_RULE_ADDR_ZIP_EN = 'Please enter a valid ZIP-Code into field %fieldname%.';
    const RULE_STR_WS_DASH_DOT = 'strwsdashdot';
    const ERRORMSG_RULE_STR_WS_DASH_DOT_DE = 'Bitte geben Sie nur Buchstaben, Leerzeichen, Minuszeichen und Punkte in das Feld %fieldname% ein.';
    const ERRORMSG_RULE_STR_WS_DASH_DOT_EN = 'Please enter only letters, spaces, minus-signs and stops into field %fieldname%';
    const RULE_STR_WS_DASH_DOT_PAR = 'strwsdashdotparen';
    const ERRORMSG_RULE_STR_WS_DASH_DOT_PAR_DE = 'Bitte geben Sie nur Buchstaben, Leerzeichen, Minuszeichen runde Klammern und Punkte in das Feld %fieldname% ein.';
    const ERRORMSG_RULE_STR_WS_DASH_DOT_PAR_EN = 'Please enter only letters, spaces, minus-signs, parantheses and stops into field %fieldname%.';
    const RULE_URL = 'url';
    const ERRORMSG_RULE_URL_DE = 'Bitte geben Sie eine gültige URL im Feld %fieldname% an';
    const ERRORMSG_RULE_URL_EN = 'Please enter a valid URL into field %fieldname%.';
    const RULE_ALPHA = 'Alpha';
    const ERRORMSG_RULE_ALPHA_DE = 'Bitte geben Sie nur Buchstaben in das Feld %fieldname% ein.';
    const ERRORMSG_RULE_ALPHA_EN = 'Please enter only letters into field %fieldname%.';
    const RULE_ALPHA_CAPS = 'ALPHA';
    const ERRORMSG_RULE_ALPHA_CAPS_DE = 'Bitte geben Sie nur Großbuchstaben in das Feld %fieldname% ein.';
    const ERRORMSG_RULE_ALPHA_CAPS_EN = 'Please enter only capital letters into field %fieldname%.';
    const RULE_ALPHA_SMALL = 'alpha';
    const ERRORMSG_RULE_ALPHA_SMALL_DE = 'Bitte geben Sie nur Kleinbuchstabenb in das Feld %fieldname% ein.';
    const ERRORMSG_RULE_ALPHA_SMALL_EN = 'Please enter only small letters into field %fieldname%.';
    const RULE_DIGIT = 'digit';
    const ERRORMSG_RULE_DIGIT_DE = 'Bitte geben Sie nur Ziffern in das Feld %fieldname% ein.';
    const ERRORMSG_RULE_DIGIT_EN = 'Please enter only digits into field %fieldname%.';
    const RULE_DECIMAL = 'decimal';
    const ERRORMSG_RULE_DECIMAL_DE = 'Bitte geben Sie eine Dezimalzahl in das Feld %fieldname% ein.';
    const ERRORMSG_RULE_DECIMAL_EN = 'Please enter a valid decimal number into field %fieldname%.';
    const RULE_ALNUM = 'alnum';
    const ERRORMSG_RULE_ALNUM_DE = 'Bitte geben Sie nur Buchstaben und Ziffern in das Feld %fieldname% ein.';
    const ERRORMSG_RULE_ALNUM_EN = 'Please enter only letters and digits into field %fieldname%.';
    const RULE_GENERAL_TEXT = 'text';
    const ERRORMSG_RULE_GENERAL_TEXT_DE = 'Die Eingabe in das Feld %fieldname% war ungültig. Bitte versuchen Sie es noch einmal.';
    const ERRORMSG_RULE_GENERAL_TEXT_EN = 'Your input into field %fieldname% was invalid. Please try again.';
    const RULE_DATE = 'date';
    const ERRORMSG_RULE_DATE_DE = 'Bitte geben Sie ein gültiges Datum in das Feld %fieldname% ein.';
    const ERRORMSG_RULE_DATE_EN = 'Please enter a valid date into field %fieldname%.';
    const RULE_DATETIME = 'dateTime';
    const ERRORMSG_RULE_DATETIME_DE = 'Bitte geben Sie eine gültige Datum-Uhrzeit-Kombination in das Feld %fieldname% ein.';
    const ERRORMSG_RULE_DATETIME_EN = 'Please enter a valid datetime into field %fieldname%.';
    const RULE_MIN_LENGTH = 'minlen';
    const ERRORMSG_RULE_MIN_LENGTH_DE = 'Bitte geben Sie mindestens %validationparam% Zeichen in das Feld %fieldname% ein.';
    const ERRORMSG_RULE_MIN_LENGTH_EN = 'Please enter at least %validationparam% characters into field %fieldname%.';
    const RULE_MAX_LENGTH = 'maxlen';
    const ERRORMSG_RULE_MAX_LENGTH_DE = 'Bitte geben Sie höchstens %vaidationparam% Zeichen in das Feld %fieldname% ein.';
    const ERRORMSG_RULE_MAX_LENGTH_EN = 'Please enter at most %validationparam% characters into field %fieldname%.';
    const RULE_MIN_NUMERIC = 'minnum';
    const ERRORMSG_RULE_MIN_NUMERIC_DE = 'Bitte geben Sie einen Wert größer oder gleich %validationparam% in das Feld %fieldname% ein.';
    const ERRORMSG_RULE_MIN_NUMERIC_EN = 'Please enter a number greater than or equal to %validationparam% into field %fieldname%.';
    const RULE_MAX_NUMERIC = 'maxnum';
    const ERRORMSG_RULE_MAX_NUMERIC_DE = 'Bitte geben Sie einen Wert kleiner oder gleich %validationparam% in das Feld %fieldname% ein.';
    const ERRORMSG_RULE_MAX_NUMERIC_EN = 'Please enter a number less than or equal to %validationparam% into field %fieldname%.';
    const RULE_MIN_DATE = 'mindate';
    const ERRORMSG_RULE_MIN_DATE_DE = 'Bitte geben Sie ein Datum frühestens ab dem %validationparam% in das Feld %fieldname% ein.';
    const ERRORMSG_RULE_MIN_DATE_EN = 'Please enter a date greater than or equal to %validationparam% into field %fieldname%.';
    const RULE_MAX_DATE = 'maxdate';
    const ERRORMSG_RULE_MAX_DATE_DE = 'Bitte geben Sie ein Datum bis höchstens den %validationparam% in das Feld %fieldname% ein.';
    const ERRORMSG_RULE_MAX_DATE_EN = 'Please enter a date less than or equal to %validationparam% into field %fieldname%.';
    const RULE_CUST_REGEX = 'regex';
    const ERRORMSG_RULE_CUST_REGEX_DE = 'Das Format Ihrer Eingabe in das Feld %fieldname% war ungültig. Bitte versuchen Sie es nocheinmal.';
    const ERRORMSG_RULE_CUST_REGEX_EN = 'Your input into field %fieldname% was invalid. Please try again.';
    const RULE_REQUIRES = 'requires';
    const ERRORMSG_RULE_REQUIRES_DE = 'Das Feld %validationparam% muss gefüllt sein';
    const ERRORMSG_RULE_REQUIRES_EN = 'The field %validationparam% has to be filled in.';
    const RULE_EXTENDS = 'extends';
    const RULE_IN = 'in';
    const ERRORMSG_RULE_IN_DE = 'Ihre Eingabe in das Feld %fieldname% war ungültig. Bitte versuchen Sie es nocheinmal.';
    const ERRORMSG_RULE_IN_EN = 'Your input into field %fieldname% was invalid. Please try again.';
    const RULE_INSTANCEOF = 'instanceof';
    const RULE_FIELD = 'field';
    const RULE_METHOD = 'method';
    const RULE_EMPTY_ALLOWED = 'orempty';
    const ERRORMSG_RULE_EMPTY_ALLOWED_DE = '';
    const ERRORMSG_RULE_EMPTY_ALLOWED_EN = '';

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
     * @param $code
     */
    public function setLanguageCode($code)
    {
        $this->languageCode = $code;
    }

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
        if (!array_key_exists($fieldName, $this->data)) {
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
                return (array_key_exists($fieldName, $this->data));
            },
            self::RULE_EMAIL => function () {
                $fieldValue = func_get_arg(1);
                return filter_var($fieldValue, FILTER_VALIDATE_EMAIL);
            },
            self::RULE_NAME => function () {
                $fieldValue = func_get_arg(1);
                return (preg_match('/^[[:alpha:]äöüßÄÖÜ\s\-]+$/', $fieldValue) > 0);
            },
            self::RULE_ADDR_STREET => function () {
                $fieldValue = func_get_arg(1);
                return (preg_match('/^[[:alpha:]äöüßÄÖÜ\s\-\.]+$/', $fieldValue) > 0);
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
                        '/^[[:alpha:]äöüßÄÖÜ\s\-\.]+\s+[[:digit:]]+[[:alpha:]]{0,1}(?:\s*\-\s*[[:digit:]]{0,}[[:alpha:]]*){0,1}$/',
                        $fieldValue
                    ) > 0);
            },
            self::RULE_ADDR_CITY => function () {
                $fieldValue = func_get_arg(1);
                return (preg_match('/^[[:alpha:]äöüßÄÖÜ\s\-\.]+$/', $fieldValue) > 0);
            },
            self::RULE_ADDR_ZIP => function () {
                $fieldValue = func_get_arg(1);
                return (preg_match('/^[0-9]{5}$/', $fieldValue) > 0);
            },
            self::RULE_STR_WS_DASH_DOT => function () {
                $fieldValue = func_get_arg(1);
                return (preg_match('/^[[:alpha:]äöüßÄÖÜ\-\.]+$/', $fieldValue) > 0);
            },
            self::RULE_STR_WS_DASH_DOT_PAR => function () {
                $fieldValue = func_get_arg(1);
                return (preg_match('/^[[:alpha:]äöüßÄÖÜ\-\.\(\)]+$/', $fieldValue) > 0);
            },
            self::RULE_URL => function () {
                $fieldValue = func_get_arg(1);
                return filter_var($fieldValue, FILTER_VALIDATE_URL);
            },
            self::RULE_ALPHA => function () {
                $fieldValue = func_get_arg(1);
                return (preg_match('/^[[:alpha:]äöüßÄÖÜ]+$/', $fieldValue) > 0);
            },
            self::RULE_ALPHA_CAPS => function () {
                $fieldValue = func_get_arg(1);
                return (preg_match('/^[[:upper:]ÄÖÜ]+$/', $fieldValue) > 0);
            },
            self::RULE_ALPHA_SMALL => function () {
                $fieldValue = func_get_arg(1);
                return (preg_match('/^[[:lower:]äöüß]+$/', $fieldValue) > 0);
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
                return (preg_match('/^[[:alnum:]äöüßÄÖÜ]+$/', $fieldValue) > 0);
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
                return ($this->_dateStrHasOrdinalRelToRefDateStr(
                    $fieldValue,
                    $validationParam,
                    self::ORDINAL_COMPARATOR_GTE
                ));
            },
            self::RULE_MAX_DATE => function () {
                $fieldValue = func_get_arg(1);
                $validationParam = func_get_arg(2);
                return ($this->_dateStrHasOrdinalRelToRefDateStr(
                    $fieldValue,
                    $validationParam,
                    self::ORDINAL_COMPARATOR_LTE
                ));
            },
            self::RULE_REQUIRES => function () {
                $validationParam = func_get_arg(2);
                $dataExists = array_key_exists($validationParam, $this->data);
                if (!$dataExists) return false;
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
                if (!$extendedRulesExists) return $isValid;
                //Validate the data of this field against the rules of the extended Field
                $this->_validateField($validationParam, $fieldValue, $fieldStatusArr, $isValid);
                return $isValid;
            },
            self::RULE_IN => function () {
                $fieldValue = func_get_arg(1);
                $validationParam = func_get_arg(2);
                $validValues = json_decode($validationParam, false);
                $isValid = (is_array($validValues) && in_array($fieldValue, $validValues, true));
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
            }
        );
    }

    protected $errorMessages = array();

    protected function _setDefaultErrorMsgCallables()
    {
        $this->errorMessages = array(
            self::RULE_REQUIRED => function ($fieldName, $fieldValue, $validationParam) {
                return (array_key_exists($fieldName, $this->data));
            },
            self::RULE_EMAIL => function ($fieldName, $fieldValue, $validationParam) {
                return filter_var($fieldValue, FILTER_VALIDATE_EMAIL);
            },
            self::RULE_NAME => function ($fieldName, $fieldValue, $validationParam) {
                return (preg_match('/^[[:alpha:]äöüßÄÖÜ\s\-]+$/', $fieldValue) > 0);
            },
            self::RULE_ADDR_STREET => function ($fieldName, $fieldValue, $validationParam) {
                return (preg_match('/^[[:alpha:]äöüßÄÖÜ\s\-\.]+$/', $fieldValue) > 0);
            },
            self::RULE_ADDR_STREET_NO => function ($fieldName, $fieldValue, $validationParam) {
                return (preg_match(
                        '/^[[:digit:]]+[[:alpha:]]{0,1}(?:\s*\-\s*[[:digit:]]{0,}[[:alpha:]]*){0,1}$/',
                        $fieldValue
                    ) > 0);
            },
            self::RULE_ADDR_STREET_PLUS_NO => function ($fieldName, $fieldValue, $validationParam) {
                return (preg_match(
                        '/^[[:alpha:]äöüßÄÖÜ\s\-\.]+\s+[[:digit:]]+[[:alpha:]]{0,1}(?:\s*\-\s*[[:digit:]]{0,}[[:alpha:]]*){0,1}$/',
                        $fieldValue
                    ) > 0);
            },
            self::RULE_ADDR_CITY => function ($fieldName, $fieldValue, $validationParam) {
                return (preg_match('/^[[:alpha:]äöüßÄÖÜ\s\-\.]+$/', $fieldValue) > 0);
            },
            self::RULE_ADDR_ZIP => function ($fieldName, $fieldValue, $validationParam) {
                return (preg_match('/^[0-9]{5}$/', $fieldValue) > 0);
            },
            self::RULE_STR_WS_DASH_DOT => function ($fieldName, $fieldValue, $validationParam) {
                return (preg_match('/^[[:alpha:]äöüßÄÖÜ\-\.]+$/', $fieldValue) > 0);
            },
            self::RULE_STR_WS_DASH_DOT_PAR => function ($fieldName, $fieldValue, $validationParam) {
                return (preg_match('/^[[:alpha:]äöüßÄÖÜ\-\.\(\)]+$/', $fieldValue) > 0);
            },
            self::RULE_URL => function ($fieldName, $fieldValue, $validationParam) {
                return filter_var($fieldValue, FILTER_VALIDATE_URL);
            },
            self::RULE_ALPHA => function ($fieldName, $fieldValue, $validationParam) {
                return (preg_match('/^[[:alpha:]äöüßÄÖÜ]+$/', $fieldValue) > 0);
            },
            self::RULE_ALPHA_CAPS => function ($fieldName, $fieldValue, $validationParam) {
                return (preg_match('/^[[:upper:]ÄÖÜ]+$/', $fieldValue) > 0);
            },
            self::RULE_ALPHA_SMALL => function ($fieldName, $fieldValue, $validationParam) {
                return (preg_match('/^[[:lower:]äöüß]+$/', $fieldValue) > 0);
            },
            self::RULE_DIGIT => function ($fieldName, $fieldValue, $validationParam) {
                return (preg_match('/^[[:digit:]]+$/', $fieldValue) > 0);
            },
            self::RULE_DECIMAL => function ($fieldName, $fieldValue, $validationParam) {
                return (preg_match(
                        '/^[[:digit:]]+(\\' . $this->decimalSeparator . '[[:digit:]]{1,})|[[:digit:]]$/',
                        $fieldValue
                    ) > 0);
            },
            self::RULE_ALNUM => function ($fieldName, $fieldValue, $validationParam) {
                return (preg_match('/^[[:alnum:]äöüßÄÖÜ]+$/', $fieldValue) > 0);
            },
            self::RULE_GENERAL_TEXT => function ($fieldName, $fieldValue, $validationParam) {
                return (strip_tags($fieldValue) === $fieldValue);
            },
            self::RULE_DATE => function ($fieldName, $fieldValue, $validationParam) {
                return ($this->dateValidation[$this->dateFormat]($fieldValue));
            },
            self::RULE_DATETIME => function ($fieldName, $fieldValue, $validationParam) {
                return ($this->dateValidation[$this->dateTimeFormat]($fieldValue));
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
                return ($this->_dateStrHasOrdinalRelToRefDateStr(
                    $fieldValue,
                    $validationParam,
                    self::ORDINAL_COMPARATOR_GTE
                ));
            },
            self::RULE_MAX_DATE => function ($fieldName, $fieldValue, $validationParam) {
                return ($this->_dateStrHasOrdinalRelToRefDateStr(
                    $fieldValue,
                    $validationParam,
                    self::ORDINAL_COMPARATOR_LTE
                ));
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
        return (in_array($ruleName, $arr, 1));
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
     * Validator constructor.
     * @param array $dataRules
     * @param array|null $data
     */
    public function __construct(array $dataRules, array $data = null)
    {
        $this->_setDefaultRuleValidation();
        $this->_setDefaultDateFormatValidation();
        if (null === $data) {
            $data = [];
        }
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
    public function setData(array $data)
    {
        foreach ($data as $dataKey => $dataVal) {
            $this->data[$dataKey] = $dataVal;
        }
    }

    /**
     * @param $fieldName
     * @param $ruleString
     */
    protected function _parseRuleString($fieldName, $ruleString)
    {
        if (!array_key_exists($fieldName, $this->data)) {
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
     * After validation, $fieldStatusArr will contain detialed information about the validation result, including rule-mismatch error-messages
     * @param array|null $fieldStatusArr
     * @param $data
     * @return bool
     */
    public function isValid(array &$fieldStatusArr,$data)
    {
        $this->_setErrorMessages();
        if (null === $this->data) {
            throw new \InvalidArgumentException(
                'Method \'Cannot check validity while data is not set\''
            );
        }
        $isValid = true;
        $statusArr = array();

        foreach ($this->data as $fieldName => $fieldValue) {
            $singleFieldStatusArr = [];
            $this->_validateField($fieldName, $fieldValue, $singleFieldStatusArr, $isValid);
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
    protected function _validateField($fieldName, $fieldValue, &$singleFieldStatusArr, &$isValidTotal)
    {
        $isValidField = true;
        $unmatchedRules = [];
        $appliedRulesString = '';
        $i = 0;
        if (isset($this->rules[$fieldName])) {
            $emptyAllowed = (array_key_exists(Validator::RULE_EMPTY_ALLOWED,$this->rules[$fieldName]) || !array_key_exists(Validator::RULE_REQUIRED,$this->rules[$fieldName]));
            if ($emptyAllowed && empty($fieldValue)) {
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
                        $unmatchedRules[] = array('ruleName' => $ruleName, 'ruleValue' => $ruleValue, 'ruleError' => $this->getRuleErrorMessage(
                            $ruleName
                        ));
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
    public function getRuleErrorMessage($ruleName)
    {
        if (isset($this->errorMessages[$ruleName])) {
            return $this->errorMessages[$ruleName];
        } else {
            return '';
        }
    }

    protected function _setErrorMessages()
    {
        $languageSuffix = strtoupper($this->languageCode);
        $constRuleRequired = 'ERRORMSG_RULE_REQUIRED_' . $languageSuffix;
        $constRuleEmail = 'ERRORMSG_RULE_EMAIL_' . $languageSuffix;
        $constRuleName = 'ERRORMSG_RULE_NAME_' . $languageSuffix;
        $constRuleAddrStreet = 'ERRORMSG_RULE_ADDR_STREET_' . $languageSuffix;
        $constRuleAddrStreetNo = 'ERRORMSG_RULE_ADDR_STREET_NO_' . $languageSuffix;
        $constRuleAddrStreetPlusNo = 'ERRORMSG_RULE_ADDR_STREET_PLUS_NO_' . $languageSuffix;
        $constRuleAddrCity = 'ERRORMSG_RULE_ADDR_CITY_' . $languageSuffix;
        $constRuleAddrZip = 'ERRORMSG_RULE_ADDR_ZIP_' . $languageSuffix;
        $constRuleStrWsDashDot = 'ERRORMSG_RULE_STR_WS_DASH_DOT_' . $languageSuffix;
        $constRuleStrWsDashDotPar = 'ERRORMSG_RULE_STR_WS_DASH_DOT_PAR_' . $languageSuffix;
        $constRuleUrl = 'ERRORMSG_RULE_URL_' . $languageSuffix;
        $constRuleAlpha = 'ERRORMSG_RULE_ALPHA_' . $languageSuffix;
        $constRuleAlphaCaps = 'ERRORMSG_RULE_ALPHA_CAPS_' . $languageSuffix;
        $constRuleAlphaSmall = 'ERRORMSG_RULE_ALPHA_SMALL_' . $languageSuffix;
        $constRuleDigit = 'ERRORMSG_RULE_DIGIT_' . $languageSuffix;
        $constRuleAlnum = 'ERRORMSG_RULE_ALNUM_' . $languageSuffix;
        $constRuleDate = 'ERRORMSG_RULE_DATE_' . $languageSuffix;
        $constRuleDatetime = 'ERRORMSG_RULE_DATETIME_' . $languageSuffix;
        $constRuleMinNumeric = 'ERRORMSG_RULE_MIN_NUMERIC_' . $languageSuffix;
        $constRuleMaxNumeric = 'ERRORMSG_RULE_MAX_NUMERIC_' . $languageSuffix;
        $constRuleMinDate = 'ERRORMSG_RULE_MIN_DATE_' . $languageSuffix;
        $constRuleMaxDate = 'ERRORMSG_RULE_MAX_DATE_' . $languageSuffix;
        $constRuleCustRegex = 'ERRORMSG_RULE_CUST_REGEX_' . $languageSuffix;
        $constRuleRequires = 'ERRORMSG_RULE_REQUIRES_' . $languageSuffix;
        $constRuleMinLen = 'ERRORMSG_RULE_MIN_LENGTH_' . $languageSuffix;
        $constRuleMaxLen = 'ERRORMSG_RULE_MAX_LENGTH_' . $languageSuffix;

        $className = __CLASS__;

        $ruleErrors = array(
            self::RULE_REQUIRED => constant("$className::$constRuleRequired"),
            self::RULE_EMAIL => constant("$className::$constRuleEmail"),
            self::RULE_NAME => constant("$className::$constRuleName"),
            self::RULE_ADDR_STREET => constant("$className::$constRuleAddrStreet"),
            self::RULE_ADDR_STREET_NO => constant("$className::$constRuleAddrStreetNo"),
            self::RULE_ADDR_STREET_PLUS_NO => constant("$className::$constRuleAddrStreetPlusNo"),
            self::RULE_ADDR_CITY => constant("$className::$constRuleAddrCity"),
            self::RULE_ADDR_ZIP => constant("$className::$constRuleAddrZip"),
            self::RULE_STR_WS_DASH_DOT => constant("$className::$constRuleStrWsDashDot"),
            self::RULE_STR_WS_DASH_DOT_PAR => constant("$className::$constRuleStrWsDashDotPar"),
            self::RULE_URL => constant("$className::$constRuleUrl"),
            self::RULE_ALPHA => constant("$className::$constRuleAlpha"),
            self::RULE_ALPHA_CAPS => constant("$className::$constRuleAlphaCaps"),
            self::RULE_ALPHA_SMALL => constant("$className::$constRuleAlphaSmall"),
            self::RULE_DIGIT => constant("$className::$constRuleDigit"),
            self::RULE_ALNUM => constant("$className::$constRuleAlnum"),
            self::RULE_DATE => constant("$className::$constRuleDate"),
            self::RULE_DATETIME => constant("$className::$constRuleDatetime"),
            self::RULE_MIN_NUMERIC => constant("$className::$constRuleMinNumeric"),
            self::RULE_MAX_NUMERIC => constant("$className::$constRuleMaxNumeric"),
            self::RULE_MIN_DATE => constant("$className::$constRuleMinDate"),
            self::RULE_MAX_DATE => constant("$className::$constRuleMaxDate"),
            self::RULE_CUST_REGEX => constant("$className::$constRuleCustRegex"),
            self::RULE_REQUIRES => constant("$className::$constRuleRequires"),
            self::RULE_MIN_LENGTH => constant("$className::$constRuleMinLen"),
            self::RULE_MAX_LENGTH => constant("$className::$constRuleMaxLen")
        );
        $this->errorMessages = $ruleErrors;
    }


    /**
     * @param $haystack
     * @param $needle
     * @return bool
     */
    public static function strStartsWith($haystack, $needle)
    {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }

    /**
     * @param $haystack
     * @param $needle
     * @return bool
     */
    public static function strEndsWith($haystack, $needle)
    {
        // search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos(
                $haystack,
                $needle,
                $temp
            ) !== false);
    }

    /**
     * @param $dataItem
     * @param $ruleString
     * @return array
     */
    public static function evaluateDataItemAgainstRuleString($dataItem, $ruleString)
    {
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