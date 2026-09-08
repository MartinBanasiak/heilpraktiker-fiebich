<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\dcShop\interfaces\TemplatingInterface;
use DynCom\dc\dcShop\interfaces\TextProviderInterface;

/**
 * Class Templating
 */
class Templating implements TemplatingInterface, TextProviderInterface {

    protected $tmpFile;
    protected $error;
    protected $content;
    protected $HTMLSnippetProvider;
    protected $locTextConstants;
    protected $templateDirs = array();

    protected $view;

    /**
     * @param HTMLSnippetProvider $snippetProvider
     * @param array               $locTextConstants
     */
    public function __construct( HTMLSnippetProvider $snippetProvider, array $locTextConstants ) {
        $this->HTMLSnippetProvider = $snippetProvider;
        $this->locTextConstants    = $locTextConstants;
    }

    /**
     * @param $path
     *
     * @return bool
     */
    public function registerTemplateDir( $path ) {
        return $this->_registerTemplateDir($path);
    }

    /**
     * @param $path
     * @return bool
     * @throws \Exception
     */
    protected function _registerTemplateDir( $path ) {
        if (!is_dir($path)) {
            throw new \Exception('Given path is not a valid directory!');
        }
        $this->templateDirs[] = $path;
        return TRUE;
    }

    /**
     * @param $fileName
     *
     * @return bool
     */
    public function includeTemplateFileByName( $fileName ) {
        return $this->_includeTemplateFileByName($fileName);
    }

    /**
     * @param array               $locTextConstants
     */
    public function updateLocTextConstants(array $locTextConstants) {
        $this->locTextConstants    = $locTextConstants;
    }

    /**
     * @param $fileName
     * @return bool
     * @throws \Exception
     */
    protected function _includeTemplateFileByName( $fileName ) {
        if (empty($fileName)) {
            return FALSE;
        }
        if (count($this->templateDirs) == 0) {
            throw new \Exception('No template directories registered!');
        }
        for ($i = (count($this->templateDirs) - 1); $i >= 0; ++$i) {
            $fileRealpath = realpath($this->templateDirs[$i]) . DIRECTORY_SEPARATOR . $fileName;
            if (file_exists($fileRealpath) && is_readable($fileRealpath)) {
                if (isset($this->view)) {
                    $this->view->includeTemplateHere($fileRealpath);
                } else {
                    include($fileRealpath);
                }
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * @param $name
     *
     * @return bool|mixed|null
     */
    public function getText( $name ) {
        return $this->_getText($name);
    }

    /**
     * @param $name
     *
     * @return bool|mixed|null
     */
    protected function _getText( $name ) {
        $value = $this->HTMLSnippetProvider->getText($name);
        if (isset($value)) {
            return $this->HTMLSnippetProvider->getText($name);
        } elseif (isset($this->locTextConstants[$name])) {
            return $this->locTextConstants[$name];
        }
        return $name;
    }

    /**
     * @param $name
     *
     * @return mixed|void
     */
    public function printText( $name ) {
        $this->_printText($name);
    }

    /**
     * @param $name
     *
     * @return bool
     */
    protected function _printText( $name ) {
        $value = $this->HTMLSnippetProvider->getText($name);
        if (isset($value)) {
            $this->HTMLSnippetProvider->printText($name);
        } elseif (isset($this->locTextConstants[$name])) {
            echo $this->locTextConstants[$name];
        }
        return FALSE;
    }

    /**
     * @param $file
     *
     * @return mixed|void
     */
    public function setTemplateFileByPath( $file ) {
        $this->_setTemplateFileByPath($file);
    }

    /**
     * @param $filePath
     *
     * @return bool
     */
    protected function _setTemplateFileByPath( $filePath ) {
        if (!file_exists($filePath)) {
            $error       = 'No template file found!';
            $this->error = $error;
            throw new \Exception($this->error);
            return FALSE;
        } else {
            $this->tmpFile = $filePath;
        }
        $this->content = "";
        if ($this->_readTemplate()) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * @return bool
     */
    protected function _readTemplate() {
        $file = @fopen($this->tmpFile, "r");
        if (!$file) {
            throw new \Exception('Template file does not exist or could not be opened for reading');
            return FALSE;
        } else {
            while (!feof($file)) {
                $temp = fgets($file, 4096);
                $this->content .= $temp;
            }
        }
        return TRUE;
    }

    /**
     * @param $string
     *
     * @return mixed|void
     */
    public function setTemplateString( $string ) {
        $this->_setTemplateString($string);
    }

    /**
     * @param $string
     *
     * @return bool
     */
    protected function _setTemplateString( $string ) {
        $preppedString = (string)$string;
        if (strlen($preppedString) > 0) {
            $this->content = $preppedString;
            return TRUE;
        }
        throw new \Exception('No template string');
        return FALSE;
    }

    /**
     * @param $title
     * @param $value
     *
     * @return mixed|void
     */
    public function replace( $title, $value ) {
        $this->_replace($title, $value);
    }

    /**
     * @param $title
     * @param $value
     */
    protected function _replace( $title, $value ) {
        $this->content = str_replace("{" . $title . "}", $value, $this->content);
    }

    /**
     * @param array $arr
     *
     * @return mixed|void
     */
    public function replaceArray( array $arr ) {
        $this->_replaceArray($arr);
    }

    /**
     * @param array $arr
     */
    protected function _replaceArray( array $arr ) {
        foreach ($arr as $replacement) {
            if (isset($replacement['title']) && isset($replacement['value'])) {
                $title = (string)$replacement['title'];
                $value = (string)$replacement['value'];
                $this->_replace($title, $value);
            }
        }
    }

    /**
     *
     */
    public function printContent() {
        $this->_printContent();
    }

    /**
     *
     */
    protected function _printContent() {
        echo $this->content;
    }

    /**
     *
     */
    public function returnContent() {
        $this->_returnContent();
    }

    /**
     * @return mixed
     */
    protected function _returnContent() {
        return $this->content;
    }

    /**
     *
     */
    public function reset() {
        $this->_reset();
    }

    /**
     *
     */
    protected function _reset() {
        $this->content = NULL;
        $this->error   = NULL;
        $this->tmpFile = NULL;
    }

    /**
     * @param $fileName
     *
     * @return bool
     */
    public function setTemplateFileByName( $fileName ) {
        return $this->_setTemplateFileByName($fileName);
    }

    /**
     * @param $fileName
     *
     * @return bool
     */
    protected function _setTemplateFileByName( $fileName ) {
        if (count($this->templateDirs) == 0) {
            throw new \Exception('No template directories registered!');
            return FALSE;
        }
        for ($i = (count($this->templateDirs) - 1); $i >= 0; --$i) {
            $dir = realpath($this->templateDirs[$i]) . DIRECTORY_SEPARATOR . $fileName;
            if (file_exists($dir)) {
                $this->tmpFile = $dir;
                $this->content = "";
                if ($this->_readTemplate()) {
                    return TRUE;
                }
                return FALSE;
            }
        }
        throw new \Exception('Template File not found');
        return FALSE;
    }

    /**
     *
     */
    public function readTemplate() {
        $this->_readTemplate();
    }

    /**
     *
     */
    public function deleteNonReplaced() {
        $this->_deleteNonReplaced();
    }

    /**
     *
     */
    protected function _deleteNonReplaced() {
        $sanitized     = preg_replace("/\{[^\}]*\}/", "", $this->content);
        $this->content = $sanitized;
    }

    /**
     *
     */
    public function deleteNonReplacedWithInnerWrapper() {
        $this->_deleteNonReplacedWithInnerWrapper();
    }

    /**
     *
     */
    protected function _deleteNonReplacedWithInnerWrapper() {
        $sanitized1    = preg_replace("/(<([A-Za-z][a-zA-Z0-9]*)\b[^>]*>[\r\n\t\s]*)\{[^\}]*\}([\r\n\t\s]*<\/\2>)/", "", $this->content);
        $sanitized2    = preg_replace("/\{[^\}]*\}/", "", $sanitized1);
        $this->content = $sanitized2;
    }

    /**
     *
     */
    public function deleteNonReplacedWithAttrs() {
        $this->_deleteNonReplacedWithAttrs();
    }

    /**
     *
     */
    protected function _deleteNonReplacedWithAttrs() {
        $sanitized     = preg_replace('/(?:<[^>])?([\w\-.:]*\s*=\s*){0,1}([\'"]{0,1}\{[^\}]+?\}[\'"]{0,1}\s*)/', '', $this->content);
        $this->content = $sanitized;
    }

    /**
     * @param $string
     *
     * @return mixed
     */
    public function stringCleanupEmptyAttributes( $string ) {
        return $this->_stringCleanupEmptyAttributes($string);
    }

    /**
     * @param $string
     *
     * @return mixed
     */
    protected function _stringCleanupEmptyAttributes( $string ) {
        //Cleanup unreplaced or empty named attributes, i.e. all patterns like: emptyatTtr1_but-e="]{SOMETHING}" - alternatively with single quotes and/or nothing between quotes
        $sanitized1 = preg_replace('/(\b[a-zA-Z][a-zA-Z_\-0-9]+\=)([\'"])((?:\{[^}]*\}|))(\2)(\s*)/', '', $string);
        //Cleanup unreplaced non-named attributes, i.e. all {SOMETHING}-patterns that are neither within quotation-marks nor between a closing and opening bracket
        $sanitized2 = preg_replace('/(?:>[\r\n\s\t]*\{[^}]*\}[\r\n\s\t]*<)|(?:[\'"]\{[^}]*\}[\'"])|(\{[^}]*\})/', '', $sanitized1);
        return $sanitized2;
    }

    /**
     * @param $string
     *
     * @return mixed
     */
    public function stringDeleteNonReplacedWithAttrs( $string ) {
        return $this->_stringDeleteNonReplacedWithAttrs($string);
    }

    /**
     * @param $string
     *
     * @return mixed
     */
    protected function _stringDeleteNonReplacedWithAttrs( $string ) {
        $sanitized = preg_replace('/(?:<[^>])?([\w\-.:]*\s*=\s*){0,1}(([\'"]){0,1}\{[^\}]+?\}\3{0,1}\s*)/', '', $string);
        return $sanitized;
    }

    /**
     * @param        $snippetName
     * @param array  $replaceArray
     * @param string $cleanupFlag
     *
     * @return mixed|null
     */
    public function prepareHTMLSnippet( $snippetName, array $replaceArray, $cleanupFlag = 'NO_CLEANUP' ) {
        return $this->_prepareHTMLSnippet($snippetName, $replaceArray, $cleanupFlag);
    }

    /**
     * @param $snippetName
     * @param array $replaceArray
     * @param string $cleanupFlag
     * @return mixed|null
     * @throws \Exception
     */
    protected function _prepareHTMLSnippet( $snippetName, array $replaceArray, $cleanupFlag = 'NOCLEANUP' ) {
        $snippet = $this->HTMLSnippetProvider->getText($snippetName);
        if (empty($snippet)) {
            throw new \Exception('HTML Snippet not found');
        }
        if (empty($replaceArray) || !(count($replaceArray) > 0)) {
            throw new \Exception('replaceArray not set or has no content');
        }
        $replacedSnippet = $this->_stringReplaceArray($snippet, $replaceArray);
        $output          = NULL;
        if ($cleanupFlag == 'CLEANUP_ALL') {
            $cleanedSnippet = $this->_stringDeleteNonReplacedWithAttrs($replacedSnippet);
            $output         = $cleanedSnippet;
        } elseif ($cleanupFlag == 'CLEANUP_ATTRS') {
            $cleanedSnippet = $this->_stringCleanupEmptyAttributes($replacedSnippet);
            $output         = $cleanedSnippet;
        } elseif ($cleanupFlag == 'NO_CLEANUP') {
            $output = $replacedSnippet;
        }
        return $output;
    }

    /**
     * @param       $string
     * @param array $arr
     *
     * @return mixed
     */
    protected function _stringReplaceArray( $string, array $arr ) {
        foreach ($arr as $replacement) {
            if (isset($replacement['title']) && isset($replacement['value'])) {
                $title  = (string)$replacement['title'];
                $value  = (string)$replacement['value'];
                $string = str_replace('{' . $title . '}', $value, $string);
            }
        }
        return $string;
    }

    /**
     * @param $class
     */
    protected function _setView( $class ) {
        $this->view = $class;
    }

    /**
     * @param $class
     */
    public function setView( $class ) {
        $this->_setView($class);
    }

    /**
     * @param $price
     * @return string
     */
    public function formatPriceEUR($price) {
        $orig = (float)$price;
        return number_format($orig,2,',','.') . ' €';
    }

    /**
     * @param $price
     * @param null $currencyCode
     * @return string
     */
    public function formatPrice($price, $currencyCode = null) {
        if($currencyCode === '' ||  $currencyCode === 'EUR' || null === $currencyCode) {
            return $this->formatPriceEUR($price);
        } else {
            switch($currencyCode) {
                case 'USD': return '$ ' . number_format((float)$price,2,'.',','); break;
                case 'CHF': return number_format((float)$price,2,',','.'); break;
                default: return $currencyCode . ' ' . number_format((float)$price,2,'.',','); break;
            }
        }
    }

    /**
     * @param $text
     * @param $placeholderName
     * @param $replacementText
     */
    public function replacePlaceholder(&$text, $placeholderName, $replacementText ) {
        if(stripos($text,'%'.$placeholderName.'%') !== -1) {
            $text = str_replace('%'.$placeholderName.'%',$replacementText,$text);
        }
    }
}