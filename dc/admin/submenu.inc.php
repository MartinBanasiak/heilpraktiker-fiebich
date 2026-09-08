<?php
$adminMenu = \DynCom\dc\common\classes\Adminmenu::get();

$subsites = array();
if ($_GET["level_1"] != "") {
    $subsites = $adminMenu[$_GET["level_1"]]['subsites'];
}
?>

<?php if (count($subsites)): ?>
    <ul>
        <?php foreach ($subsites as $code => $subsite): ?>
            <li>
                <?php
                $active = '';
                if ($_GET["level_2"] != "" && $_GET["level_2"] == $code) {
                    $active = 'class="active"';
                }
                ?>
                <a <?php echo $active; ?>
                    href="<?php echo get_menu_link($_GET["level_1"] . "/" . $code, $site, $language); ?>"><?php echo $subsite['name']; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>