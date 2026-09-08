<?php
namespace DynCom\dc\dcShop\interfaces;

/**
 * Interface TemplatingInterface
 */
interface TemplatingInterface {

    /**
     * @param $filePath
     *
     * @return mixed
     */
    public function setTemplateFileByPath( $filePath );

    /**
     * @param $dir
     *
     * @return mixed
     */
    public function registerTemplateDir( $dir );

    /**
     * @param $fileName
     *
     * @return mixed
     */
    public function setTemplateFileByName( $fileName );

    /**
     * @param $fileName
     *
     * @return mixed
     */
    public function includeTemplateFileByName( $fileName );

    /**
     * @param $text
     *
     * @return mixed
     */
    public function setTemplateString( $text );

    /**
     * @param $title
     * @param $value
     *
     * @return mixed
     */
    public function replace( $title, $value );

    /**
     * @param array $array
     *
     * @return mixed
     */
    public function replaceArray( array $array ); //array(0 => array('title' => title, 'value' => value), 1 => array('title' => title, 'value' => value))

    public function printContent();

    public function returnContent();

    /**
     * @param        $snippetName
     * @param array  $replaceArray
     * @param string $cleanupFlag
     *
     * @return mixed
     */
    public function prepareHTMLSnippet( $snippetName, array $replaceArray, $cleanupFlag = 'NOCLEANUP' );

    /**
     * @param $name
     *
     * @return mixed
     */
    public function getText( $name );

    public function reset();

    /**
     * @param $text
     * @param $placeholderName
     * @param $replacement
     * @return mixed
     */
    public function replacePlaceholder(&$text, $placeholderName, $replacement );
}