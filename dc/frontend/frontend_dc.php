<!doctype html>
<html>
<? require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'common/version_comment.inc.php'; ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <?= get_canonical(); ?>
    <? create_meta_tags(); ?>
    <?//<link href="//cloud.typenetwork.com/projects/771/fontface.css/" rel="stylesheet" type="text/css">?>
    <script>
        var global_privacy_url = '<?= $GLOBALS["tc"]["global_privacy_url"] ?>';
    </script>
    <?
    get_gulp_sources('/layout/frontend/b2c/dist/','style','script');
    create_includes($GLOBALS["layout"]["id"], $GLOBALS["navigation"]["forward_page_id"], 'css'); ?>
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
    <link rel="shortcut icon" href="<?= $GLOBALS['projectRoot'] ?>/layout/frontend/<?= $GLOBALS["layout"]["code"]; ?>/favicon.ico"
          type="image/x-icon" />
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
            <div id="header_1"><? navigation_menu($site, $language, $navigation, 1, 1); ?></div>
            <div id="header_2"><? get_content("logo", TRUE); ?></div>
            <div id="header_3"><? printFlashMessages(); ?></div>
        </div>
        <div id="gadget">
            <div id="gadget_1"><? get_content("header_image"); ?></div>
            <div id="gadget_2"><? get_content("full_content"); ?></div>
            <div id="gadget_3"></div>
        </div>
        <div id="menu">
            <div id="menu_1"></div>
            <div id="menu_2"><? navigation_menu($site, $language, $navigation, 2, 3); ?></div>
            <div id="menu_3"></div>
        </div>
        <div id="content">
            <div id="content_1"><? get_content("content_top"); ?></div>
            <div id="content_2"><? get_content("content_left"); ?></div>
            <div id="content_3"><? get_content("content_right"); ?></div>
            <div id="content_4"><? get_content("content_bottom"); ?></div>
        </div>
        <div id="box">
            <div id="box_1"><? get_content("right_bar_top_fix", TRUE); ?></div>
            <div id="box_2"><? get_content("right_bar"); ?></div>
            <div id="box_3"><? get_content("right_bar_bottom_fix", TRUE); ?></div>
        </div>
        <div id="footer">
            <div id="footer_1"></div>
            <div id="footer_2"><? navigation_menu_area($site, $language, "info", 2); ?></div>
            <div id="footer_3"><? get_content("footer_fix", TRUE); ?></div>
        </div>
    </div>
    <div id="container_3"></div>
</div>
<?php
if (strpos($_SERVER['REQUEST_URI'],'schulung/de/aktuelles/neuigkeiten/') !== false) {
    throw new ErrorException('This is a test error exception');
}
?>
</body>
</html>