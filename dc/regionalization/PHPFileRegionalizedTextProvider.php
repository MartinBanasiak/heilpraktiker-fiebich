<?php
namespace DynCom\dc\regionalization;
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 13.10.2016
 * Time: 22:17
 */
class PHPFileRegionalizedTextProvider implements RegionalizedTextProvider
{

    /**
     * @var array
     */
    protected $textsArray;

    public function __construct($locale) {
        $localeCode = substr($locale,0,strpos($locale,'-'));
        $filePath = rtrim(__DIR__,'/') . '/resources/localized_strings.' . $localeCode . '.php';
        if (file_exists($filePath) && is_readable($filePath) && is_file($filePath)) {
            $this->textsArray = include($filePath);
        } else {
            $filePath = rtrim(__DIR__,'/') . '/resources/localized_strings.en.php';
            $this->textsArray = include($filePath);
        }
    }

    public function getRegionalizedText($name, $default = null)
    {
        return array_key_exists($name,$this->textsArray) ? $this->textsArray[$name] : $default ?: $name;
    }


}