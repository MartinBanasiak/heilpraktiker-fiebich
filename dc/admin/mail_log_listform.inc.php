<?php
$translation = \DynCom\dc\common\classes\Registry::get("translation");
$formname    = "form_mail_log_list";

$zeitraum_values = array(
    "1 WEEK",
    "1 MONTH",
    "1 YEAR",
    "100 YEAR"
);

$zeitraum_names = array(
    $translation->get("last_week"),
    $translation->get("last_month"),
    $translation->get("last_year"),
    $translation->get("show_all")
);

?>

<script type="text/javascript">
    function filter_mails( _days ) {
        jQuery('#filter_days').val(_days);
        jQuery('#form_mail_log_list').submit();
    }
</script>

<ul class="toolbar_menu">
    <?= button("edit", $translation->get("display"), $formname, "loadCard('card')"); ?>
    <?= button("mail", $translation->get("send_again"), $formname, "jQuery('#filter_days').val(jQuery('#page_filter').val());sendRequest('send_list')"); ?>
</ul>

<div id="mainContent">
    <?php echo current_website_language($site, $language); ?>
    <h1><?php echo get_translation('left_mail_stat'); ?></h1>

    <h3><?php echo $translation->get("show_mails_till"); ?>:</h3>
    <?php
    mail_filter($GLOBALS["site"]['id'], $GLOBALS["language"]['id']);
    ?>
    <div class="clearfix"></div>

    <form id="<?= $formname ?>" name="<?= $formname ?>" method="post" action="?action=filter">
        <input type="hidden" value="" name="filter_days" id="filter_days" />
        <input type="hidden" class="selected_linklist_row" name="input_id" value="" />

        <div class="requestLoader"></div>
        <?
        if (isset($_POST['filter_days'])) {
            $where = $_POST['filter_days'];
        } else {
            $where = '1 WEEK';
        }

        $query  = "SELECT
			id, 
			subject AS '" . $translation->get("subject") . "', 
			DATE_FORMAT(send_date,'%d.%m.%Y um %H:%i Uhr') AS '" . $translation->get("send_on") . "', 
			to_adress AS '" . $translation->get("recipient_email") . "', 
			from_adress AS '" . $translation->get("sender_email") . "' 
		FROM
			main_mail_log where send_date >= DATE_SUB(NOW(),INTERVAL " . $where . ")
		ORDER BY 
			send_date DESC";
        $format = array('option', 'text', 'text', 'text', 'text');
        if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
            linklist($result, $formname, $format, "input_id", "card");
        }
        ?>

    </form>

</div>