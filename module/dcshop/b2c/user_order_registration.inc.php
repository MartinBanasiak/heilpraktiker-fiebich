<script type="text/javascript">
    document.onkeydown = function ( evt ) {
        evt = evt || window.event;
        if (evt.keyCode == 13) {
            document.form_shop_login2.submit();
        }
    };
</script>
<?php
if (!$GLOBALS['visitor']['frontend_login']) {?>
    <div id="login_fields">
        <h2 style="margin-top:0;"><?=$GLOBALS['tc']['existing_customer'];?></h2>
        <form id="form_shop_login2" name="form_shop_login2" method="post" action="?action=shop_login">
            <?input_shop('E-Mail', "input_email", "text", '', 50, FALSE, FALSE);?>
            <?input_shop($GLOBALS["tc"]["password"], "input_password", "password", '', 50, FALSE, FALSE);?><br/>
            <?=button("order_next", $GLOBALS["tc"]["login"], "form_shop_login2", "?action=shop_login");?>
        </form>
    </div>
<?}?>