<?php
namespace DynCom\dc\common\classes;
/**
 * Class Adminmenu
 * @package DynCom\dc\common\classes
 */
class Adminmenu {

    private static $adminMenu = NULL;

    /**
     * Adminmenu constructor.
     */
    private function __construct() {
    }

    /**
     * @param int $_active
     * @return array|null
     */
    public static function get($_active = 0 ) {
        if (self::$adminMenu === NULL) {
            $translation = \DynCom\dc\common\classes\Registry::get('translation');

            self::$adminMenu = array(

                'default'     => array(
                    'name'         => $translation->get('top_structure'),
                    'include'      => CMS_PATH . "admin/dashboard_structure.php",
                    'linklistmenu' => FALSE,
                ),

                // kollektionen
                'collections' => array(
                    'name'         => $translation->get('top_collections'),
                    'include'      => MODULE_PATH . "collection/dashboard_collections.php",
                    'linklistmenu' => FALSE
                ),

                // Struktur
                'structure'   => array(
                    'name'         => $translation->get('top_structure'),
                    'include'      => CMS_PATH . "admin/dashboard_structure.php",
                    'linklistmenu' => FALSE,
                    'subsites'     => array(

                        'sites'      => array(
                            'name'         => $translation->get('left_sites'),
                            'include'      => CMS_PATH . "admin/edit_page.inc.php",
                            'linklistmenu' => TRUE,
                            'icon'         => 'seiten.png'
                        ),

                        /*'shoppingworld'      => array(
                            'name'         => $translation->get('left_shoppingworld'),
                            'include'      => CMS_PATH . "admin/edit_page.inc.php",
                            'linklistmenu' => TRUE,
                            'icon'         => 'seiten.png'
                        ),*/

                        'navigation' => array(
                            'name'         => $translation->get('left_navigation'),
                            'include'      => CMS_PATH . "admin/edit_main_navigation.inc.php",
                            'linklistmenu' => TRUE,
                            'icon'         => 'navigation.png'
                        ),

                        'files'      => array(
                            'name'         => $translation->get('left_files'),
                            'include'      => CMS_PATH . "admin/edit_files.inc.php",
                            'linklistmenu' => TRUE,
                            'icon'         => 'dateien.png'
                        ),

                        'components' => array(
                            'name'         => $translation->get('left_components'),
                            'include'      => CMS_PATH . "admin/edit_component.inc.php",
                            'linklistmenu' => TRUE,
                            'icon'         => 'bausteine.png'
                        ),

                        /*'templates' => array(
                            'name' => $translation->get('left_templates'),
                            'include'      => CMS_PATH . "admin/edit_page.inc.php",
                            'linklistmenu' => TRUE,
                            'icon'         => 'seiten.png'
                        ),*/

                    )
                ),

                // inhalte
                'contents'    => array(
                    'name'         => $translation->get('top_contents'),
                    'include'      => CMS_PATH . "admin/dashboard_contents.php",
                    'linklistmenu' => FALSE,
                    'subsites'     => array(
                        'textcontent'  => array(
                            'name'                  => $translation->get('left_text'),
                            'include'               => MODULE_PATH . "textcontent/textcontent.php",
                            'admin_start_parameter' => "textcontent_edit",
                            'linklistmenu'          => TRUE
                        ),

                        'slideshow'    => array(
                            'name'                  => $translation->get('left_slideshows'),
                            'include'               => MODULE_PATH . "slideshow/slideshow.php",
                            'admin_start_parameter' => "slideshow_edit",
                            'linklistmenu'          => TRUE
                        ),

                        'contactform'  => array(
                            'name'                  => $translation->get('left_contactforms'),
                            'include'               => MODULE_PATH . "contactform/contactform.php",
                            'admin_start_parameter' => "contactform_edit",
                            'linklistmenu'          => TRUE
                        ),

                        'slidecontent' => array(
                            'name'                  => $translation->get('left_slidecontent'),
                            'include'               => MODULE_PATH . "slidecontent/slidecontent.php",
                            'admin_start_parameter' => "slidecontent_edit",
                            'linklistmenu'          => TRUE
                        ),

                        'filegallery'  => array(
                            'name'                  => $translation->get('left_filecontent'),
                            'include'               => MODULE_PATH . "filegallery/filegallery.php",
                            'admin_start_parameter' => "filegallery_edit",
                            'linklistmenu'          => TRUE
                        ),

                        'gallery'      => array(
                            'name'                  => $translation->get('left_imagegallery'),
                            'include'               => MODULE_PATH . "gallery/gallery.php",
                            'admin_start_parameter' => "gallery_edit",
                            'linklistmenu'          => TRUE
                        ),


                        'magicscroll'  => array(
                            'name'                  => $translation->get('left_scrollbars'),
                            'include'               => MODULE_PATH . "magicscroll/magicscroll.php",
                            'admin_start_parameter' => "magicscroll_edit",
                            'linklistmenu'          => TRUE
                        ),


                        'googlemaps'   => array(
                            'name'                  => $translation->get('left_googlemaps'),
                            'include'               => MODULE_PATH . "googlemaps/googlemaps.php",
                            'admin_start_parameter' => "googlemaps_edit",
                            'linklistmenu'          => TRUE
                        ),

                        'facebook'     => array(
                            'name'                  => $translation->get('left_facebook'),
                            'include'               => MODULE_PATH . "facebook/facebook.php",
                            'admin_start_parameter' => "facebook_edit",
                            'linklistmenu'          => TRUE
                        ),

                        'youtube'      => array(
                            'name'                  => $translation->get('left_youtube'),
                            'include'               => MODULE_PATH . "youtube/youtube.php",
                            'admin_start_parameter' => "youtube_edit",
                            'linklistmenu'          => TRUE
                        ),

                        'iframe'       => array(
                            'name'                  => $translation->get('left_extern'),
                            'include'               => MODULE_PATH . "iframe/iframe.php",
                            'admin_start_parameter' => "iframe_edit",
                            'linklistmenu'          => TRUE
                        ),

                    )
                ),

                // Statistik
                'statistics'  => array(
                    'name'         => $translation->get('top_statistic'),
                    'include'      => CMS_PATH . "admin/statistic.inc.php",
                    'linklistmenu' => FALSE,
                    'subsites'     => array(
                        'mail_stat'   => array(
                            'name'         => $translation->get('left_mail_stat'),
                            'include'      => CMS_PATH . "admin/mail_log.inc.php",
                            'linklistmenu' => TRUE
                        ),

                        'access_stat' => array(
                            'name'         => $translation->get('left_access_stat'),
                            'include'      => CMS_PATH . "admin/statistic_access.inc.php",
                            'linklistmenu' => TRUE
                        )
                    )
                ),

                // Konfiguration
                'config'      => array(
                    'name'     => '<img src="' . $GLOBALS['projectRoot'] . '/layout/admin/img/2015/settings.png" />',
                    'subsites' => array(
                        'config_websites'    => array(
                            'name'         => $translation->get('top_websites'),
                            'include'      => CMS_PATH . "admin/edit_site.inc.php",
                            'linklistmenu' => TRUE
                        ),

                        'config_language'    => array(
                            'name'    => $translation->get('top_languages'),
                            'include' => CMS_PATH . "admin/edit_language.inc.php",
                        ),

                        'config_users'       => array(
                            'name'         => $translation->get('top_users'),
                            'include'      => CMS_PATH . "admin/edit_admin_user.inc.php",
                            'linklistmenu' => FALSE
                        ),

                        'config_layouts'     => array(
                            'name'         => $translation->get('top_layouts'),
                            'include'      => CMS_PATH . "admin/edit_layout.inc.php",
                            'linklistmenu' => TRUE
                        ),

                        'config_collections' => array(
                            'name'                  => $translation->get('top_collections'),
                            'include'               => MODULE_PATH . "collection/collection.php",
                            'admin_start_parameter' => "collection_edit_setup",
                            'linklistmenu'          => TRUE
                        ),
                        'config_url_management' => array(
                            'name'                  => $translation->get('url_management'),
                            'include'               => CMS_PATH . "admin/edit_url_management.inc.php",
                        ),
                        'config_sitemap' => array(
                            'name'                  => $translation->get('sitemap'),
                            'include'               => CMS_PATH . "admin/sitemap_generating.inc.php",
                        ),
                    )
                ),

                // Hilfe
                'help'        => array(
                    'name'     => '<img src="' . $GLOBALS['projectRoot'] . '/layout/admin/img/2015/help.png" />',
                    'subsites' => array(
                        'help_license'    => array(
                            'name'         => $translation->get('top_license'),
                            'include'      => CMS_PATH . "admin/licence.inc.php",
                            'linklistmenu' => FALSE
                        ),

                        'help_systeminfo' => array(
                            'name'         => $translation->get('top_systeminfo'),
                            'include'      => CMS_PATH . "admin/phpinfo.php",
                            'linklistmenu' => FALSE
                        ),

                        'help_help'       => array(
                            'name'         => $translation->get('top_help'),
                            'include'      => CMS_PATH . "admin/help.php",
                            'linklistmenu' => FALSE
                        ),
                    )
                )

            );
        }

        if ($GLOBALS['admin_user']['right_create_user'] == 1 | $GLOBALS['admin_user']['is_super_user'] == 1) {
            self::$adminMenu['config']['subsites']['config_users']['linklistmenu'] = TRUE;
        }

        // kollektionsmenu
        self::create_collections_menu();

        // Shopmenu
        self::create_shop_menu();


        return self::$adminMenu;
    }

    protected function create_collections_menu() {
        $query  = "SELECT * FROM main_collection_setup WHERE (main_language_id = " . (int)$GLOBALS["language"]['id'] . " OR all_languages = 1)";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 0) {
            return;
        }

        self::$adminMenu['collections']['subsites'] = array();
        while ($row = @mysqli_fetch_array($result)) {
            self::$adminMenu['collections']['subsites'][$row['id']] = array(
                'name'                  => $row['description'],
                'include'               => MODULE_PATH . "collection/collection.php",
                'admin_start_parameter' => "collection_edit",
                'linklistmenu'          => TRUE
            );
        }
    }

    protected function create_shop_menu() {
        $translation = \DynCom\dc\common\classes\Registry::get('translation');

        $query  = "SHOW TABLES LIKE 'shop_shop'";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 0) {
            return;                                //---> Shop nicht vorhanden
        }

        /*self::$adminMenu['statistics']['subsites']["order_stat"] = array(
            'name'         => $translation->get('left_order_stat'),
            'include'      => CMS_PATH . "admin/order_statistic.inc.php",
            'linklistmenu' => TRUE
        );

        self::$adminMenu['statistics']['subsites']["search_stat"] = array(
            'name'         => $translation->get('left_search_stat'),
            'include'      => CMS_PATH . "admin/search_query_statistic.inc.php",
            'linklistmenu' => TRUE
        );*/
    }
}
