<?php
use DynCom\dc\common\interfaces\SessionFlashMessageBag;

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'module/dcshop/shop_start.inc.php'; ?>
    <!doctype html>
    <html>

    <? require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'common/version_comment.inc.php'; ?>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
        <?= get_canonical(); ?>
        <? create_meta_tags(); ?>
        <script>
            var global_privacy_url = '<?= $GLOBALS["tc"]["global_privacy_url"] ?>';
        </script>

        <?
        get_gulp_sources('/layout/frontend/b2b/dist/', 'style_catalog');
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
    $body_class = '';
    if ($GLOBALS['language']['std_main_navigation_id'] == $navigation['id']) {
        $body_class = 'home';
    } else {
        if (isset($_GET['shop_category'])) {
            $body_class = $_GET['shop_category'];
        }
        if (isset($_GET['card'])) {
            $body_class = "card";
        }
    }
    ?>
    <body class="<?= $body_class ?>">

    <?  if ($GLOBALS["site"]["google_tag_container_id"] <> "") { ?>
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?= $GLOBALS["site"]["google_tag_container_id"] ?>"
                          height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
    <? } ?>

    <?

    if ($GLOBALS['shop']['extended_search']) {
        echo get_globals_and_sid_as_div();
    }
    ?>
    <div id="primary_navigation_mobile">
        <a class="close_button_navigation_mobile">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
            <?= $GLOBALS['tc']['toggle_navigation_label'] ?>
        </a>
        <div class="navigation_scrollbox">
            <div class="navigation">
                <? navigation_full_menu_shop($site, $language, $navigation, 1, 3) ?>
            </div>
            <div class="visible-xs visible-sm navigation navigation_cms">
                <? navigation_full_menu_area($site, $language, "info", 3); ?>
            </div>
        </div>
    </div>
    <div id="overlay" class=""></div>
    <div id="container" class="">
        <div id="container_1">
            <div id="header">
                <div id="header_0" class="hidden-xs hidden-sm">
                    <div class="container">
                        <div id="header_account">
                            <? get_content("header_login", TRUE, $IOCContainer); ?>
                            <div id="user_account_navigation" class="user_account">
                                <div class="user_account_link be_reseller_link">
                                    <a href="/<?= $site['code'] ?>/<?= $language['code'] ?>/reseller/">
                                        <span><?= $GLOBALS['tc']['become_customer'] ?></span>
                                        <i class="fa fa-user-plus" aria-hidden="true"></i>
                                    </a>
                                </div>
                                <div class="user_account_link login_link">
                                    <a href="/<?= $site['code'] ?>/<?= $language['code'] ?>/login/">
                                        <span><?= $GLOBALS['tc']['login'] ?></span>
                                        <i class="fa fa-user" aria-hidden="true"></i>
                                    </a>
                                </div>
                                <script>
                                    $(document).ready(function () {
                                        $('#user_account_navigation .login_link').click(function (event) {
                                            $('#lightbox_login').modal('show');
                                            event.preventDefault();
                                        });
                                        $('#user_account_navigation .be_reseller_link').click(function (event) {
                                            $('#lightbox_register').modal('show');
                                            event.preventDefault();
                                        });
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="header_1">
                    <div class="container">
                        <div id="toggle_navigation" class="visible-xs visible-sm">
                            <div class="navigation-bar"></div>
                            <div class="navigation-bar"></div>
                            <div class="navigation-bar"></div>
                        </div>
                        <div id="header_logo"><? get_content("header_logo", TRUE); ?></div>
                        <div id="header_search">
                            <? get_content("header_search", TRUE, $IOCContainer); ?>
                        </div>
                        <div id="header_navigation"><? navigation_full_menu_area($site, $language, "header", 2); ?></div>
                        <div id="header_account_mobile" class="visible-xs visible-sm">
                            <a id="header_search_mobile">
                                <i class="fa fa-search" aria-hidden="true"></i>
                            </a>
                            <div id="user_account_navigation" class="user_account">
                                <div class="user_account_link be_reseller_link">
                                    <a href="/<?= $site['code'] ?>/<?= $language['code'] ?>/reseller/">
                                        <span><?= $GLOBALS['tc']['become_customer'] ?></span>
                                        <i class="fa fa-user-plus" aria-hidden="true"></i>
                                    </a>
                                </div>
                                <div class="user_account_link login_link">
                                    <a href="/<?= $site['code'] ?>/<?= $language['code'] ?>/login/">
                                        <span><?= $GLOBALS['tc']['login'] ?></span>
                                        <i class="fa fa-user" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="header_2" class="hidden-xs hidden-sm">
                    <div class="container">
                        <div
                                id="primary_navigation"><? navigation_full_menu_shop($site, $language, $navigation, 1, 3) ?></div>
                        <div id="header_account"><? get_content("header_account", TRUE, $IOCContainer); ?></div>
                    </div>
                </div>
                <div class="clearfloat"></div>
            </div>
            <div id="gadget" class="hidden-xs hidden-sm">
                <div class="container">
                    <div id="header_trust">
                        <? get_content("header_trust"); ?>
                    </div>
                    <div id="gadget_2">
                        <? if ($navigation['code'] == "shop") {
                            get_shop_breadcrumb();
                        } else if (($GLOBALS['language']['std_main_navigation_id'] != $navigation['id'])) { ?>
                            <div class="row">
                                <div class="main_content_right col-xs-12 col-sm-12 col-md-12 col-lg-9">
                                    <?= cust_curr_navigation_path($site, $language, $navigation) ?>
                                </div>
                            </div>
                            <?
                        } ?>
                    </div>
                </div>
            </div>
            <div id="content">
                <div class="container">
                    <div id="content_1">
                        <? get_content("content", null, $IOCContainer); ?>
                    </div>
                    <? if ($navigation['code'] == "shop" && (isset($_GET['card']) || isset($_GET['shop_category']))) { ?>
                        <div id="content_3" class="main_content_full">
                            <? get_content("content_right", null, $IOCContainer); ?>
                        </div>
                        <?
                    } else { ?>
                        <div class="row">
                            <div id="content_2" class="main_content_left hidden-xs hidden-sm col-md-3">
                                <div id="subnavigation">
                                    <? if ($navigation['code'] == "shop") {
                                        navigation_menu_shop($site, $language, $navigation, 2, 3);
                                    } elseif ($GLOBALS['language']['std_main_navigation_id'] != $navigation['id']) {
                                        navigation_menu($site, $language, $navigation, 1, 3);
                                    } ?>
                                </div>
                                <? get_content("content_left", null, $IOCContainer); ?>
                            </div>
                            <div id="content_3" class="main_content_right col-xs-12 col-sm-12 col-md-9">
                                <? get_content("content_right", null, $IOCContainer); ?>
                            </div>
                        </div>
                        <?
                    }

                    if ($GLOBALS['language']['std_main_navigation_id'] == $navigation['id']) { ?>
                        <div class="row small_row_col">
                            <div id="content_4" class="main_content_left_home col-xs-12 col-sm-12 col-md-8 col-lg-3-5">
                                <? get_content("content_left_home", null, $IOCContainer); ?>
                            </div>
                            <div id="content_5" class="main_content_right_home col-xs-12 col-sm-12 col-md-4 col-lg-2-5">
                                <? get_content("content_right_home", null, $IOCContainer); ?>
                            </div>
                        </div>
                    <?
                    } ?>
                    <div id="content_6">
                        <? get_content("content_bottom", null, $IOCContainer); ?>
                    </div>
                </div>
            </div>
            <div id="footer" class="catalog_footer">
                <div id="footer_1">
                    <div class="container">
                        <div class="row">
                            <div id="footer_navigation"
                                 class="footer_info col-xs-12 col-sm-12 col-md-6"><? navigation_full_menu_area($site, $language, "info", 3); ?></div>
                            <div class="footer_payment col-xs-12 col-sm-12 col-md-6"><? get_content("footer_payment", TRUE,$IOCContainer); ?></div>
                            <div class="footer_shipping col-xs-12 col-sm-12 col-md-6"><? get_content("footer_shipping", TRUE,$IOCContainer); ?></div>
                            <div class="footer_socialmedia col-xs-12 col-sm-12 col-md-6"><? get_content("footer_socialmedia", TRUE, $IOCContainer); ?></div>
                        </div>
                    </div>
                </div>
                <div id="footer_2">
                    <div class="container">
                        <div class="row">
                            <div id="footer_navigation_legal" class="col-xs-12 col-sm-12 col-md-6 col-lg-7 col-xlg-8 pull-right">
                                <? navigation_menu_area($site, $language, "legal", 3); ?>
                            </div>
                            <div class="footer_copyright col-xs-12 col-sm-12 col-md-6 col-lg-5 col-xlg-4">
                                <? get_content("footer_copyright", TRUE, $IOCContainer); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'module/dcshop/shop_end.inc.php';

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
                <div class="flashMessage flashMessage_info"><?= $msg ?></div>
                <?
            }
            ?>
        </div>
        <div id="flashMessages_notice">
            <?php
            foreach ($notice_flashmsgs as $msg) {
                ?>
                <div class="flashMessage flashMessage_notice"><?= $msg ?></div>
                <?
            }
            ?>
        </div>
        <div id="flashMessages_warning">
            <?php
            foreach ($warning_flashmsgs as $msg) {
                ?>
                <div class="flashMessage flashMessage_warning"><?= $msg ?></div>
                <?
            }
            ?>
        </div>
        <div id="flashMessages_error">
            <?php
            foreach ($error_flashmsgs as $msg) {
                ?>
                <div class="flashMessage flashMessage_error"><?= $msg ?></div>
                <?
            }
            ?>
        </div>
        <div id="flashMessages_success">
            <?php
            foreach ($success_flashmsgs as $msg) {
                ?>
                <div class="flashMessage flashMessage_success"><?= $msg ?></div>
                <?
            }
            ?>
        </div>
    </div>
    <div class="modal fade form-label-left" id="lightbox_login" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <? get_content("lightbox_login", TRUE, $IOCContainer); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade form-label-left" id="lightbox_register" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <? get_content("lightbox_register", TRUE, $IOCContainer); ?>
                </div>
            </div>
        </div>
    </div>
    </body>
    </html>
