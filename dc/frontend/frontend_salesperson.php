<? require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR . 'dcshop' . DIRECTORY_SEPARATOR . 'shop_start.inc.php';
finalize_meta_tags();
?>
<!doctype html>
<html>
<? require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'version_comment.inc.php'; ?>
<head>
    <title><?= $GLOBALS['site_title'] ?></title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=1170" />
    <? create_includes($GLOBALS["layout"]["id"], $GLOBALS["navigation"]["forward_page_id"], 'css'); ?>
    <? create_includes($GLOBALS["layout"]["id"], $GLOBALS["navigation"]["forward_page_id"], 'js'); ?>
    <? create_live_edit_includes(); ?>
    <?
    if ($GLOBALS["site"]["google_tag_container_id"] <> "") {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'google_tag_manager.php';
    }else
    {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'google_analytics.inc.php';
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'facebook_pixel.inc.php';
    }

    ?>
    <meta name="Keywords" content="<?= $GLOBALS['meta_keywords'] ?>>" />
    <meta name="Description" content="<?= $GLOBALS['meta_description'] ?>" />
    <meta name="Content-language" content="<?= $language["code"] ?>" />
    <meta name="Robots" content="INDEX,FOLLOW" />
    <link rel="shortcut icon" href="<?= $GLOBALS['projectRoot'] ?>/layout/frontend/<?= $GLOBALS["layout"]["code"]; ?>/favicon.ico"
          type="image/x-icon" />
</head>
<?
if (strlen($_GET["shop_category"]) > 0) {
    $bodyclass = $_GET["shop_category"];
}
if ($_GET["action"] == "payment") {
    $bodyclass .= " " . $_GET["action"];
}
?>
<body class="<?= $bodyclass ?>">
<?  if ($GLOBALS["site"]["google_tag_container_id"] <> "") { ?>
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?= $GLOBALS["site"]["google_tag_container_id"] ?>"
                      height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
<? } ?>
<script type="text/javascript" language="JavaScript" src="<?= $GLOBALS['projectRoot'] ?>/plugins/tooltip/tooltip.js"></script>
<div id="spacer_1">
    <div id="constants" style="display: none;">
        <div id="company" data-company="<?= $GLOBALS["shop"]["company"] ?>"></div>
        <div id="shop_code" data-shop-code="<?= $GLOBALS["shop"]["code"] ?>"></div>
        <div id="lang_code" data-lang-code="<?= $GLOBALS["shop_language"]["code"] ?>"></div>
        <div id="item_src" data-item-src="<?= $GLOBALS["shop"]["item_source"] ?>"></div>
    </div>
</div>
<div id="spacer_2"></div>
<div id="spacer_3"></div>
<div id="container">
    <div id="container_1"></div>
    <div id="container_2">
        <div id="header">
            <div id="header_1"><? get_content("header_logo", TRUE); ?></div>
            <div id="header_2"><? get_content("header_user_area"); ?></div>
            <div id="header_3"><? get_content("header_basket"); ?></div>
        </div>
        <div id="gadget">
            <div id="gadget_1"><? get_content("header_search"); ?><? get_content("header_direct_order"); ?></div>
            <div id="gadget_2"></div>
            <div id="gadget_3"></div>
        </div>
        <div id="menu">
            <div id="menu_1"><? navigation_full_menu_shop($site, $language, $navigation, 1, 2); ?></div>
            <div id="menu_2"></div>
            <div id="menu_3"></div>
        </div>
        <div id="content">
            <div id="content_1"></div>
            <div id="content_2"></div>
            <div id="content_3">
                <div id="content_3_top"><?
                    if (!isset($_GET['slevel_1']) && $_GET['shop_category'] != 'account' && $_GET['shop_category'] != 'basket') {
                        echo cust_curr_navigation_path($site, $language, $navigation);
                    }
                    get_content("content_main", $navigation["id"]);
                    ?></div>
            </div>
        </div>
        <div id="box">
            <div id="box_1"></div>
            <div id="box_2"></div>
            <div id="box_3"></div>
        </div>
        <div id="footer">
            <div id="footer_1"><? navigation_menu_area($site, $language, "info", 2); ?></div>
            <div id="footer_2"><? get_content("footer_text"); ?></div>
            <div id="footer_3"></div>
        </div>
    </div>
    <div id="container_3"></div>
</div>
<?php require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR . 'dcshop' . DIRECTORY_SEPARATOR . 'b2b' . DIRECTORY_SEPARATOR . 'tooltips.inc.php'; ?>
</body>

</html>
