<?php
$translation = \DynCom\dc\common\classes\Registry::get('translation');
$adminMenu   = \DynCom\dc\common\classes\Adminmenu::get(0);

?>

<nav>
    <ul>
        <?php if (collection_exists()): ?>
            <li class="structure_nav">
                <a href="<?php echo get_menu_link('collections', $site, $language); ?>"
                   class="<?php echo (isset($_GET["level_1"]) && $_GET["level_1"] == "collections") ? 'active' : ''; ?>"><?php echo $adminMenu['collections']['name']; ?></a>
            </li>
        <?php endif; ?>
        <li class="structure_nav">
            <a href="<?php echo get_menu_link('structure', $site, $language); ?>"
               class="<?php echo (isset($_GET["level_1"]) && $_GET["level_1"] == "structure") ? 'active' : ''; ?>"><?php echo $adminMenu['structure']['name']; ?></a>
        </li>
        <li class="structure_nav">
            <a href="<?php echo get_menu_link('contents', $site, $language); ?>"
               class="<?php echo (isset($_GET["level_1"]) && $_GET["level_1"] == "contents") ? 'active' : ''; ?>"><?php echo $adminMenu['contents']['name']; ?></a>
        </li>
        <li class="structure_nav">
            <a href="<?php echo get_menu_link('statistics', $site, $language); ?>"
               class="<?php echo (isset($_GET["level_1"]) && $_GET["level_1"] == "statistics") ? 'active' : ''; ?>"><?php echo $adminMenu['statistics']['name']; ?></a>
        </li>

        <li class="topmenu gray_arrow" id="site_language_menu">

            <?php
            $siteLanguage = get_site_language_menu($site, $language);
            echo $siteLanguage['selectedSite'];
            ?>

            <ul class="site_dropdown">

                <?php echo $siteLanguage['siteLanguageMenu']; ?>

                <li class="spacer">&nbsp;</li>
                <li>
                    <a href="<?php echo get_menu_link('config/config_websites', $site, $language); ?>"><?php echo $translation->get('top_config_websites'); ?></a>
                </li>
                <li>
                    <a href="<?php echo get_menu_link('config/config_language', $site, $language); ?>"><?php echo $translation->get('top_config_languages'); ?></a>
                </li>
            </ul>
        </li>

        <li class="topmenu gray_arrow" id="user_menu">
            <a href="#"><?php echo $admin_user["name"]; ?></a>
            <ul>
                <li class="menu_userdata">
                    <a href="<?php echo get_menu_link('config/config_users', $site, $language, "?action=open_card&input_id=" . $admin_user['id']); ?>"><?php echo $admin_user["name"]; ?>
                        <br /><?php echo $admin_user["email"]; ?></a>
                </li>

                <li class="spacer">&nbsp;</li>

                <li class="menu_link_userdata">
                    <a href="<?php echo get_menu_link('config/config_users', $site, $language, "?action=open_card&input_id=" . $admin_user['id']); ?>"><?php echo $translation->get('top_userdata'); ?></a>
                </li>
                <li class="menu_link_logout">
                    <a href="?action=admin_logout"><?php echo $translation->get('top_logout'); ?></a>
                </li>

                <li class="spacer">&nbsp;</li>

                <li>
                    <a href="<?php echo get_menu_link('config/config_users', $site, $language); ?>"><?php echo $translation->get('top_user_settings'); ?></a>
                </li>
            </ul>
        </li>

        <li class="topmenu" id="settings_menu">
            <a href="#"><img src="<?= $GLOBALS['projectRoot'] ?>/layout/admin/img/2015/settings.png" /></a>
            <ul>
                <li>
                    <a href="<?php echo get_menu_link('config/config_websites', $site, $language); ?>"><?php echo $translation->get('top_websites'); ?></a>
                </li>

                <li>
                    <a href="<?php echo get_menu_link('config/config_language', $site, $language); ?>"><?php echo $translation->get('top_languages'); ?></a>
                </li>

                <li>
                    <a href="<?php echo get_menu_link('config/config_users', $site, $language); ?>"><?php echo $translation->get('top_users'); ?></a>
                </li>

                <li>
                    <a href="<?php echo get_menu_link('config/config_layouts', $site, $language); ?>"><?php echo $translation->get('top_layouts'); ?></a>
                </li>

                <li>
                    <a href="<?php echo get_menu_link('config/config_collections', $site, $language); ?>"><?php echo $translation->get('top_collections'); ?></a>
                </li>

                <li>
                    <a href="<?php echo get_menu_link('config/config_url_management', $site, $language); ?>"><?php echo $translation->get('url_management'); ?></a>
                </li>
                <li>
                    <a href="<?php echo get_menu_link('config/config_sitemap', $site, $language); ?>"><?php echo $translation->get('sitemap'); ?></a>
                </li>
            </ul>
        </li>


        <li class="topmenu" id="help_menu">
            <a href="#"><img src="<?= $GLOBALS['projectRoot'] ?>/layout/admin/img/2015/help.png" /></a>
            <ul>
                <li>
                    <a href="<?php echo get_menu_link('help/help_license', $site, $language); ?>"><?php echo $translation->get('top_license'); ?></a>
                </li>

                <li>
                    <a href="<?php echo get_menu_link('help/help_systeminfo', $site, $language); ?>"><?php echo $translation->get('top_systeminfo'); ?></a>
                </li>

                <li>
                    <a href="<?php echo get_menu_link('help/help_help', $site, $language); ?>"><?php echo $translation->get('top_help'); ?></a>
                </li>
            </ul>
        </li>
    </ul>
</nav>
