<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\dcShop\interfaces\TextProviderInterface;

/**
 * Class HTMLSnippetProvider
 */
class HTMLSnippetProvider implements TextProviderInterface {

    const GENERICDIV      = '
		<div class="{CLASSNAME}" id="{ID}" {VARATTRIBUTES}>{INNERHTML}</div>';
    const GENERICSPAN     = '<span class="{CLASSNAME}" id="{ID}" {VARATTRIBUTES}>{INNERHTML}</span>';
    const GENERICANCHOR   = '<a href="{HREF}" id="{ID} class="{CLASSNAME}" alt="{ALTTEXT}" {VARATTRIBUTES}>{INNERHTML}</a>';
    const GENERICFORM     = '
		<form class="{CLASSNAME}" id="{ID}" method="{METHOD}" action="{ACTION}" {VARATTRIBUTES}>{INNERHTML}</form>';
    const GENERICTABLE    = '
		<table class="{CLASSNAME}" id="{ID}" width="{WIDTH}" height="{HEIGHT}" cellpadding="{CELLPADDING}" cellspacing="{CELLSPACING}" align="{ALIGN}" valign="{VALIGN}" {VARATTRIBUTES}>{INNERHTML}</table>';
    const GENERICTHEAD    = '
		<thead>{INNERHTML}</thead>';
    const GENERICTBODY    = '
		<tbody>{INNERHTML}</tbody>';
    const GENERICTFOOT    = '
		<tfoot>{INNERHTML}</tfoot>';
    const GENERICTABLEROW = '
		<tr class="{CLASSNAME}" id="{ID}" width="{WIDTH}" height="{HEIGHT}" {VARATTRIBUTES}>{INNERHTML}</tr>';
    const GENERICTH       = '<th class="{CLASSNAME}" id="{ID}" colspan="{COLSPAN}" rowspan="{ROWSPAN}" align="{ALIGN}" valign="{VALIGN}" width="{WIDTH}" height="{HEIGHT}" {VARATTRIBUTES}>{INNERHTML}</th>';
    const GENERICTD       = '<td class="{CLASSNAME}" id="{ID}" colspan="{COLSPAN}" rowspan="{ROWSPAN}" align="{ALIGN}" valign="{VALIGN}" width="{WIDTH}" height="{HEIGHT}" {VARATTRIBUTES}>{INNERHTML}</td>';
    const GENERICINPUT    = '<input name="{INPUTNAME}" type="{TYPE}" value="{VALUE}" placeholder="{PLACEHOLDER}" class="{CLASSNAME}" id="{ID}" {CHECKED} {DISABLED} {VARATTRIBUTES} />';
    const GENERICSELECT   = '
		<select name="{INPUTNAME}" class="{CLASSNAME}" id="{ID}" {MULTIPLE} {DISABLED} {VARATTRIBUTES}>{INNERHTML}</select>';
    const GENERICOPTGROUP = '
		<optgroup label="{LABEL}">{INNERHTML}</optgroup>';
    const GENERICOPTION   = '<option value="{VALUE}" {DISABLED} {SELECTED} {VARATTRIBUTES}>{NAME}</option>';
    const GENERICTEXTAREA = '
		<textarea rows="{ROWS}" cols="{COLS}" width="{WIDTH}" height="{HEIGHT}" {DISABLED} {VARATTRIBUTES}>{TEXT}</textarea>';
    const LABELEDINPUT    = '
		<div class="label">
			<label for="{INPUTNAME}">{NAME}</label>
		</div>
		<div class="input">
			<input name="{INPUTNAME}" type="{TYPE}" value="{VALUE}" class="{CLASSNAME}" id="{ID}" {VARATTRIBUTES} />
		</div>';
    const LABELEDSELECT   = '
		<div class="label">
			<label for="{INPUTNAME}">{NAME}</label>
		</div>
		<div class="input">
			<select name="{INPUTNAME}" class="{CLASSNAME}" id="{ID}" {MULTIPLE} {DISABLED} {VARATTRIBUTES}>{INNERHTML}</select>
		</div>';
    const LABALEDTEXTAREA = '
		<div class="label">
			<label for="{INPUTNAME}">{NAME}</label>
		</div>
		<div class="input">
			<textarea rows="{ROWS}" cols="{COLS}" width="{WIDTH}" height="{HEIGHT}" {DISABLED} {VARATTRIBUTES}>{VALUE}</textarea>
		</div>';
    const ERRORBOX        = '
		<div class="errorbox {ADDEDCLASSNAMES}" id="{ID}">{INNERHTML}</div>';
    const SUCCESSBOX      = '
		<div class="successbox {ADDEDCLASSNAMES}" id="{ID}">{INNERHTML}</div>';
    const NOTICEBOX       = '
		<div class="noticebox {ADDEDCLASSNAMES}" id="{ID}">{INNERHTML}</div>';
    const ERRORTEXT       = '
		<div class="errortext {ADDEDCLASSNAMES}" id="{ID}">{INNERHTML}</div>';
    const SUCCESSTEXT     = '
		<div class="successtext {ADDEDCLASSNAMES}" id="{ID}">{INNERHTML}</div>';
    const NOTICETEXT      = '
		<div class="noticetext {ADDEDCLASSNAMES}" id="{ID}">{INNERHTML}</div>';

    /**
     * @param $name
     *
     * @return mixed|null
     */
    public function getConstant( $name ) {
        return $this->_getConstant($name);
    }

    /**
     * @param $name
     *
     * @return mixed|null
     */
    protected function _getConstant( $name ) {
        if (defined('self::' . $name)) {
            return constant('self::' . $name);
        }
        return NULL;
    }

    /**
     * @param $name
     */
    public function printConstant( $name ) {
        $this->_printConstant($name);
    }

    /**
     * @param $name
     */
    protected function _printConstant( $name ) {
        if (defined('self::' . $name)) {
            echo constant($name);
        }
    }

    /**
     * @param $name
     *
     * @return mixed|null
     */
    public function getText( $name ) {
        return $this->_getConstant($name);
    }

    /**
     * @param $name
     *
     * @return mixed|void
     */
    public function printText( $name ) {
        $this->_printConstant($name);
    }

    /**
     * @param        $inner
     * @param string $id
     * @param string $className
     *
     * @return string
     */
    public function wrapInDiv( $inner, $id = '', $className = '' ) {
        $output = "<div";
        if(preg_match("/[a-zA-Z0-9_\-]+/",$id)) {
            $output .= " id=\"" . $id . "\" ";
        }
        if(preg_match("/[a-zA-Z0-9_\-]+/",$className)) {
            $output .= " class=\"" . $className . "\" ";
        }
        $output .= ">" . $inner . "</div>";
        return $output;
    }

    /**
     * @param        $inner
     * @param string $id
     * @param string $className
     *
     * @return string
     */
    public function wrapInSpan( $inner, $id = '', $className = '' ) {
        $output = "<span";
        if(preg_match("/[a-zA-Z0-9_\-]+/",$id)) {
            $output .= " id=\"" . $id . "\" ";
        }
        if(preg_match("/[a-zA-Z0-9_\-]+/",$className)) {
            $output .= " class=\"" . $className . "\" ";
        }
        $output .= ">" . $inner . "</span>";
        return $output;
    }

    /**
     * @param        $inner
     * @param string $id
     * @param string $className
     *
     * @return string
     */
    public function wrapInUL( $inner, $id = '', $className = '' ) {
        $output = "<ul";
        if(preg_match("/[a-zA-Z0-9_\-]+/",$id)) {
            $output .= " id=\"" . $id . "\" ";
        }
        if(preg_match("/[a-zA-Z0-9_\-]+/",$className)) {
            $output .= " class=\"" . $className . "\" ";
        }
        $output .= ">" . $inner . "</ul>";
        return $output;
    }

    /**
     * @param        $inner
     * @param string $id
     * @param string $className
     *
     * @return string
     */
    public function wrapInLI( $inner, $id = '', $className = '' ) {
        $output = "<li";
        if(preg_match("/[a-zA-Z0-9_\-]+/",$id)) {
            $output .= " id=\"" . $id . "\" ";
        }
        if(preg_match("/[a-zA-Z0-9_\-]+/",$className)) {
            $output .= " class=\"" . $className . "\" ";
        }
        $output .= ">" . $inner . "</li>";
        return $output;
    }

    /**
     * @param        $href
     * @param        $inner
     * @param string $id
     * @param string $className
     *
     * @return string
     */
    public function wrapInAnchor( $href, $inner, $id = '', $className = '' ) {
        if(!filter_var($href,FILTER_VALIDATE_URL)) {
            return '';
        }
        $output = "<a href=\"" . $href . "\"";
        if(preg_match("/[a-zA-Z0-9_\-]+/",$id)) {
            $output .= " id=\"" . $id . "\" ";
        }
        if(preg_match("/[a-zA-Z0-9_\-]+/",$className)) {
            $output .= " class=\"" . $className . "\" ";
        }
        $output .= ">" . $inner . "</a>";
        return $output;
    }

}
