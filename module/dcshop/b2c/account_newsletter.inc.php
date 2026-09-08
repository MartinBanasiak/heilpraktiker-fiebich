<?php
/**
 * Created by PhpStorm.
 * User: lorenz
 * Date: 14.07.2017
 * Time: 11:30
 */

$formname = 'newsletter_subscribe';
$formname2 = 'newsletter_unsubscribe';
?>

<div class="category_info">
    <h1 class="shop_site_headline"><?= $GLOBALS["tc"]["newsletter"] ?></h1>
</div>

<?php
if ($_POST["save_data"] == 1 && $_REQUEST["action_id"] === "subscribe") {
    $receivers = array();
    $receivers[0] = array(
        "email"                         =>      $GLOBALS["shop_user"]["email"],
        "activated"                     =>      0,
        "deactivated"                   =>      0,
        "registered"                    =>      time(),
        "source"                        =>      $GLOBALS["shop"]["newsletter_segment"],
        "global_attributes"	            =>      array(
            "languagecode"              =>      array(
                "value"                 =>      $GLOBALS['language']['code'],
                "type"                  =>      'text',
                "description"           =>      'Sprachcode',
            ),
            "name"                      =>      array(
                "value"                 =>      filter_var($_REQUEST["input_surname"],FILTER_SANITIZE_STRING) . "" . filter_var($_REQUEST["input_lastname"],FILTER_SANITIZE_STRING),
                "type"                  =>      'text',
                "description"           =>      'Segment',
            ),
            "first_name"                =>      array(
                "value"                 =>      filter_var($_REQUEST["input_surname"],FILTER_SANITIZE_STRING),
                "type"                  =>      'text',
                "description"           =>      'Segment',
            ),
            "last_name"                 =>      array(
                "value"                 =>      filter_var($_REQUEST["input_lastname"],FILTER_SANITIZE_STRING),
                "type"                  =>      'text',
                "description"           =>      'Segment',
            ),
            "birthday"                  =>      array(
                "value"                 =>      filter_var($_REQUEST["input_birthday"],FILTER_SANITIZE_STRING),
                "type"                  =>      'text',
                "description"           =>      'Segment',
            ),
            "post_code"                 =>      array(
                "value"                 =>      filter_var($_REQUEST["input_post_code"],FILTER_SANITIZE_STRING),
                "type"                  =>      'text',
                "description"           =>      'Segment',
            ),
            "city"                      =>      array(
                "value"                 =>      filter_var($_REQUEST["input_city"],FILTER_SANITIZE_STRING),
                "type"                  =>      'text',
                "description"           =>      'Segment',
            ),
        ),
        "attributes"	                =>      array(
            "segment"                   =>      array(
                "value"                 =>      $GLOBALS["shop"]["newsletter_segment"],
                "type"                  =>      'text',
                "description"           =>      'Segment',
            ),
        ),
    );

    $cleverReachConnector = new \DynCom\dc\common\classes\CleverReachConnector(
        $GLOBALS["shop"]["newsletter_account"],
        $GLOBALS["shop"]["newsletter_login"],
        $GLOBALS["shop"]["newsletter_password"],
        $GLOBALS["shop"]["newsletter_group"]
    );

    if (!$cleverReachConnector->upsertReceivers($receivers) || $cleverReachConnector->isError()) {
        get_requestbox($GLOBALS["tc"]["newsletter_no_response_error"],$GLOBALS["tc"]["error"],'error',true);
    } else {
        get_requestbox($GLOBALS["tc"]["newsletter_subscribe_success"],$GLOBALS["tc"]["success"],'success',true);
    }
} elseif ($_POST["save_data"] == 1 && $_REQUEST["action_id"] === "unsubscribe") {

    $receivers = array();
    $receivers[0] = array(
        "email"                         =>      $GLOBALS["shop_user"]["email"],
    );

    $cleverReachConnector = new \DynCom\dc\common\classes\CleverReachConnector(
        $GLOBALS["shop"]["newsletter_account"],
        $GLOBALS["shop"]["newsletter_login"],
        $GLOBALS["shop"]["newsletter_password"],
        $GLOBALS["shop"]["newsletter_group"]
    );

    if (!$cleverReachConnector->unsubscrineReceivers($receivers) || $cleverReachConnector->isError()) {
        get_requestbox($GLOBALS["tc"]["newsletter_no_response_error"],$GLOBALS["tc"]["error"],'error',true);
    } else {
        get_requestbox($GLOBALS["tc"]["newsletter_unsubscribe_success"],$GLOBALS["tc"]["success"],'success',true);
    }
}
?>

<h2><?=$GLOBALS['tc']['subscribe']?></h2>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post" class="form-label-left">
    <div class="row">
        <div class="col-xs-12 col-sm-8 col-lg-6">

            <? input_shop($GLOBALS["tc"]["salutation"], "input_salutation", "hidden", $GLOBALS["shop_customer"]["salutation"],null) ?>
            <? input_shop($GLOBALS["tc"]["surname"], "input_surname", "hidden", $GLOBALS["shop_customer"]["surname"],null) ?>
            <? input_shop($GLOBALS["tc"]["lastname"], "input_lastname", "hidden", $GLOBALS["shop_customer"]["lastname"],null) ?>
            <? input_shop($GLOBALS["tc"]["email"], "input_email", "hidden", $GLOBALS["shop_customer"]["email"],null) ?>
            <? input_shop($GLOBALS["tc"]["post_code"], "input_post_code", "hidden", $GLOBALS["shop_customer"]["post_code"],null) ?>
            <? input_shop($GLOBALS["tc"]["city"], "input_city", "hidden", $GLOBALS["shop_customer"]["city"],null) ?>

            <? input_shop($GLOBALS["tc"]["salutation"], "input_salutation_", "text", $GLOBALS["shop_customer"]["salutation"],null,true) ?>
            <? input_shop($GLOBALS["tc"]["surname"], "input_surname_", "text", $GLOBALS["shop_customer"]["surname"],null,true) ?>
            <? input_shop($GLOBALS["tc"]["lastname"], "input_lastname_", "text", $GLOBALS["shop_customer"]["lastname"],null,true) ?>
            <? input_shop($GLOBALS["tc"]["email"], "input_email_", "text", $GLOBALS["shop_customer"]["email"],null,true) ?>
            <? input_shop($GLOBALS["tc"]["post_code"], "input_post_code_", "text", $GLOBALS["shop_customer"]["post_code"],null,true) ?>
            <? input_shop($GLOBALS["tc"]["city"], "input_city_", "text", $GLOBALS["shop_customer"]["city"],null,true) ?>
            <? input_shop($GLOBALS["tc"]["birthday"], "input_birthday", "date", $GLOBALS["shop_customer"]["birthday"]) ?>

            <input type="hidden" name="save_data" value="1" />
        </div>
    </div>
    <div class="button_row">
        <?= button("save button button_action", $GLOBALS["tc"]["subscribe"], $formname, "?action=newsletter&action_id=subscribe"); ?>
    </div>
</form>

<h2><?=$GLOBALS['tc']['unsubscribe']?></h2>
<form id="<?= $formname2 ?>" name="<?= $formname2 ?>" method="post" class="form-label-left">
    <div class="row">
        <div class="col-xs-12 col-sm-8 col-lg-6">
            <? input_shop($GLOBALS["tc"]["email"], "input_email", "text", $GLOBALS["shop_customer"]["email"],null,true) ?>
            <input type="hidden" name="save_data" value="1" />
        </div>
    </div>
    <div class="button_row">
        <?= button("save button button_action", $GLOBALS["tc"]["unsubscribe"], $formname2, "?action=newsletter&action_id=unsubscribe"); ?>
    </div>
</form>

