<?php

if (
isset($_GET['access_token']) && $_GET['access_token'] <> ''
&& isset($_GET['token_type']) && $_GET['token_type'] <> ''
&& isset($_GET['expires_in']) && $_GET['expires_in'] <> ''
&& isset($_GET['scope']) && $_GET['scope'] <> ''
&& isset($_GET['UserData']) && $_GET['UserData'] <> ''
&& isset($_GET['shopdata']) && $_GET['shopdata'] <> ''
) {
    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'shop.config.php';

    ?>

        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width,initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
            <link rel='canonical' href='https://192.168.56.180/b2c/de/home/'/>
            <title>Der Cronus Golf-Shop | Golf-Equipment online kaufen | Ronnefeldt Tee</title>
            <script>
                var global_privacy_url = '/b2c/de/legal/datenschutz/';
            </script>
            <link rel="stylesheet" href="<?= $GLOBALS['projectRoot'] ?>/layout/frontend/b2c/dist/css/style.min.css?time=1504706145"/>
            <script src="<?= $GLOBALS['projectRoot'] ?>/layout/frontend/b2c/dist/js/script.js?time=1504706145"></script>
            <link rel="apple-touch-icon" sizes="180x180" href="<?= $GLOBALS['projectRoot'] ?>/layout/frontend/b2c/dist/favicons/apple-touch-icon.png">
            <link rel="icon" type="image/png" href="<?= $GLOBALS['projectRoot'] ?>/layout/frontend/b2c/dist/favicons/favicon-32x32.png" sizes="32x32">
            <link rel="icon" type="image/png" href="<?= $GLOBALS['projectRoot'] ?>/layout/frontend/b2c/dist/favicons/favicon-16x16.png" sizes="16x16">
            <link rel="manifest" href="<?= $GLOBALS['projectRoot'] ?>/layout/frontend/b2c/dist/favicons/manifest.json">
            <link rel="mask-icon" href="<?= $GLOBALS['projectRoot'] ?>/layout/frontend/b2c/dist/favicons/safari-pinned-tab.svg" color="#FFB958">
            <link rel="shortcut icon" href="<?= $GLOBALS['projectRoot'] ?>/layout/frontend/b2c/dist/favicons/favicon.ico">
            <meta name="msapplication-config">
            <meta name="theme-color" content="#ffffff">

            <style>
                #addressBookWidgetDiv {
                    min-width: 300px;
                    width: 100%;
                    max-width: 900px;
                    min-height: 228px;
                    height: 240px;
                    max-height: 400px;
                }

                #walletWidgetDiv {
                    min-width: 300px;
                    width: 100%;
                    max-width: 900px;
                    min-height: 228px;
                    height: 240px;
                    max-height: 400px;
                }

                /* The following are required only when you use the read-only widgets: */

                #readOnlyAddressBookWidgetDiv {
                    min-width: 266px;
                    width: 100%;
                    max-width: 900px;
                    min-height: 145px;
                    height: 165px;
                    max-height: 180px;
                }

                #readOnlyWalletWidgetDiv {
                    min-width: 266px;
                    width: 100%;
                    max-width: 900px;
                    min-height: 145px;
                    height: 165px;
                    max-height: 180px;
                }
            </style>

        </head>
        <body class="home">

        <!-- End Google Tag Manager (noscript) -->
        <div id="primary_navigation_mobile">
            <a class="close_button_navigation_mobile">Menü</a>
            <div class="navigation_scrollbox">
                <div class="navigation">
                    <ul class="level_1">
                        <li class="level_1  "><a href="/b2c/de/golf/">Golfausstattung</a>
                            <ul class="level_2">
                                <li class="level_2  "><a href="/b2c/de/golfschlaeger/">Golfschläger</a>
                                    <ul class="level_3">
                                        <li class="level_3  "><a href="/b2c/de/hybriden/">Hybriden</a></li>
                                        <li class="level_3  "><a href="/b2c/de/eisen/">Eisen</a></li>
                                        <li class="level_3  "><a href="/b2c/de/putter/">Putter</a></li>
                                        <li class="level_3  "><a href="/b2c/de/komplettsets/">Komplettsets</a></li>
                                        <li class="level_3  "><a href="/b2c/de/kinder-jugen/">Kinder/Jugendschläger</a></li>
                                    </ul>
                                </li>
                                <li class="level_2  "><a href="/b2c/de/golfbaelle/">Golfbälle</a>
                                    <ul class="level_3">
                                        <li class="level_3  "><a href="/b2c/de/basic/">Basic</a></li>
                                        <li class="level_3  "><a href="/b2c/de/standard/">Standard</a></li>
                                        <li class="level_3  "><a href="/b2c/de/premium/">Premium</a></li>
                                    </ul>
                                </li>
                                <li class="level_2  "><a href="/b2c/de/bags-trolleys/">Bags & Trolleys</a>
                                    <ul class="level_3">
                                        <li class="level_3  "><a href="/b2c/de/carrybags/">Carrybags</a></li>
                                        <li class="level_3  "><a href="/b2c/de/cartbags/">Cartbags</a></li>
                                        <li class="level_3  "><a href="/b2c/de/ziehtrolleys/">Ziehtrolleys</a></li>
                                    </ul>
                                </li>
                                <li class="level_2  "><a href="/b2c/de/zubehoer/">Zubehör</a>
                                    <ul class="level_3">
                                        <li class="level_3  "><a href="/b2c/de/regenschirme/">Regenschirme</a></li>
                                        <li class="level_3  "><a href="/b2c/de/schlaegerhauben/">Schlägerhauben</a></li>
                                        <li class="level_3  "><a href="/b2c/de/handschuhe/">Handschuhe</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="level_1  "><a href="/b2c/de/fashion/">Fashion</a>
                            <ul class="level_2">
                                <li class="level_2  "><a href="/b2c/de/herren/">Fashion Herren</a>
                                    <ul class="level_3">
                                        <li class="level_3  "><a href="/b2c/de/golfbekleidung/">Golfbekleidung</a></li>
                                        <li class="level_3  "><a href="/b2c/de/herren-accessoires/">Accessoires</a></li>
                                    </ul>
                                </li>
                                <li class="level_2  "><a href="/b2c/de/damen/">Fashion Damen</a>
                                    <ul class="level_3">
                                        <li class="level_3  "><a href="/b2c/de/bekleidung/">Golfbekleidung</a></li>
                                        <li class="level_3  "><a href="/b2c/de/damen-accessoires/">Accessoires</a></li>
                                    </ul>
                                </li>
                                <li class="level_2  "><a href="/b2c/de/kids/">Fashion Kids</a>
                                    <ul class="level_3">
                                        <li class="level_3  "><a href="/b2c/de/boys/">Boys</a></li>
                                        <li class="level_3  "><a href="/b2c/de/girls/">Girls</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="level_1  "><a href="/b2c/de/geschenkideen/">Geschenke</a>
                            <ul class="level_2">
                                <li class="level_2  "><a href="/b2c/de/gutscheine/">Gutscheine</a>
                                    <ul class="level_3">
                                        <li class="level_3  "><a href="/b2c/de/gutscheinkarten/">Gutscheinkarten</a></li>
                                        <li class="level_3  "><a href="/b2c/de/onlinegutscheine/dc_order/">Online-Gutscheine</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="level_2  "><a href="/b2c/de/ideen/">Geschenkideen</a>
                                    <ul class="level_3">
                                        <li class="level_3  "><a href="/b2c/de/aufmerksamkeit/">Kleine Aufmerksamkeiten</a></li>
                                        <li class="level_3  "><a href="/b2c/de/geschenksets/">Geschenksets</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="level_1  "><a href="/b2c/de/marken/">Marken</a>
                            <ul class="level_2">
                                <li class="level_2  "><a href="/b2c/de/top-marken-golf/">Top-Marken Golf</a>
                                    <ul class="level_3">
                                        <li class="level_3  "><a href="/b2c/de/callaway/">Callaway</a></li>
                                        <li class="level_3  "><a href="/b2c/de/wilson/">Wilson</a></li>
                                        <li class="level_3  "><a href="/b2c/de/bigmax/">Big Max</a></li>
                                        <li class="level_3  "><a href="/b2c/de/taylormade/">TaylorMade</a></li>
                                        <li class="level_3  "><a href="/b2c/de/titleist/">Titleist</a></li>
                                    </ul>
                                </li>
                                <li class="level_2  "><a href="/b2c/de/top-marken-fashion/">Top-Marken Fashion</a>
                                    <ul class="level_3">
                                        <li class="level_3  "><a href="/b2c/de/adidas/">Adidas</a></li>
                                        <li class="level_3  "><a href="/b2c/de/callaway-fashion/">Callaway</a></li>
                                        <li class="level_3  "><a href="/b2c/de/alberto/">Alberto</a></li>
                                        <li class="level_3  "><a href="/b2c/de/nike/">Nike</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="level_1  "><a href="/b2c/de/aktionen/">Aktionen</a>
                            <ul class="level_2">
                                <li class="level_2  "><a href="/b2c/de/highlights/">Highlights</a>
                                    <ul class="level_3">
                                        <li class="level_3  "><a href="/b2c/de/neuheiten/">Neuheiten</a></li>
                                        <li class="level_3  "><a href="/b2c/de/top-seller/">Top-Seller</a></li>
                                    </ul>
                                </li>
                                <li class="level_2  "><a href="/b2c/de/angebote/">Angebote</a>
                                    <ul class="level_3">
                                        <li class="level_3  "><a href="/b2c/de/sale/">Sale</a></li>
                                        <li class="level_3  "><a href="/b2c/de/restposten/">Restposten</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="visible-xs navigation navigation_cms">

                    <ul class="level_2">
                        <li class="level_2"><a href="/b2c/de/info/cronus-golf/">Cronus Golf</a>
                            <ul class="level_3">
                                <li class="level_3"><a href="/b2c/de/info/cronus-golf/ueber-uns/">Über uns</a></li>
                                <li class="level_3"><a href="/b2c/de/info/cronus-golf/ladengeschaefte/">Ladengeschäfte</a></li>
                                <li class="level_3"><a href="/b2c/de/info/cronus-golf/golf-etikette/">Golf-Etikette</a></li>
                                <li class="level_3"><a href="/b2c/de/info/cronus-golf/ratgeber/">Golf-Ratgeber</a></li>
                                <li class="level_3"><a href="/b2c/de/info/cronus-golf/golf-tipps/">Golf-Tipps</a></li>
                                <li class="level_3"><a href="/b2c/de/info/cronus-golf/kontakt/">Kontakt</a></li>
                            </ul>
                        </li>
                        <li class="level_2"><a href="/b2c/de/info/bester-kundenservice/">Bester Kundenservice</a>
                            <ul class="level_3">
                                <li class="level_3"><a href="/b2c/de/info/bester-kundenservice/faq/">Häufige Fragen (FAQ)</a>
                                </li>
                                <li class="level_3"><a href="/b2c/de/info/bester-kundenservice/kundeninformationen/">Kundeninformationen</a>
                                </li>
                                <li class="level_3"><a href="/b2c/de/info/bester-kundenservice/zahlungsoptionen/">Zahlungsoptionen</a>
                                </li>
                                <li class="level_3"><a
                                            href="/b2c/de/info/bester-kundenservice/versandoptionen/">Versandoptionen</a></li>
                                <li class="level_3"><a href="/b2c/de/info/bester-kundenservice/custom-fitting/">Custom
                                        Fitting</a></li>
                                <li class="level_3"><a
                                            href="/b2c/de/info/bester-kundenservice/groessentabelle/">Größentabelle</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="visible-xs navigation navigation_cms">
                    <ul class="level_2">
                        <li class="level_2"><a href="/b2c/de/legal/widerrufsrecht/">Widerrufsrecht</a></li>
                        <li class="level_2"><a href="/b2c/de/legal/datenschutz/">Datenschutz</a></li>
                        <li class="level_2"><a href="/b2c/de/legal/agb/">AGB</a></li>
                        <li class="level_2"><a href="/b2c/de/legal/impressum/">Impressum</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div id="overlay" class=""></div>
        <div id="container" class="">
            <div id="container_1">
                <div id="header_account" class="hidden-xs">
                    <div class="container">
                        <div class="component header_account">
                            <div id="user_account_navigation" class="user_account">
                                <div class="user_account_link login_link">
                                    <a href="/b2c/de/fix/login/">
                                        Anmelden </a>
                                </div>
                                <div class="user_account_link favorites_link">
                                    <a
                                            href="/b2c/de/favorites/">
                                        Merkliste (0) <i class="fa fa-angle-down" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="language_switch">
                                <a href="#" class="language_switch_button">
                                    Sprache wählen <i class="fa fa-angle-down" aria-hidden="true"></i>
                                </a>
                                <div class="list_language_switch">
                                    <ul id="language_switch" name="list_language_switch">
                                        <li class="active"><a href="/b2c/de/home/">Deutsch</a></li>
                                        <li><a href="/b2c/en/home/">Englisch</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="header">
                    <div class="container">
                        <div id="toggle_navigation" class="visible-xs visible-sm">
                            <div class="navigation-bar"></div>
                            <div class="navigation-bar"></div>
                            <div class="navigation-bar"></div>
                        </div>
                        <div id="header_1">
                            <div class="component header_logo">
                                <div class="textcontent">
                                    <a href="/b2c/de/"><img alt="" src="<?= $GLOBALS['projectRoot'] ?>/userdata/images/CRONUS-Golf_Logo.png"
                                                            style="width: 190px;"/></a>
                                </div>
                            </div>
                        </div>
                        <div id="header_2" class="hidden-xs hidden-sm">
                            <div
                                    id="primary_navigation">
                                <ul class="level_1">
                                    <li class="level_1  "><a href="/b2c/de/golf/">Golfausstattung</a>
                                        <ul class="level_2">
                                            <li class="level_2  "><a href="/b2c/de/golfschlaeger/">Golfschläger</a>
                                                <ul class="level_3">
                                                    <li class="level_3  "><a href="/b2c/de/hybriden/">Hybriden</a></li>
                                                    <li class="level_3  "><a href="/b2c/de/eisen/">Eisen</a></li>
                                                    <li class="level_3  "><a href="/b2c/de/putter/">Putter</a></li>
                                                    <li class="level_3  "><a href="/b2c/de/komplettsets/">Komplettsets</a></li>
                                                    <li class="level_3  "><a
                                                                href="/b2c/de/kinder-jugen/">Kinder/Jugendschläger</a></li>
                                                </ul>
                                            </li>
                                            <li class="level_2  "><a href="/b2c/de/golfbaelle/">Golfbälle</a>
                                                <ul class="level_3">
                                                    <li class="level_3  "><a href="/b2c/de/basic/">Basic</a></li>
                                                    <li class="level_3  "><a href="/b2c/de/standard/">Standard</a></li>
                                                    <li class="level_3  "><a href="/b2c/de/premium/">Premium</a></li>
                                                </ul>
                                            </li>
                                            <li class="level_2  "><a href="/b2c/de/bags-trolleys/">Bags & Trolleys</a>
                                                <ul class="level_3">
                                                    <li class="level_3  "><a href="/b2c/de/carrybags/">Carrybags</a></li>
                                                    <li class="level_3  "><a href="/b2c/de/cartbags/">Cartbags</a></li>
                                                    <li class="level_3  "><a href="/b2c/de/ziehtrolleys/">Ziehtrolleys</a></li>
                                                </ul>
                                            </li>
                                            <li class="level_2  "><a href="/b2c/de/zubehoer/">Zubehör</a>
                                                <ul class="level_3">
                                                    <li class="level_3  "><a href="/b2c/de/regenschirme/">Regenschirme</a></li>
                                                    <li class="level_3  "><a href="/b2c/de/schlaegerhauben/">Schlägerhauben</a>
                                                    </li>
                                                    <li class="level_3  "><a href="/b2c/de/handschuhe/">Handschuhe</a></li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="level_1  "><a href="/b2c/de/fashion/">Fashion</a>
                                        <ul class="level_2">
                                            <li class="level_2  "><a href="/b2c/de/herren/">Fashion Herren</a>
                                                <ul class="level_3">
                                                    <li class="level_3  "><a href="/b2c/de/golfbekleidung/">Golfbekleidung</a>
                                                    </li>
                                                    <li class="level_3  "><a href="/b2c/de/herren-accessoires/">Accessoires</a>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li class="level_2  "><a href="/b2c/de/damen/">Fashion Damen</a>
                                                <ul class="level_3">
                                                    <li class="level_3  "><a href="/b2c/de/bekleidung/">Golfbekleidung</a></li>
                                                    <li class="level_3  "><a href="/b2c/de/damen-accessoires/">Accessoires</a>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li class="level_2  "><a href="/b2c/de/kids/">Fashion Kids</a>
                                                <ul class="level_3">
                                                    <li class="level_3  "><a href="/b2c/de/boys/">Boys</a></li>
                                                    <li class="level_3  "><a href="/b2c/de/girls/">Girls</a></li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="level_1  "><a href="/b2c/de/geschenkideen/">Geschenke</a>
                                        <ul class="level_2">
                                            <li class="level_2  "><a href="/b2c/de/gutscheine/">Gutscheine</a>
                                                <ul class="level_3">
                                                    <li class="level_3  "><a href="/b2c/de/gutscheinkarten/">Gutscheinkarten</a>
                                                    </li>
                                                    <li class="level_3  "><a href="/b2c/de/onlinegutscheine/dc_order/">Online-Gutscheine</a>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li class="level_2  "><a href="/b2c/de/ideen/">Geschenkideen</a>
                                                <ul class="level_3">
                                                    <li class="level_3  "><a href="/b2c/de/aufmerksamkeit/">Kleine
                                                            Aufmerksamkeiten</a></li>
                                                    <li class="level_3  "><a href="/b2c/de/geschenksets/">Geschenksets</a></li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="level_1  "><a href="/b2c/de/marken/">Marken</a>
                                        <ul class="level_2">
                                            <li class="level_2  "><a href="/b2c/de/top-marken-golf/">Top-Marken Golf</a>
                                                <ul class="level_3">
                                                    <li class="level_3  "><a href="/b2c/de/callaway/">Callaway</a></li>
                                                    <li class="level_3  "><a href="/b2c/de/wilson/">Wilson</a></li>
                                                    <li class="level_3  "><a href="/b2c/de/bigmax/">Big Max</a></li>
                                                    <li class="level_3  "><a href="/b2c/de/taylormade/">TaylorMade</a></li>
                                                    <li class="level_3  "><a href="/b2c/de/titleist/">Titleist</a></li>
                                                </ul>
                                            </li>
                                            <li class="level_2  "><a href="/b2c/de/top-marken-fashion/">Top-Marken Fashion</a>
                                                <ul class="level_3">
                                                    <li class="level_3  "><a href="/b2c/de/adidas/">Adidas</a></li>
                                                    <li class="level_3  "><a href="/b2c/de/callaway-fashion/">Callaway</a></li>
                                                    <li class="level_3  "><a href="/b2c/de/alberto/">Alberto</a></li>
                                                    <li class="level_3  "><a href="/b2c/de/nike/">Nike</a></li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="level_1  "><a href="/b2c/de/aktionen/">Aktionen</a>
                                        <ul class="level_2">
                                            <li class="level_2  "><a href="/b2c/de/highlights/">Highlights</a>
                                                <ul class="level_3">
                                                    <li class="level_3  "><a href="/b2c/de/neuheiten/">Neuheiten</a></li>
                                                    <li class="level_3  "><a href="/b2c/de/top-seller/">Top-Seller</a></li>
                                                </ul>
                                            </li>
                                            <li class="level_2  "><a href="/b2c/de/angebote/">Angebote</a>
                                                <ul class="level_3">
                                                    <li class="level_3  "><a href="/b2c/de/sale/">Sale</a></li>
                                                    <li class="level_3  "><a href="/b2c/de/restposten/">Restposten</a></li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div id="header_3">
                            <div class="component header_basket">
                                <div id="header_basket" class=" ">
                                    <div class="shopping_bag">
                                        <a href="/b2c/de/basket/">
            <span
                    class="hidden-xs hidden-sm hidden-md">Warenkorb </span>
                                            <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="header_4">
                            <div class="component header_search">
                                <div id="search">
                                    <form id="form_search" name="form_search" method="get"
                                          action="/b2c/de/search/">
                                        <div class="search_field">
                                            <input type="text" name="input_search" id="input_search"
                                                   placeholder="Lieblingsprodukt suchen"/>
                                        </div>
                                        <div class="search_button" onclick="$('#form_search').submit();">
                                            <i class="fa fa-search" aria-hidden="true"></i>
                                        </div>
                                    </form>
                                    <div id="itemsearch_suggestion_wrapper">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="header_shop_icons" class="visible-xs">
                            <div class="header_shop_icon header_icon_favorites">
                                <a href="/b2c/de/favorites/">
                                    <i class="fa fa-heart" aria-hidden="true"></i>
                                </a>
                            </div>
                            <div class="header_shop_icon header_icon_account">
                                <a href="/b2c/de/fix/login/">
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
                            Checkout
                        </div>
                        <div id="gadget_2">
                        </div>
                        <div id="gadget_3"></div>
                    </div>
                </div>
                <div id="content">
                    <div class="container">
                        <div id="content_1">

                            <div class="row">

                                <script>
                                    window.onAmazonLoginReady = function () {
                                        amazon.Login.setClientId('<?= $GLOBALS["amazon_pay"]["client_id"] ?>');
                                    };
                                </script>
                                <script async="async"
                                        src='https://static-eu.payments-amazon.com/OffAmazonPayments/eur/sandbox/lpa/js/Widgets.js'>
                                </script>


                                <!-- Place this code in your HTML where you would like the address widget to appear. -->
                                <div id="addressBookWidgetDiv" class="row">AAAA</div>

                                <!-- Place this code in your HTML where you would like the wallet widget to appear. -->
                                <div id="walletWidgetDiv" class="row">BBBB</div>

                                <script>
                                    window.onAmazonPaymentsReady = function () {
                                        new OffAmazonPayments.Widgets.AddressBook({
                                            sellerId: '<?= $GLOBALS["amazon_pay"]["seller_id"] ?>',
                                            scope: 'payments:shipping_address',
                                            onOrderReferenceCreate: function (orderReference) {
                                                // Here is where you can grab the Order Reference ID.
                                                orderReference.getAmazonOrderReferenceId();
                                                document.getElementById('reference_value').value = orderReference.getAmazonOrderReferenceId();
                                            },
                                            onAddressSelect: function (orderReference) {
                                                // Replace the following code with the action that you want
                                                // to perform after the address is selected. The
                                                // amazonOrderReferenceId can be used to retrieve the address
                                                // details by calling the GetOrderReferenceDetails operation.

                                                // If rendering the AddressBook and Wallet widgets
                                                // on the same page, you do not have to provide any additional
                                                // logic to load the Wallet widget after the AddressBook widget.
                                                // The Wallet widget will re-render itself on all subsequent
                                                // onAddressSelect events, without any action from you.
                                                // It is not recommended that you explicitly refresh it.
                                            },
                                            design: {
                                                designMode: 'responsive'
                                            },
                                            onReady: function (orderReference) {
                                                // Enter code here you want to be executed
                                                // when the address widget has been rendered.
                                            },
                                            onError: function (error) {
                                                // Your error handling code.
                                                // During development you can use the following
                                                // code to view error messages:
                                                console.log(error.getErrorCode() + ': ' + error.getErrorMessage());
                                                // See "Handling Errors" for more information.
                                            }
                                        }).bind("addressBookWidgetDiv");

                                        new OffAmazonPayments.Widgets.Wallet({
                                            sellerId: '<?= $GLOBALS["amazon_pay"]["seller_id"] ?>',
                                            scope: 'payments:widget',
                                            onPaymentSelect: function (orderReference) {
                                                // Replace this code with the action that you want to perform
                                                // after the payment method is selected.

                                                // Ideally this would enable the next action for the buyer
                                                // including either a "Continue" or "Place Order" button.
                                            },
                                            design: {
                                                designMode: 'responsive'
                                            },
                                            onError: function (error) {
                                                // Your error handling code.
                                                // During development you can use the following
                                                // code to view error messages:
                                                console.log(error.getErrorCode() + ': ' + error.getErrorMessage());
                                                // See "Handling Errors" for more information.
                                            }
                                        }).bind("walletWidgetDiv");

                                    };

                                    function reDirect() {

                                        var refrenceId = document.getElementById('reference_value').value;
                                        window.location = "/module/dcshop/b2c/amazon_payment.inc.php?<?= $_SERVER['QUERY_STRING']?>&refrence_id=" + refrenceId;
                                    }

                                </script>

                                <div class="button_row text-right">
                                    <input type="hidden" id="reference_value" name="reference_value">
                                    <button  class="basket_button_next button button_action text-center" id="next_button" onclick="reDirect()"> Next</button>
                                </div>

                            </div>




                        </div>
                        <div class="row">
                            <div id="content_2" class="main_content_left hidden-xs hidden-sm col-md-3">
                                <div id="subnavigation">

                                </div>
                            </div>
                            <div id="content_3" class="main_content_right col-xs-12 col-sm-12 col-md-9">
                            </div>
                        </div>
                    </div>
                </div>




    <?php
} else {
    die('No Permission');
}

?>

