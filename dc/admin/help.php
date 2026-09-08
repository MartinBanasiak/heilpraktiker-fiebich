<?php
$translation = \DynCom\dc\common\classes\Registry::get("translation");
?>

<div id="mainContent">
    <?php echo current_website_language($site, $language); ?>
    <h1><?php echo get_translation('top_help'); ?></h1>

    <form id="<?= $formname ?>" name="<?= $formname ?>" method="post">

        <table class="cardform" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td>
                    <h2><?php echo $translation->get("support"); ?></h2>
                    <p>
                        <?php echo $translation->get("support_text_1"); ?><br /><br />
                        <span class="bold"><?php echo $translation->get("support_text_2"); ?></span><br />
                        <a target="_blank" href="http://support.dc-solution.de">support.dc-solution.de</a>
                    </p>
                    <p>&nbsp;</p>
                    <h2><?php echo $translation->get("manual"); ?></h2>
                    <p>
                        <?php echo $translation->get("support_text_3"); ?><br /><br />
                        <span class="bold"><?php echo $translation->get("support_text_4"); ?></span><br />
                        <a target="_blank" href="http://support.dc-solution.de">support.dc-solution.de</a>
                    </p>
                </td>

                <td>
                    <h2><?php echo $translation->get("contact"); ?></h2>
                    <img width="250" height="77" style="border-width: 0px; border-style: solid;" src="<?= $GLOBALS['projectRoot'] ?>/layout/admin/img/2015/Logo-dynamic-commerce.svg" alt="dynamic commerce GmbH - E-Commerce mit Microsoft Dynamics NAV">
                    <p>
                        <br />
                        <span class="bold">dynamic commerce GmbH</span>
                        <br />
                        Von-Linde-Str. 11<br />
                        95326 Kulmbach<br /><br />

                        Tel: 09221 39165-0<br />
                        Fax: 09221 39165-99<br />
                        <br />
                        <a href="mailto:info@dc-solution.de">info@dc-solution.de</a><br />
                        <a href="http://www.dc-solution.de" target="_blank">www.dc-solution.de</a>
                    </p>
                </td>
            </tr>
        </table>

    </form>
</div>