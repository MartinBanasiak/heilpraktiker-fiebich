<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<?php require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'dc/common/version_comment.inc.php'; ?>
<?php require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'dc/common/common_functions.inc.php'; ?>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?= site_title($language, $navigation) ?></title>
    <meta name="Keywords" content="<?= $language["meta_keywords"] ?>" />
    <meta name="Description" content="<?= $language["meta_description"] ?>" />
    <meta name="Content-language" content="<?= $language["code"] ?>" />
    <meta name="Robots" content="INDEX,FOLLOW" />
    <link rel="shortcut icon" href="<?= $GLOBALS['projectRoot'] ?>/<?= $GLOBALS["layout"]["favicon_include"]; ?>" type="image/x-icon" />
    <?
    //create_includes($GLOBALS["layout"]["id"],$GLOBALS["navigation"]["id"],'css');
    //create_includes($GLOBALS["layout"]["id"],$GLOBALS["navigation"]["id"],'js');
    ?>
</head>
<body>
<div id="spacer_1"><a href="javascript:void(0);"
                      onclick="toggleOn('login_content_3');toggleOff('login_content_1');toggleOff('login_content_2');"><?= $GLOBALS["tc"]["imprint"] ?></a>
</div>
<div id="spacer_2"></div>
<div id="spacer_3"></div>
<div id="login_container">
    <div id="login_container_1"></div>
    <div id="login_container_2">
        <div id="login_content">
            <div id="login_content_1"><? get_content("content", 14); ?></div>
            <div id="login_content_2"><? get_content("content", 13); ?></div>
            <div id="login_content_3"><? get_content("content", 15); ?></div>
        </div>
        <div id="login_footer">
            <div id="login_footer_1"></div>
            <div id="login_footer_2">
                <strong><?= $GLOBALS["tc"]["reseller_login"] ?></strong><br />

                <div class="spacer_6"></div>
                <form id="form_shop_login" name="form_shop_login" method="post" action="?action=shop_login">
                    <div class="label"><label for="input_customer_no"><?= $GLOBALS["tc"]["customer_no"] ?>:</label>
                    </div>
                    <div class="input"><input name="input_customer_no" type="text" class="code" id="input_customer_no"
                                              maxlength="45" /></div>
                    <div class="label"><label for="input_login"><?= $GLOBALS["tc"]["username"] ?>:</label></div>
                    <div class="input"><input type="text" class="code" name="input_login" id="input_login"
                                              maxlength="45" /></div>
                    <div class="label"><label for="input_password"><?= $GLOBALS["tc"]["password"] ?>:</label></div>
                    <div class="input"><input type="password" class="code" name="input_password" id="input_password"
                                              maxlength="45" /></div>
                    <div class="label"><label for="imageField"></label></div>
                    <div class="input"><input type="image" name="imageField" id="imageField"
                                              src="<?= $GLOBALS['projectRoot'] ?>/layout/frontend/shop/img/button_login.jpg" /></div>
                </form>
            </div>
            <div id="login_footer_3">
                <ul>
                    <li><a href="http://www.ihnen-consulting.com"><?= $GLOBALS["tc"]["not_a_reseller"] ?></a></li>
                    <li><a href="javascript:void(0);"
                           onclick="toggleOn('login_content_1');toggleOff('login_content_2');toggleOff('login_content_3');"><?= $GLOBALS["tc"]["not_registered"] ?></a>
                    </li>
                    <li><a href="javascript:void(0);"
                           onclick="toggleOn('login_content_2');toggleOff('login_content_1');toggleOff('login_content_3');"><?= $GLOBALS["tc"]["forgot_password"] ?></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div id="login_container_3"></div>
</div>
</body>
</html>
<? require_once __DIR__ . DIRECTORY_SEPARATOR . 'close.inc.php'; ?>