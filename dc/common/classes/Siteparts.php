<?php
namespace DynCom\dc\common\classes;
/**
 * Class Siteparts
 * @package DynCom\dc\common\classes
 */
class Siteparts {

    private static $siteparts = NULL;

    /**
     * Siteparts constructor.
     */
    private function __construct() {
    }

    /**
     * @return array|null
     */
    public static function get() {
        if (self::$siteparts === NULL) {
            $translation = \DynCom\dc\common\classes\Registry::get('translation');

            self::$siteparts = array(
                1  => array( // textcontent
                    'header_table' => 'textcontent_header',
                    'line_table'   => '',
                    'description'  => $translation->get('left_text'),
                    'class'        => 'inhalt_text',
                    'code'         => 'textcontent',
                    'folder'       => 'textcontent'
                ),

                2  => array( // slideshow
                    'header_table' => 'slideshow_header',
                    'line_table'   => 'slideshow_line',
                    'description'  => $translation->get('left_slideshows'),
                    'class'        => 'inhalt_slideshow',
                    'code'         => 'slideshow',
                    'folder'       => 'slideshow'
                ),

                3  => array( // Kontaktformular
                    'header_table' => 'contactform_header',
                    'line_table'   => 'contactform_line',
                    'description'  => $translation->get('left_contactforms'),
                    'class'        => 'inhalt_contact',
                    'code'         => 'contactform',
                    'folder'       => 'contactform'
                ),

                4  => array( // Accordion
                    'header_table' => 'slidecontent_header',
                    'line_table'   => 'slidecontent_line',
                    'description'  => $translation->get('left_slidecontent'),
                    'class'        => 'inhalt_accordion',
                    'code'         => 'slidecontent',
                    'folder'       => 'slidecontent'
                ),

                5  => array( // Dateigalerie
                    'header_table' => 'filegallery_header',
                    'line_table'   => 'filegallery_line',
                    'description'  => $translation->get('left_filecontent'),
                    'class'        => 'inhalt_files',
                    'code'         => 'filegallery',
                    'folder'       => 'filegallery'
                ),

                6  => array( // Bildergalerie
                    'header_table' => 'gallery_header',
                    'line_table'   => 'gallery_line',
                    'description'  => $translation->get('left_imagegallery'),
                    'class'        => 'inhalt_bildergalerie',
                    'code'         => 'gallery',
                    'folder'       => 'gallery'
                ),

                7  => array( // scrollleiste
                    'header_table' => 'scrollbar_header',
                    'line_table'   => 'scrollbar_line',
                    'description'  => $translation->get('left_scrollbars'),
                    'class'        => 'inhalt_scrollbar',
                    'code'         => 'magicscroll',
                    'folder'       => 'magicscroll'
                ),

                8  => array( // Google maps
                    'header_table' => 'google_maps_header',
                    'line_table'   => 'google_maps_line',
                    'description'  => $translation->get('left_googlemaps'),
                    'class'        => 'inhalt_maps',
                    'code'         => 'googlemaps',
                    'folder'       => 'googlemaps'
                ),

                9  => array( // Facebook Inhalte
                    'header_table' => 'facebook_header',
                    'line_table'   => '',
                    'description'  => $translation->get('left_facebook'),
                    'class'        => 'inhalt_facebook',
                    'code'         => 'facebook',
                    'folder'       => 'facebook'
                ),

                10 => array( // Youtube Inhalte
                    'header_table' => 'youtube_header',
                    'line_table'   => '',
                    'description'  => $translation->get('left_youtube'),
                    'class'        => 'inhalt_youtube',
                    'code'         => 'youtube',
                    'folder'       => 'youtube'
                ),

                11 => array( // Externe inhalte
                    'header_table' => 'iframe_header',
                    'line_table'   => '',
                    'description'  => $translation->get('left_extern'),
                    'class'        => 'inhalt_iframe',
                    'code'         => 'iframe',
                    'folder'       => 'iframe'
                ),

                15 => array( // Newsletter
                    'header_table' => 'newsletter_sitepart',
                    'line_table'   => '',
                    'description'  => $translation->get('left_newsletter_sitepart'),
                    'class'        => 'newsletter_sitepart',
                    'code'         => 'newsletter_sitepart',
                    'folder'       => 'newsletter'
                ),

                //Webshop Language Switching
                17 => array(
                    'header_table' => 'main_shop_language_switch',
                    'line_table'   => '',
                    'description'  => $translation->get('webshop_language_switch'),
                    'folder'       => 'languageswitch',
                    'class'        => 'language_switch',
                    'code'         => 'language_switch'
                )
            );
        }

        // Shopmenu
        static $instance;
        if ($instance === null) {
            $instance = new self();
        }
        $instance->create_shop_siteparts();

        return self::$siteparts;
    }

    protected function create_shop_siteparts() {
        $translation = \DynCom\dc\common\classes\Registry::get('translation');

        $query  = "SHOW TABLES LIKE 'shop_shop'";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 0) {
            return;                                //---> Shop nicht vorhanden
        }

        //Webshop Main
        self::$siteparts[12] = array(
            'header_table' => 'main_shop_sitepart',
            'line_table'   => '',
            'description'  => $translation->get('webshop'),
            'class'        => 'shop',
            'folder'       => 'dcshop/siteparts/main',
            'code'         => 'shop_main',
            'is_shop'      => TRUE
        );

        //Webshop Login External
        self::$siteparts[13] = array(
            'header_table' => 'main_shop_login',
            'line_table'   => '',
            'description'  => $translation->get('webshop_login'),
            'class'        => 'shop_account_login',
            'folder'       => 'dcshop/siteparts/login',
            'code'         => 'shop_login',
            'is_shop'      => TRUE
        );

        //Webshop Item Preview
        self::$siteparts[14] = array(
            'header_table' => 'main_shop_item_preview',
            'line_table'   => '',
            'description'  => $translation->get('webshop_item_preview'),
            'folder'       => 'dcshop/siteparts/item_preview',
            'class'        => 'shop_item_preview',
            'code'         => 'shop_item_preview',
            'is_shop'      => TRUE
        );
        //Webshop Top Items
        self::$siteparts[16] = array(
            'header_table' => 'main_shop_top_items',
            'line_table'   => '',
            'description'  => $translation->get('webshop_top_items'),
            'folder'       => 'dcshop/siteparts/top_items',
            'class'        => 'shop_top_items',
            'code'         => 'shop_top_items',
            'is_shop'      => TRUE
        );

        //Webshop Vendor Search
        self::$siteparts[18] = array(
            'header_table' => 'main_shop_dealer_search',
            'line_table'   => '',
            'description'  => $translation->get('dealer_search'),
            'folder'       => 'dcshop/siteparts/dealer_search',
            'class'        => 'shop_dealer_search',
            'code'         => 'shop_dealer_search',
            'is_shop'      => TRUE
        );

        //Webshop Vendor Search
        self::$siteparts[19] = array(
            'header_table' => 'main_shop_category_preview',
            'line_table'   => '',
            'description'  => $translation->get('category_preview'),
            'folder'       => 'dcshop/siteparts/category_preview',
            'class'        => 'shop_category_preview',
            'code'         => 'shop_category_preview',
            'is_shop'      => TRUE
        );


    }
}