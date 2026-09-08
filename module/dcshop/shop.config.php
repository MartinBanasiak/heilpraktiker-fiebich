<?
$GLOBALS["shop_setup"]["shop_password"]                 = getenv('SHOP_PASSWORD');
$GLOBALS["shop_setup"]["shop_userdata_basedir"]         = "/userdata/dcshop";
$GLOBALS["shop_setup"]["uploaddir"]                     = "/userdata/dcshop/images/";
$GLOBALS["shop_setup"]["uploaddir_videos"]              = "/userdata/dcshop/videos/";
$GLOBALS["shop_setup"]["uploaddir_documents"]           = "/userdata/private/documents/";
$GLOBALS["shop_setup"]["uploaddir_category_picture"]    = "/userdata/dcshop/category_picture/";
$GLOBALS["shop_setup"]["uploaddir_category_icon"]       = "/userdata/dcshop/category_icon/";
$GLOBALS["shop_setup"]["uploaddir_attachements"]        = "/userdata/private/attachments/";
$GLOBALS["shop_setup"]["vendor_image_dir"]              = "/userdata/dcshop/vendor_images/";
$GLOBALS["shop_setup"]["uploaddir_filter_icon"]         = "/userdata/dcshop/filter_icon/";
$GLOBALS["shop_setup"]["uploaddir_dc"]                  = "/userdata/dcshop/dc_background/";
$GLOBALS['shop_setup']['uploaddir_campaign_images']     = '/userdata/dcshop/campaign_images/';
$GLOBALS["shop_setup"]["uploaddir_order_icons"]         = "/userdata/dcshop/order_icons/";
$GLOBALS["shop_setup"]["uploaddir_salesperson_images"]  = "/userdata/dcshop/salesperson_images/";
$GLOBALS["shop_setup"]["uploaddir_customize"]  = "/userdata/dcshop/customize/";
$GLOBALS['shop_setup']['uploaddir_360_degree_images'] = '/userdata/dcshop/360_degree_images';

//Standardwerte (für alle Shops, falls keine abweichende config existiert)

// Artikellisten
$GLOBALS["shop_setup"]["num_items_per_page"] = 12;
if($GLOBALS["shop"]["no_of_items_per_page"] <> $GLOBALS["shop_setup"]["num_items_per_page"] ){
    $GLOBALS["shop_setup"]["num_items_per_page"]  = $GLOBALS["shop"]["no_of_items_per_page"];
}

$GLOBALS["shop_setup"]["show_rma"] = 1;
$GLOBALS["shop_setup"]['max_no_of_results']  = 30;
$GLOBALS['max_no_of_results'] = $GLOBALS['shop_setup']['max_no_of_results'];

// Bildgrößen
$GLOBALS["shop_setup"]["image_config"][1]["path"]                       = $GLOBALS["shop_setup"]["uploaddir"] . "thumb_1";
$GLOBALS["shop_setup"]["image_config"][1]["maxwidth"]                   = 80;
$GLOBALS["shop_setup"]["image_config"][1]["maxheight"]                  = 80;
$GLOBALS["shop_setup"]["image_config"][2]["path"]                       = $GLOBALS["shop_setup"]["uploaddir"] . "thumb_2";
$GLOBALS["shop_setup"]["image_config"][2]["maxwidth"]                   = 330;
$GLOBALS["shop_setup"]["image_config"][2]["maxheight"]                  = 330;
$GLOBALS["shop_setup"]["image_config"][3]["path"]                       = $GLOBALS["shop_setup"]["uploaddir"] . "thumb_3";
$GLOBALS["shop_setup"]["image_config"][3]["maxwidth"]                   = 600;
$GLOBALS["shop_setup"]["image_config"][3]["maxheight"]                  = 600;
$GLOBALS["shop_setup"]["image_config"][4]["path"]                       = $GLOBALS["shop_setup"]["uploaddir"] . "normal";
$GLOBALS["shop_setup"]["image_config"][4]["maxwidth"]                   = 1200;
$GLOBALS["shop_setup"]["image_config"][4]["maxheight"]                  = 1200;
$GLOBALS["shop_setup"]["image_config"][5]["path"]                       = $GLOBALS["shop_setup"]["uploaddir"] . "thumb_4";
$GLOBALS["shop_setup"]["image_config"][5]["maxwidth"]                   = 240;
$GLOBALS["shop_setup"]["image_config"][5]["maxheight"]                  = 240;
$GLOBALS["shop_setup"]["category_picture_maxwidth"]                     = 1200;
$GLOBALS["shop_setup"]["category_picture_maxheight"]                    = 400;
$GLOBALS["shop_setup"]["category_icon_maxwidth"]                        = 330;
$GLOBALS["shop_setup"]["category_icon_maxheight"]                       = 330;
$GLOBALS["shop_setup"]["filter_icon_maxwidth"]                          = 150;
$GLOBALS["shop_setup"]["filter_icon_maxheight"]                         = 150;
$GLOBALS["shop_setup"]["campaign_icon_condition_items_maxwidth"]        = 50;
$GLOBALS["shop_setup"]["campaign_icon_condition_items_maxheight"]       = 50;
$GLOBALS["shop_setup"]["campaign_banner_condition_items_maxwidth"]      = 980;
$GLOBALS["shop_setup"]["campaign_banner_condition_items_maxheight"]     = 150;
$GLOBALS["shop_setup"]["campaign_icon_action_items_maxwidth"]           = 50;
$GLOBALS["shop_setup"]["campaign_icon_action_items_maxheight"]          = 50;
$GLOBALS["shop_setup"]["campaign_banner_action_items_maxwidth"]         = 980;
$GLOBALS["shop_setup"]["campaign_banner_action_items_maxheight"]        = 150;
$GLOBALS["shop_setup"]["order_icon_maxwidth"]                           = 62;
$GLOBALS["shop_setup"]["order_icon_maxheight"]                          = 62;
$GLOBALS["shop_setup"]["salesperson_image_maxwidth"]                    = 150;
$GLOBALS["shop_setup"]["salesperson_image_maxheight"]                   = 180;

// Layoutpfade für Webforms
$GLOBALS["shop_setup"]["layout_path"]   = '/layout/frontend/b2c/';
$GLOBALS["shop_setup"]["fck_style_dir"] = "layout/frontend/b2c/dist/css/fck.css";
$GLOBALS["shop_setup"]["fck_xml_dir"]   = 'layout/frontend/b2c/ck_styles.js';

// User-Sortierung von Artikellisten
/*$GLOBALS["category_sort_types"][1]['description'] = "shop_view_active_item.order_ranking";
$GLOBALS["category_sort_types"][1]['code'] = "ranking";
$GLOBALS["category_sort_types"][1]['name'] = $GLOBALS['tc']['order_ranking'];
$GLOBALS["category_sort_types"][2]['description'] = "shop_view_active_item.item_no";
$GLOBALS["category_sort_types"][2]['code'] = "item_no";
$GLOBALS["category_sort_types"][2]['name'] = $GLOBALS['tc']['order_item_no'];
$GLOBALS["category_sort_types"][3]['description'] = "shop_view_active_item.description";
$GLOBALS["category_sort_types"][3]['code'] = "description";
$GLOBALS["category_sort_types"][3]['name'] = $GLOBALS['tc']['order_description'];
$GLOBALS["category_sort_types"][4]['description'] = "shop_view_active_item.creation_date";
$GLOBALS["category_sort_types"][4]['code'] = "creation_date";
$GLOBALS["category_sort_types"][4]['name'] = $GLOBALS['tc']['order_latest'];
$GLOBALS["category_sort_types"][5]['description'] = "shop_item_has_category.sorting ASC";
$GLOBALS["category_sort_types"][5]['code'] = "sorting";
$GLOBALS["category_sort_types"][5]['name'] = "Empfehlung";
$GLOBALS["category_sort_types"][6]['description'] = "shop_view_active_item.base_price ASC";
$GLOBALS["category_sort_types"][6]['code'] = "base_price_asc";
$GLOBALS["category_sort_types"][6]['name'] = $GLOBALS['tc']['order_price_asc'];
$GLOBALS["category_sort_types"][7]['description'] = "shop_view_active_item.base_price DESC";
$GLOBALS["category_sort_types"][7]['code'] = "base_price_desc";
$GLOBALS["category_sort_types"][7]['name'] = $GLOBALS['tc']['order_price_desc'];*/

$GLOBALS["category_sort_types"][0]['description'] = "shop_view_active_item.order_ranking";
$GLOBALS["category_sort_types"][0]['code']        = "ranking";
$GLOBALS["category_sort_types"][0]['name']        = $GLOBALS['tc']['order_ranking'];
$GLOBALS["category_sort_types"][1]['description'] = "";
$GLOBALS["category_sort_types"][1]['code'] = "";
$GLOBALS["category_sort_types"][1]['name'] = "";
$GLOBALS["category_sort_types"][2]['description'] = "shop_view_active_item.description";
$GLOBALS["category_sort_types"][2]['code']        = "description";
$GLOBALS["category_sort_types"][2]['name']        = $GLOBALS['tc']['order_description'];
$GLOBALS["category_sort_types"][3]['description'] = "shop_view_active_item.id DESC";
$GLOBALS["category_sort_types"][3]['code']        = "creation_date";
$GLOBALS["category_sort_types"][3]['name']        = $GLOBALS['tc']['order_latest'];
$GLOBALS["category_sort_types"][4]['description'] = "shop_view_active_item.base_price ASC";
$GLOBALS["category_sort_types"][4]['code']        = "base_price_asc";
$GLOBALS["category_sort_types"][4]['name']        = $GLOBALS['tc']['order_price_asc'];
$GLOBALS["category_sort_types"][5]['description'] = "shop_view_active_item.base_price DESC";
$GLOBALS["category_sort_types"][5]['code']        = "base_price_desc";
$GLOBALS["category_sort_types"][5]['name']        = $GLOBALS['tc']['order_price_desc'];
$GLOBALS["category_sort_types"][6]['description'] = "shop_item_has_category.sorting ASC";
$GLOBALS["category_sort_types"][6]['code']        = "sorting";
$GLOBALS["category_sort_types"][6]['name']        = $GLOBALS['tc']['recommendation'];
#$GLOBALS['category_sort_types'][7]['description'] = '';
#$GLOBALS['category_sort_types'][7]['code'] = 'customer_price';
#$GLOBALS['category_sort_types'][7]['name'] = $GLOBALS['tc']['customer_price'];

//Standard-Seite für ssl
$GLOBALS['shop_setup']['use_ssl']  = 0;
$GLOBALS['shop_setup']['ssl_site'] = 'demo.dc-solution.de';
//Standard-Seite für newsletter
$GLOBALS['shop_setup']['newsletter_site'] = 'www.dc-solution.de';
//URL für Sitepart Newsletter-Anmeldung
$GLOBALS['shop_setup']['newsletter_subscription_url'] = "localhost/endkunden/de/left_menu/news/";

//Modulberechtigungen
$GLOBALS['shop_setup']['rating_active'] = 1;

// Bildgrößen für Digitalen Gutscheinversand
$GLOBALS["shop_setup"]["dc_image_config"][1]["path"]      = $GLOBALS["shop_setup"]["uploaddir_dc"] . "thumb_1";
$GLOBALS["shop_setup"]["dc_image_config"][1]["maxwidth"]  = 150;
$GLOBALS["shop_setup"]["dc_image_config"][1]["maxheight"] = 68;
$GLOBALS["shop_setup"]["dc_image_config"][2]["path"]      = $GLOBALS["shop_setup"]["uploaddir_dc"] . "thumb_2";
$GLOBALS["shop_setup"]["dc_image_config"][2]["maxwidth"]  = 740;
$GLOBALS["shop_setup"]["dc_image_config"][2]["maxheight"] = 360;
$GLOBALS["shop_setup"]["dc_image_config"][3]["path"]      = $GLOBALS["shop_setup"]["uploaddir_dc"] . "original";
$GLOBALS["shop_setup"]["dc_image_config"][3]["maxwidth"]  = 790;
$GLOBALS["shop_setup"]["dc_image_config"][3]["maxheight"] = 420;

$GLOBALS["shop_setup"]["mail_color"] = "#99C137";

//Layout 1 = 3 spaltig, Layout 2 = 2 spaltig
$GLOBALS['shop_setup']['itemcard_layout'] = 1;

$GLOBALS['shop_setup']['show_category_icon_in_top_navigation_menu'] = 0;
$GLOBALS['shop_setup']['show_category_icon_in_side_navigation_menu'] = 0;
$GLOBALS['shop_setup']['show_category_description_in_category_list'] = 0;

$GLOBALS['shop_setup']['allowed_false_login_times'] = 3;
$GLOBALS['shop_setup']['false_login_waiting_seconds'] = 60;


$GLOBALS['shop_setup']['password_strength_length'] = (int)getenv('PASSWORD_STRENGTH_LENGTH');
if($GLOBALS['shop_setup']['password_strength_length'] < 1)
{
    $GLOBALS['shop_setup']['password_strength_length'] = 6;
}

$GLOBALS["payolution_config"]["installment_plan_username"] = "greenpanda-installment";
$GLOBALS["payolution_config"]["installment_plan_password"] = "BDKdUhXq9Gk4R3YcKlJsUu0EptsE7LfExcH";

$GLOBALS["amazon_pay"]["seller_id"] = "A3U44A13EKFQQ1";
$GLOBALS["amazon_pay"]["client_id"] = "amzn1.application-oa2-client.ee86c009e13141438642c32db207811f";



$GLOBALS['shop_setup']['show_short_url'] =  (int)getenv('show_short_url');
$GLOBALS['shop_setup']['num_orders_per_page'] = 10;



?>