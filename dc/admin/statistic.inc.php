<?
$messages    = array();
$translation = \DynCom\dc\common\classes\Registry::get("translation");

$action = (isset($_GET['action']) ? $_GET['action'] : "");

if ($action != "" && $action != "admin_login") {
    require_once(CMS_PATH . "admin/mail_log.inc.php");
    exit();
}

?>
<? $formname = "form_stat"; ?>
<div id="mainContent">
    <?php echo current_website_language($site, $language); ?>
    <h1><?php echo get_translation('top_statistic'); ?></h1>

    <?php
    if (count($messages)) {
        echo '<div id="overlayMessages">' . join("\r\n", $messages) . '</div>';
    }

    ?>

    <h2><?php echo get_translation('left_mail_stat'); ?></h2>

    <form id="<?= $formname ?>" name="<?= $formname ?>" method="post" action="?action=filter">
        <input type="hidden" class="selected_linklist_row" name="input_id" value="" />

        <div class="requestLoader"></div>
        <?

        $where = 'where send_date >= DATE_SUB(NOW(),INTERVAL 7 DAY)';

        $query  = "SELECT id, subject AS '" . $translation->get("subject") . "', DATE_FORMAT(send_date,'%d.%m.%Y um %H:%i Uhr') AS '" . $translation->get("send_on") . "', to_adress AS '" . $translation->get("recipient_email") . "', from_adress AS '" . $translation->get("sender_email") . "' FROM main_mail_log " . $where . " ORDER BY send_date DESC";
        $format = array('option', 'text', 'text', 'text', 'text');
        if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
            linklist($result, $formname, $format, "input_id", "card");
        }
        ?>

    </form>
</div>