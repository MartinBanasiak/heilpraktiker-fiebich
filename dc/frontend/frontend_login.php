<?php
?>
    <!doctype html>
    <html>

    <? require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'version_comment.inc.php'; ?>
    <? require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR . 'dcshop' . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'shop_functions.inc.php'; ?>
    <? require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR . 'dcshop' . DIRECTORY_SEPARATOR . 'shop.config.php'; ?>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
        <?= get_canonical(); ?>
        <? create_meta_tags(); ?>
        <script>
            var global_privacy_url = '<?= $GLOBALS["tc"]["global_privacy_url"] ?>';
        </script>

        <?
        get_gulp_sources('/layout/frontend/b2b/dist/','style_login');
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
    }
    ?>
    <body class="<?= $body_class ?>">
    <?  if ($GLOBALS["site"]["google_tag_container_id"] <> "") { ?>
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?= $GLOBALS["site"]["google_tag_container_id"] ?>"
                          height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
    <? } ?>
    <div id="overlay" class=""></div>
    <div id="container" class="">
        <div id="container_1">
            <div id="header">
                <div id="header_1">
                    <div class="container">
                        <div id="header_logo"><? get_content("header_logo", TRUE); ?></div>
                        <div id="user_account_navigation" class="user_account">
                            <div class="user_account_link be_reseller_link">
                                <a href="/<? echo customizeUrl(); ?>/reseller/">
                                    <span>Händler werden</span>
                                    <i class="fa fa-user-plus" aria-hidden="true"></i>
                                </a>
                            </div>
                            <div class="user_account_link login_link">
                                <a href="/<? echo customizeUrl(); ?>/login/">
                                    <span>Anmelden</span>
                                    <i class="fa fa-user" aria-hidden="true"></i>
                                </a>
                            </div>
                            <div class="user_account_link homepage_link">
                                <a href="/catalog/de/">
                                    <span>Zur Webseite</span>
                                    <i class="fa fa-globe" aria-hidden="true"></i>
                                </a>
                            </div>
                            <script>
                                $(document).ready(function(){
                                    $('#user_account_navigation .login_link').click(function (event) {
                                        $('#lightbox_login').modal('show');
                                        event.preventDefault();
                                    });
                                    $('#user_account_navigation .be_reseller_link').click(function (event) {
                                        $('#lightbox_register').modal('show');
                                        event.preventDefault();
                                    });
                                    $('#register').click(function (event) {
                                        $('#lightbox_register').modal('show');
                                        event.preventDefault();
                                    });
                                    $('#lost_password').click(function (event) {
                                        $('#lightbox_password').modal('show');
                                        event.preventDefault();
                                    });
                                });
                            </script>
                        </div>
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
                    <div class="row">
                        <div id="content_1" class="col-xs-12 col-sm-6">
                            <? get_content("content", null, $IOCContainer); ?>
                        </div>
                        <div id="content_2" class="hidden-xs col-sm-6">
                            <? get_content("content_2", null, $IOCContainer); ?>
                        </div>
                        <div id="content_3" class="col-xs-12">
                            <? get_content("content_3", null, $IOCContainer); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div id="footer">
                <div class="container">
                    <div class="row">
                        <div id="footer_navigation" class="col-xs-12 col-sm-12 col-md-7 pull-right">
                            <? navigation_full_menu_area($site, $language, "info", 3); ?>
                        </div>
                        <div id="footer_copyright" class="col-xs-12 col-sm-12 col-md-5">
                            <? get_content("footer_copyright", TRUE, $IOCContainer); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade form-label-left" id="lightbox_login" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
    <div class="modal fade form-label-left" id="lightbox_register" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
    <div class="modal fade form-label-left" id="lightbox_password" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <?php
                    if (isset($_SESSION['password_reminder_success']) && $_SESSION['password_reminder_success']) {
                        ?>
                        <div class="alert alert-success">
                            <?php echo $GLOBALS['tc']['password_success'] ?>
                        </div>

                        <script type="text/javascript">
                            $(window).load(function(){
                                $('#lightbox_password').modal('show');
                            });
                        </script>
                        <?
                    } elseif (isset($_SESSION['password_reminder_success']) && !$_SESSION['password_reminder_success']) {
                        ?>
                        <div class="alert alert-danger">
                            <?php echo $GLOBALS['tc']['password_error'] ?>
                        </div>

                        <script type="text/javascript">
                            $(window).load(function(){
                                $('#lightbox_password').modal('show');
                            });
                        </script>
                        <?
                        unset($_SESSION['password_reminder_success']);
                    }
                    ?>
                    <? get_content("lightbox_password", TRUE, $IOCContainer); ?>
                </div>
            </div>
        </div>
    </div>
    </body>
    </html>
<?php
