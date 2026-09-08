<?php
if(!isset($GLOBALS['admin_user']))
{
    headerFunctionBridge("Location: https://".$_SERVER['SERVER_NAME']."/404.html");
    die();
}
$messages = array();
?>

<style type="text/css">
    /*
    #phpinfo { width:800px; }
    #phpinfo pre {margin: 0px; font-family: monospace;}
    #phpinfo a:link {color: #000099; text-decoration: none; background-color: #ffffff;}
    #phpinfo a:hover {text-decoration: underline;}
    #phpinfo table {border-collapse: collapse;}
    #phpinfo .center {text-align: left;}
    #phpinfo .center table {  text-align: left;}
    #phpinfo .center th { text-align: center !important; }

    #phpinfo h1 {font-size: 150%;}
    #phpinfo h2 {font-size: 125%;}
    #phpinfo .p {text-align: left;}


     #phpinfo .v {background-color: #cccccc; color: #000000;}
    #phpinfo .vr {background-color: #cccccc; text-align: right; color: #000000;}
    #phpinfo img {float: right; border: 0px;}
    #phpinfo hr {width: 100%; background-color: #cccccc; border: 0px; height: 1px; color: #000000;}
    */

    #phpinfo td {
        background-color : #e0e0e0;
    }

    #phpinfo .e {
        background-color : #e0e0e0;
        font-weight      : bold;
        color            : #000000;
        width            : 150px;
    }

    #phpinfo .h {
        background-color : #9999cc;
        font-weight      : bold;
        color            : #000000;
    }

    #phpinfo .v {
        background-color : #fff;
    }

    #phpinfo td, #phpinfo th {
        border         : 1px solid #DEDEDE;
        vertical-align : baseline;
        max-width      : 190px;
        white-space    : pre-wrap; /* css-3 */
        white-space    : -moz-pre-wrap; /* Mozilla, since 1999 */
        white-space    : -pre-wrap; /* Opera 4-6 */
        white-space    : -o-pre-wrap; /* Opera 7 */
        word-wrap      : break-word; /* Internet Explorer 5.5+ */
    }

    #phpinfo table {
        width : 800px;
    }

    #phpinfo th {
        background-color : #99c137;
    }

    #phpinfo hr {
        float : left;
        width : 600px;
    }

    #phpinfo hr:after {
        content    : ".";
        display    : block;
        height     : 0;
        clear      : both;
        visibility : hidden;
    }

    #phpinfo h1 {
        float : left;
        width : 100%;
    }
</style>

<div id="mainContent">
    <?php //echo current_website_language($site, $language); ?>
    <h1>Systeminfo</h1>

    <?php
    ob_start();
    phpinfo();
    $phpinfo = ob_get_clean();

    // Body-Content rausholen
    $phpinfo = preg_replace('#^.*<body>(.*)</body>.*$#s', '$1', $phpinfo);

    // XHTML-Fehler korrigieren
    $phpinfo = str_replace('module_Zend Optimizer', 'module_Zend_Optimizer', $phpinfo);
    # <font> durch <span> ersetzen
    $phpinfo = str_replace('<font', '<span', $phpinfo);
    $phpinfo = str_replace('</font>', '</span>', $phpinfo);

    // Schlüsselwörter grün oder rot einfärben
    $phpinfo = preg_replace('#>(on|enabled|active)#i', '><span style="color:#090">$1</span>', $phpinfo);
    $phpinfo = preg_replace('#>(off|disabled)#i', '><span style="color:#f00">$1</span>', $phpinfo);

    echo '<div id="phpinfo">';
    echo $phpinfo;
    echo '</div>';

    ?>
</div>