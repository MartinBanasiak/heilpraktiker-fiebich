<?php
use DynCom\dc\common\interfaces\SessionFlashMessageBag;

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'module/dcshop/shop_start.inc.php'; ?>
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
        get_gulp_sources($GLOBALS['projectRoot'] . '/layout/frontend/b2c/dist/','style','script');
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

    if (null !== $category && !empty($category["in_use_with_page_id"])) {
        $body_class .= " shoppingworld";
    }

    ?>
    <body class="<?= $body_class ?>">

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
            <a class="close_button_navigation_mobile"><?=$GLOBALS['tc']['toggle_navigation_label']?></a>
            <div class="navigation_scrollbox">
                <div class="navigation">
                    <? navigation_full_menu_shop($site, $language, $navigation, 1, 3) ?>
                </div>
                <div class="visible-xs navigation navigation_cms">
                    <? navigation_full_menu_area($site, $language, "info", 3); ?>
                </div>
                <div class="visible-xs navigation navigation_cms">
                    <? navigation_menu_area($site, $language, "legal", 3); ?>
                </div>
            </div>
        </div>
        <div id="overlay" class=""></div>
        <div id="container" class="">
            <div id="container_1">
                <div id="header_account" class="hidden-xs">
                    <div class="container"><?get_content("header_account", TRUE,$IOCContainer);?></div>
                </div>
                <div id="header">
                    <div class="container">
                        <div id="toggle_navigation" class="visible-xs visible-sm">
                            <div class="navigation-bar"></div>
                            <div class="navigation-bar"></div>
                            <div class="navigation-bar"></div>
                        </div>
                        <div id="header_1"><? get_content("header_logo", TRUE,$IOCContainer); ?></div>
                        <div id="header_2" class="hidden-xs hidden-sm">
                            <div
                                id="primary_navigation"><? navigation_full_menu_shop($site, $language, $navigation, 1, 3) ?></div>
                        </div>
                        <div id="header_3"><? get_content("header_basket", TRUE,$IOCContainer); ?></div>
                        <div id="header_4"><? get_content("header_search", TRUE,$IOCContainer); ?></div>
                        <div id="header_shop_icons" class="visible-xs">
                            <div class="header_shop_icon header_icon_favorites">
                                <a href="/<? echo customizeUrl();?>/favorites/">
                                    <i class="fa fa-heart" aria-hidden="true"></i>
                                </a>
                            </div>
                            <div class="header_shop_icon header_icon_account">
                                <? IF (!($GLOBALS["visitor"]["frontend_login"])) { ?>
                                <a href="/<? echo customizeUrl();?>/fix/login/">
                                <?}else{?>
                                <a href="/<? echo customizeUrl();?>/account/">
                                <?}?>
                                    <i class="fa fa-user" aria-hidden="true"></i>
                                </a>
                            </div>
                            <div class="header_shop_icon header_icon_search">
                                <i class="fa fa-search" aria-hidden="true"></i>
                            </div>
                        </div>
                        <div class="clearfloat"></div>
                    </div>
                </div>
                <div id="gadget" class="hidden-xs hidden-sm">
                    <div class="container">
                        <div id="gadget_1">
                            <?get_content("header_trust", TRUE,$IOCContainer); ?>
                        </div>
                        <div id="gadget_2">
                            <? if ($_GET['level_1'] === "shop"){
                                get_shop_breadcrumb();
                            }else if(($GLOBALS['language']['std_main_navigation_id'] != $navigation['id'])){?>
                                <div class="row">
                                    <div class="main_content_right col-xs-12 col-sm-12 col-md-12 col-lg-9">
                                        <?=cust_curr_navigation_path($site, $language, $navigation)?>
                                    </div>
                                </div>
                            <?}?>
                        </div>
                        <div id="gadget_3"></div>
                    </div>
                </div>
                <div id="banner">
                        <? get_content("banner", FALSE, $IOCContainer, $category, $navigation); ?>
                </div>
                <div id="content">
                    <div class="container">
                        <div id="content_1">
                            <? get_content("content", FALSE, $IOCContainer, $category, $navigation); ?>
                        </div>
                        <?if($_GET['level_1'] === "shop" && (isset($_GET['card']) || (isset($_GET['shop_category']) && $_GET['shop_category'] !== 'dc_order'))) {?>
                            <div id="content_3" class="main_content_full">
                                <? get_content("content_right", FALSE, $IOCContainer, $category, $navigation); ?>
                            </div>
                        <?}else{?>
                            <div class="row">
                                <?
                                $content_3_classes = "main_content_full col-xs-12 col-sm-12 col-md-12";
                                if (!$navigation["is_landing_page"]) {
                                    $content_3_classes = "main_content_right col-xs-12 col-sm-12 col-md-9";
                                ?>
                                <div id="content_2" class="main_content_left hidden-xs hidden-sm col-md-3">
                                    <div id="subnavigation">

                                        <?
                                        if ($GLOBALS["category"]["hide_category_sub_navigation"] == 0) {
                                            if($_GET['level_1'] === "shop") {
                                                navigation_menu_shop($site, $language, $navigation, 2, 3);
                                            }elseif($GLOBALS['language']['std_main_navigation_id'] != $navigation['id']) {
                                                navigation_menu($site, $language, $navigation, 2, 3);
                                            }
                                        }?>
                                    </div>
                                    <? get_content("content_left", FALSE, $IOCContainer, $category, $navigation); ?>
                                </div>
                                <?
                                }
                                ?>
                                <div id="content_3" class="<?= $content_3_classes ?>">
                                    <? get_content("content_right", FALSE, $IOCContainer, $category, $navigation); ?>
                                </div>
                            </div>
                        <?
                        }
                        ?>
                    </div>
                </div>
                <div id="specialboxes">
                    <div class="container">
                        <div class="row">
                            <div id="special_box_1" class="col-xs-12 special_box">
                                <? get_content("special_box_full",null,$IOCContainer); ?>
                            </div>
                            <div id="special_box_2" class="col-xs-12 col-sm-12 col-md-4 special_box">
                                <? get_content("special_box_1",null,$IOCContainer); ?>
                            </div>
                            <div id="special_box_3" class="col-xs-12 col-sm-12 col-md-4 special_box">
                                <? get_content("special_box_2",null,$IOCContainer); ?>
                            </div>
                            <div id="special_box_4" class="col-xs-12 col-sm-12 col-md-4 special_box">
                                <? get_content("special_box_3",null,$IOCContainer); ?>
                            </div>
                            <div id="special_box_5" class="col-xs-12 special_box">
                                <? get_content("special_box_full_2",null,$IOCContainer); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="box" class="hidden-xs hidden-sm">
                    <div class="box-icons">
                        <div class="box-icon">
                            <i class="fa fa-phone" aria-hidden="true"></i>
                        </div>
                        <div class="box-icon">
                            <i class="fa fa-envelope" aria-hidden="true"></i>
                        </div>
                        <div class="box-icon">
                            <i class="fa fa-share-square" aria-hidden="true"></i>
                        </div>
                        <div class="box-icon">
                            <i class="fa fa-question-circle" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="box-content">
                        <? get_content("contactbox", TRUE,$IOCContainer); ?>
                    </div>
                </div>
                <div id="footer">
                    <div id="footer_seotext">
                        <div class="container">
                            <? get_content("footer_seotext", TRUE,$IOCContainer); ?>
                        </div>
                    </div>
                    <div id="footer_1">
                        <div class="container">
                            <? get_content("footer_banner", TRUE,$IOCContainer); ?>
                        </div>
                    </div>
                    <div id="footer_2">
                        <div class="container">
                            <div class="row">
                                <div id="footer_navigation" class="footer_info col-xs-12 col-sm-12 col-md-6"><? navigation_full_menu_area($site, $language, "info", 3); ?></div>
                                <div class="footer_payment col-xs-12 col-sm-12 col-md-6"><? get_content("footer_payment", TRUE,$IOCContainer); ?></div>
                                <div class="footer_shipping col-xs-12 col-sm-12 col-md-6"><? get_content("footer_shipping", TRUE,$IOCContainer); ?></div>
                                <div class="footer_socialmedia col-xs-12 col-sm-12 col-md-6"><? get_content("footer_socialmedia", TRUE,$IOCContainer); ?></div>
                            </div>
                        </div>
                    </div>
                    <div id="footer_3">
                        <div class="container">
                            <div class="row">
                                <div id="footer_navigation_legal" class="col-xs-12 col-sm-12 col-md-6 col-lg-7 col-xlg-8 pull-right">
                                    <? navigation_menu_area($site, $language, "legal", 3); ?>
                                </div>
                                <div class="footer_copyright col-xs-12 col-sm-12 col-md-6 col-lg-5 col-xlg-4">
                                    <? get_content("footer_copyright", TRUE,$IOCContainer); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="scrolltopbutton_wrapper">
            <div class="container">
                <div id="scrolltop_button"><i class="fa fa-angle-up" aria-hidden="true"></i>
                </div>
            </div>
        </div>
        <?php require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR . 'dcshop' . DIRECTORY_SEPARATOR . 'shop_end.inc.php' ;

        //Get Flash Messages
        /**
         * @var $flashMessageBag SessionFlashMessageBag
         */
        $flashMessageBag = $GLOBALS['flashMessageBag'];

        $info_flashmsgs = $flashMessageBag->get(SessionFlashMessageBag::TYPE_INFO);
        $notice_flashmsgs = $flashMessageBag->get(SessionFlashMessageBag::TYPE_NOTICE);
        $warning_flashmsgs = $flashMessageBag->get(SessionFlashMessageBag::TYPE_WARNING);
        $error_flashmsgs = $flashMessageBag->get(SessionFlashMessageBag::TYPE_ERROR);
        $success_flashmsgs = $flashMessageBag->get(SessionFlashMessageBag::TYPE_SUCCESS);
        //$flashMessageBag->set(SessionFlashMessageBag::TYPE_INFO,'my first info');
        ?>
        <div id="flashMessages">
            <div id="flashMessages_info">
                <?php
                foreach ($info_flashmsgs as $msg) {
                    ?>
                    <div class="flashMessage flashMessage_info">
                        <i class="material-icons icon">&#xE90F;</i>
                        <?= $msg ?>
                        <i class="material-icons close_flash">&#xE14C;</i>
                    </div>
                    <?
                }
                ?>
            </div>
            <div id="flashMessages_notice">
                <?php
                foreach ($notice_flashmsgs as $msg) {
                    ?>
                    <div class="flashMessage flashMessage_notice">
                        <i class="material-icons icon">&#xE90F;</i>
                        <?= $msg ?>
                        <i class="material-icons close_flash">&#xE14C;</i>
                    </div>
                    <?
                }
                ?>
            </div>
            <div id="flashMessages_warning">
                <?php
                foreach ($warning_flashmsgs as $msg) {
                    ?>
                    <div class="flashMessage flashMessage_warning">
                        <i class="fa fa-info icon" aria-hidden="true"></i>
                        <?= $msg ?>
                        <i class="material-icons close_flash">&#xE14C;</i>
                    </div>
                    <?
                }
                ?>
            </div>
            <div id="flashMessages_error">
                <?php
                foreach ($error_flashmsgs as $msg) {
                    ?>
                    <div class="flashMessage flashMessage_error">
                        <i class="material-icons icon">&#xE14C;</i>
                        <?= $msg ?>
                        <i class="material-icons close_flash">&#xE14C;</i>
                    </div>
                    <?
                }
                ?>
            </div>
            <div id="flashMessages_success">
                <?php
                foreach ($success_flashmsgs as $msg) {
                    ?>
                    <div class="flashMessage flashMessage_success">
                        <i class="material-icons icon">&#xE5CA;</i>
                        <?= $msg ?>
                        <i class="material-icons close_flash">&#xE14C;</i>
                    </div>
                    <?
                }
                ?>
            </div>
        </div>
    <?php
    $cookieConsentController = $IOCContainer->create(\DynCom\dc\common\cookie_consent\CookieConsentController::class);
    if ($cookieConsentController instanceof \DynCom\dc\common\cookie_consent\CookieConsentController) {
        $html = $cookieConsentController->handleRequest();
        echo $html;
    }
    ?>
    </body>
</html>
