<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<? require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'version_comment.inc.php'; ?>

<?
/*
***Kopfbereich***
CSS-Dateien und JS-Dateien werden dynamisch geladen
Kommentare in Produktivumgebung entfernen
*/
?>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?= site_title($language, $navigation) ?></title>
    <?

    if ($GLOBALS["site"]["google_tag_container_id"] <> "") {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'google_tag_manager.php';
    }else
    {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'google_analytics.inc.php';
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'facebook_pixel.inc.php';
    }

    ?>
    <meta name="Keywords" content="<?= $navigation["meta_keywords"] . "," . $language["meta_keywords"] ?>" />
    <meta name="Description" content="<?= $navigation["meta_description"] . " " . $language["meta_description"] ?>" />
    <meta name="Content-language" content="<?= $language["code"] ?>" />
    <meta name="Robots" content="INDEX,FOLLOW" />
    <link rel="shortcut icon" href="<?= $GLOBALS['projectRoot'] ?>/<?= $GLOBALS["layout"]["favicon_include"]; ?>" type="image/x-icon" />
    <?
    create_includes($GLOBALS["layout"]["id"], $GLOBALS["navigation"]["id"], 'css');
    create_includes($GLOBALS["layout"]["id"], $GLOBALS["navigation"]["id"], 'js');
    ?>

    <?
    /*
    ***Inhaltsbereich***
    Die verschiedenen Menübäume und Inhaltsbereiche für unterschiedliche Layoutareas können hier frei in den Containern positioniert werden
    navigation_menu_area($site,$language,"Textschlüssel Root-Navigation",Aufklappen bis Max Level) Ereugt Menübaum ab bestimmter Root-Navigation
    navigation_menu($site,$language,"Textschlüssel Root-Navigation",Aufklappen bis Max Level) Ereugt Menübaum ab bestimmter Root-Navigation
    navigation_full_menu_area($site,$language,"Textschlüssel Root-Navigation",Aufklappen bis Max Level) Ereugt Menübaum ab bestimmter Root-Navigation
    navigation_full_menu($site,$language,"Textschlüssel Root-Navigation",Aufklappen bis Max Level) Ereugt Menübaum ab bestimmter Root-Navigation
    get_content("Textschlüssel Layoutarea", $navigation["id"]): Läd Inahlt der aktuellen Navigation aus einer Layout-Area
    get_content("Textschlüssel Layoutarea"): Läd fixen Inhalt aus einer Layout-Area
    $GLOBALS["tc"]["Name Textkonstante"]: Hier können Texte aus den Textkonstanten geladen werden
    */
    ?>

</head>
<body>
<?  if ($GLOBALS["site"]["google_tag_container_id"] <> "") { ?>
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?= $GLOBALS["site"]["google_tag_container_id"] ?>"
                      height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
<? } ?>
<div id="spacer_1"></div>
<div id="spacer_2"></div>
<div id="spacer_3"></div>
<div id="container">
    <div id="container_1"></div>
    <div id="container_2">
        <div id="header">
            <div id="header_1"><? navigation_menu_area($site, $language, "dc", 3); ?></div>
            <div id="header_2"></div>
            <div id="header_3"></div>
        </div>
        <div id="gadget">
            <div id="gadget_1"><? get_content("header_image", $navigation["id"]); ?></div>
            <div id="gadget_2"><? get_content("header_content", $navigation["id"]); ?></div>
            <div id="gadget_3"></div>
        </div>
        <div id="menu">
            <div id="menu_1"></div>
            <div id="menu_2"><? navigation_menu($site, $language, $navigation, 3, 5); ?></div>
            <div id="menu_3"></div>
        </div>
        <div id="content">
            <div id="content_1"><? get_content("content_top", $navigation["id"]); ?></div>
            <div id="content_2"><? get_content("content_left", $navigation["id"]); ?></div>
            <div id="content_3"><? get_content("content_right", $navigation["id"]); ?></div>
            <div id="content_4"><? get_content("content_bottom", $navigation["id"]); ?></div>
        </div>
        <div id="box">
            <div id="box_1"><? get_content("right_bar_top_fix"); ?></div>
            <div id="box_2"><? get_content("right_bar", $navigation["id"]); ?></div>
            <div id="box_3"><? get_content("right_bar_bottom_fix"); ?></div>
        </div>
        <div id="footer">
            <div id="footer_0"><? get_content("footer", $navigation["id"]); ?></div>
            <div id="footer_1">
                <div class="search_tags_headline"><?= $GLOBALS["tc"]["tag_cloud"]; ?></div><? get_search_tags(); ?>
                <div class="clearfloat"></div>
            </div>
            <div id="footer_2"><? navigation_menu_area($site, $language, "menu_footer", 2); ?></div>
            <div id="footer_3"><? get_content("footer_fix"); ?></div>
        </div>
    </div>
    <div id="container_3"></div>
</div>
</body>
</html>
<? require_once __DIR__ . DIRECTORY_SEPARATOR . 'close.inc.php'; ?>