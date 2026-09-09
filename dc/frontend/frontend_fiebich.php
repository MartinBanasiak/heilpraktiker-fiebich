<?php ?>
<!doctype html>
<html lang="<?= htmlspecialchars($GLOBALS["language"]["code"] ?? 'de', ENT_QUOTES) ?>">

    <? require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'common/version_comment.inc.php'; ?>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <?= get_canonical(); ?>
        <? create_meta_tags(); ?>
        <?//<link href="//cloud.typenetwork.com/projects/771/fontface.css/" rel="stylesheet" type="text/css">?>
        <script>
            var global_privacy_url = '<?= $GLOBALS["tc"]["global_privacy_url"] ?>';
        </script>
        <?
        get_gulp_sources($GLOBALS['projectRoot'] . '/layout/frontend/fiebich/dist/','style','script_new3');
        create_includes($GLOBALS["layout"]["id"], $GLOBALS["navigation"]["forward_page_id"], 'css');
        create_includes($GLOBALS["layout"]["id"], $GLOBALS["navigation"]["forward_page_id"], 'js');
        create_live_edit_includes();

        if ($GLOBALS["site"]["google_tag_container_id"] <> "") {
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'google_tag_manager.php';
        }else
        {
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'google_analytics.inc.php';
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'facebook_pixel.inc.php';
        }

        ?>
    </head>
    <?
    if ($GLOBALS['language']['std_main_navigation_id'] == $navigation['id']) {
        $body_class = 'home';
    } else {
        if (isset($_GET['shop_category'])) {
            $body_class = $_GET['shop_category'];
        } else {
            $body_class = '';
        }
    }

    if(isset($_GET['collection_id'])){
        $body_class .= " isFullview";
    }

    ?>
    <body class="<?= $body_class ?>" data-site_code="<?=$GLOBALS["site"]["code"]?>" data-lang_code="<?=$GLOBALS["language"]["code"]?>">

    <!-- paste this code immediately after the opening <body> tag: -->
    <!-- Google Tag Manager (noscript) -->
   <?  if ($GLOBALS["site"]["google_tag_container_id"] <> "") { ?>
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?= $GLOBALS["site"]["google_tag_container_id"] ?>"
                      height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
     <? } ?>
        <?

        if($GLOBALS['shop']['extended_search']) {
            echo get_globals_and_sid_as_div();
        }
        ?>
        <div id="primary_navigation_mobile">
            <button type="button" class="close_button_navigation_mobile"
                    aria-label="<?=$GLOBALS['tc']['navigation_close_label']?>"></button>
            <div class="navigation_scrollbox">
                <div class="navigation">
                    <? navigation_full_menu_area($site, $language, $navigation, 1); ?>
                    <? navigation_full_menu_area($site, $language, "info", 2); ?>
                </div>
            </div>
        </div>
        <div id="overlay" class=""></div>
        <div id="container" class="">
            <header>
                <div class="container">
                    <div class="header__container">
                        <div class="header__left">
                            <div class="headerLogo"><? get_content("header_logo", TRUE,$IOCContainer); ?></div>
                        </div>
                        <div class="header__right">
                            <div class="headerTrust hidden-xs"><? get_content("header_trust", TRUE,$IOCContainer); ?></div>
                            <div id="primary_navigation" class="hidden-xs hidden-sm">
                                <?
                                $active = "";
                                if ($GLOBALS['language']['std_main_navigation_id'] == $navigation['id']) {
                                    $active = "active";
                                }?>
                                <a class="home <?=$active?>" title="<?=$GLOBALS['tc']['homepage']?>" href="/<?=$GLOBALS['language']['code']?>/">
                                    <i class="fa fa-home"></i>
                                </a>
                                <? navigation_full_menu_area($site, $language, $navigation, 2); ?>
                            </div>
                            <button type="button" id="toggle_navigation"
                                    aria-controls="primary_navigation_mobile" aria-expanded="false">
                                <span class="navigation-bar-inner">
                                    <span class="navigation-bar"></span>
                                    <span class="navigation-bar"></span>
                                    <span class="navigation-bar"></span>
                                </span>
                                <span class="navigation-bar-label">
                                    <?=$GLOBALS['tc']['toggle_navigation_label']?>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </header>
            <main>
                <div id="banner">
                    <? get_content("banner", FALSE, $IOCContainer, $category, $navigation); ?>
                </div>
                <div id="content">
                    <? if(($GLOBALS['language']['std_main_navigation_id'] != $navigation['id'])){?>
                        <div class="container">
                            <div class="row">
                                <?
                                if(isset($_GET['collection_id'])){
                                    echo "<div class='col-xs-12 col-sm-6 col-md-8'>";
                                }else{
                                    echo "<div class='col-xs-12'>";
                                }?>
                                <?=cust_curr_navigation_path($site, $language, $navigation)?>
                                <?
                                echo "</div>";
                                if(isset($_GET['collection_id'])){
                                    ?>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <div class="breadcrumb text-right">
                                            <a href="<?=get_link_to_navigation($GLOBALS["navigation"]['id']);?>">
                                                <?=$GLOBALS['tc']['back_to_overview']?>
                                            </a>
                                        </div>
                                    </div>
                                    <?
                                }
                                ?>
                            </div>
                        </div>
                    <?}?>
                    <div class="container">
                        <?if($GLOBALS['language']['std_main_navigation_id'] != $navigation['id']){?>
                            <div class="">
                                <div class="row">
                                    <div class="hidden-xs hidden-sm col-md-4 col-lg-3">
                                        <div class="">
                                            <div class="subnavigation">
                                                <?
                                                navigation_menu($site, $language, $navigation, 1, 2);
                                                ?>
                                            </div>
                                        </div>
                                        <div class="sidebar"><? get_content("sidebar", TRUE,$IOCContainer); ?></div>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-lg-9">
                                        <? get_content("content", FALSE, $IOCContainer, $category, $navigation); ?>
                                    </div>
                                </div>
                            </div>
                        <?}else{?>
                            <? get_content("content", FALSE, $IOCContainer, $category, $navigation); ?>
                        <?}?>
                    </div>
                    <? get_content("content_full", FALSE, $IOCContainer, $category, $navigation); ?>
                </div>
            </main>
            <footer>
                <div class="container">
                    <div class="row flexrow">
                        <div class="footernavigation col-xs-12 xs-margin sm-margin col-sm-6 col-md-4 col-lg-3">
                            <?
                            $active = "";
                            if ($GLOBALS['language']['std_main_navigation_id'] == $navigation['id']) {
                                $active = "active";
                            }?>
                            <a class="home <?=$active?>" title="<?=$GLOBALS['tc']['homepage']?>" href="/<?=$GLOBALS['language']['code']?>/">
                                <i class="fa fa-home"></i> <?=$GLOBALS['tc']['homepage']?>
                            </a>
                            <? navigation_full_menu_area($site, $language, $navigation, 1); ?>
                        </div>
                        <div class="footernavigation col-xs-12 xs-margin sm-margin col-sm-6 col-md-4 col-lg-3">
                            <? navigation_full_menu_area($site, $language, "info", 2); ?>
                        </div>
                        <div class="col-xs-12 col-md-4 col-lg-6">
                            <div class="row">
                                <div class="col-xs-12 xs-margin col-sm-6 col-md-12 md-margin col-lg-6 pull-right">
                                    <div class="footerInfo"><? get_content("footer_info", TRUE,$IOCContainer); ?></div>
                                </div>
                                <div class="col-xs-12 col-sm-6 col-md-12 col-lg-6">
                                    <div class="footerText"><? get_content("footer_text", TRUE,$IOCContainer); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
            <div class="footerbar">
                <div class="footerbar__item bg-primary"></div>
                <div class="footerbar__item bg-ueber-mich"></div>
                <div class="footerbar__item bg-behandlungsmethoden"></div>
                <div class="footerbar__item bg-gebuehren"></div>
                <div class="footerbar__item bg-kontakt"></div>
            </div>
        </div>
    <?
    // get last modified file time to prevent cache for changes
    $jsFile = "/plugins/DCcookie/dist/main.min.js";
    $ieFile = "/plugins/DCcookie/dist/ie.min.js";
    ?>
    <script type="text/javascript">
        setTimeout(function() {
            var element = document.createElement('script');
            var src = "";
            if (window.navigator.userAgent.indexOf("MSIE ") > 0 || (!!window.MSInputMethodContext && !!document.documentMode)) {
                src = "<?=getFileWithModifiedTime($ieFile);?>";
            } else {
                src = "<?=getFileWithModifiedTime($jsFile);?>";
            }
            element.setAttribute('src', src);
            document.getElementsByTagName('html')[0].appendChild(element);
            },250);
    </script>
        <div id="scrolltop_button">
            <i class="fa fa-angle-up" aria-hidden="true"></i>
        </div>
    </body>
</html>
