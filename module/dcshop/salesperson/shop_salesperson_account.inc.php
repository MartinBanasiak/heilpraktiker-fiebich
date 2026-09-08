<div class="infobar">
    <?= $GLOBALS["tc"]["my_account"] ?>
</div>
<div class="shop_category_2">
    <ul>
        <li><?= ($_GET["action"] <> "edit_salesperson") ? "<a href=\"" . ml("", "action", "edit_salesperson") . "\">" . $GLOBALS["tc"]["salesperson_account"] . "</a>" : "<strong>" . $GLOBALS["tc"]["salesperson_account"] . "</strong>"; ?></li>
        <li><?= ($_GET["action"] <> "customer_list") ? "<a href=\"" . ml("", "action", "customer_list") . "\">" . $GLOBALS["tc"]["customer_list"] . "</a>" : "<strong>" . $GLOBALS["tc"]["customer_list"] . "</strong>"; ?></li>
    </ul>
</div>
<?
switch ($_GET["action"]) {
    case "edit_salesperson":
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_salesperson.inc.php';
        break;
    case "customer_list":
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'customer_list.inc.php';
        break;
}
?>
