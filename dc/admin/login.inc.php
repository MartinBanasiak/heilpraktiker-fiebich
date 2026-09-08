<!DOCTYPE html>
<html lang="de">
<? require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'version_comment.inc.php'; ?>
<?php
$translation = \DynCom\dc\common\classes\Registry::get('translation');
?>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo $translation->get('backend_login_title'); ?></title>
    <link href="<?= $GLOBALS['projectRoot'] ?>/layout/admin/css/fonts.css" rel="stylesheet" type="text/css" />
    <link href="<?= $GLOBALS['projectRoot'] ?>/layout/admin/css/dc.css" rel="stylesheet" type="text/css" />
    <link href="<?= $GLOBALS['projectRoot'] ?>/layout/admin/css/login.css" rel="stylesheet" type="text/css" />

    <link rel="apple-touch-icon" sizes="180x180" href="<?= $GLOBALS['projectRoot'] ?>/layout/admin/img/2016/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="<?= $GLOBALS['projectRoot'] ?>/layout/admin/img/2016/favicons/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="<?= $GLOBALS['projectRoot'] ?>/layout/admin/img/2016/favicons/favicon-16x16.png" sizes="16x16">
    <link rel="manifest" href="<?= $GLOBALS['projectRoot'] ?>/layout/admin/img/2016/favicons/manifest.json">
    <link rel="mask-icon" href="<?= $GLOBALS['projectRoot'] ?>/layout/admin/img/2016/favicons/safari-pinned-tab.svg" color="#99c137">
    <meta name="theme-color" content="#ffffff">
</head>
<body>
<div id="container">
    <div id="container_1">

    </div>
    <div id="container_2">
        <div id="header">
            <div id="header_1">
                <h1>
                    <?= htmlspecialchars($site["name"], ENT_QUOTES, "UTF-8") ?>
                </h1>
            </div>
            <div id="header_2"></div>
            <div id="header_3"></div>
        </div>
        <div id="login">
            <div id="login_1"><?php echo $translation->get('backend_login_headline'); ?></div>
            <?

            $falseLoginData = get_false_password_admin_login_counter(md5(getUserIP()),"");
            $now = time();
            $dataBaseCounter = 0;
            $blockTime = $now;
            $blockErrorCounter = 0;

            if(count($falseLoginData) > 0)
            {
                $dataBaseCounter = $falseLoginData[0]['total_counter'];
                $blockTime = $falseLoginData[0]['next_login_time'];
                $blockErrorCounter = $falseLoginData[0]['block_counter'];;
            }
            $allowedFalseLoginTimes = !empty($GLOBALS['shop_setup']['allowed_false_login_times']) && (is_int($GLOBALS['shop_setup']['allowed_false_login_times']) || ctype_digit($GLOBALS['shop_setup']['allowed_false_login_times'])) && $GLOBALS['shop_setup']['allowed_false_login_times'] > 1 ? (int)$GLOBALS['shop_setup']['allowed_false_login_times'] : 1;
            $counter = $dataBaseCounter / $allowedFalseLoginTimes;
            $counter = (int)$counter;
            $waitingSeconds = $counter * $GLOBALS['shop_setup']['false_login_waiting_seconds'];

            if($counter != $blockErrorCounter)
            {
                $blockTime =  $now + $waitingSeconds;
                add_false_password_admin_loign_counter(md5(getUserIP()), "", $dataBaseCounter ,$blockTime,  $counter);
            }


            if( $blockTime >  $now)
            {
                $message = str_replace('%number_of_times%',$dataBaseCounter ,$translation->get('reach_false_login_times'));
                $message = str_replace('%minutes_number%',((int)$waitingSeconds/60),$message);
                echo '<h2>'.$message.'</h2>';

            }
            else
            {
                ?>

                <div id="login_2">
                    <form name="form_admin_login" id="form_admin_login" action="?action=admin_login" method="post">
                        <div class="label"><label for="input_login"><?php echo $translation->get('backend_login_label'); ?>
                                :</label></div>
                        <div class="input"><input class="code" type="text" name="input_login" id="input_login" /></div>
                        <div class="label"><label
                                    for="input_password"><?php echo $translation->get('backend_password_label'); ?>:</label>
                        </div>
                        <div class="input"><input class="code" name="input_password" type="password" id="input_password" />
                        </div>
                        <input class="button" name="" type="submit"
                               value="<?php echo $translation->get('backend_login_button'); ?>" />
                    </form>
                </div>




                <?
            }
            ?>





            <div id="login_3"></div>
        </div>
    </div>
    <div id="container_3"></div>
</div>
</body>
</html>