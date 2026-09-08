<?php
$translation = \DynCom\dc\common\classes\Registry::get("translation");
?>
<h2><?php echo $translation->get("contactform_options"); ?></h2>
<table class="cardform" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td>
            <? input($translation->get("subject"), "collection_setup_input_subject", "text", $sitepart_options["collection_setup_input_subject"], 255) ?>
        </td>

        <td>

        </td>
    </tr>
    <tr>
        <td>
            <? input($translation->get("sender_name"), "collection_setup_sender_name", "text", $sitepart_options["collection_setup_sender_name"], 255) ?>
            <? input($translation->get("sender_email"), "collection_setup_sender_email", "text", $sitepart_options["collection_setup_sender_email"], 255) ?>

        </td>
        <td>
            <? input($translation->get("recipient_name"), "collection_setup_recipient_name", "text", $sitepart_options["collection_setup_recipient_name"], 255) ?>
            <? input($translation->get("recipient_email"), "collection_setup_recipient_email", "text", $sitepart_options["collection_setup_recipient_email"], 255) ?>
        </td>
    </tr>
</table>