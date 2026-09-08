<?
//$appid = $GLOBALS["site"]["facebook_app_id"];
?>
<div class="info_button_facebook">
    <a href="#" data-toggle="modal" data-target="#inlineContent_fb" onclick="load_fb();"><i class="fa fa-facebook-square" aria-hidden="true"></i> <?= $GLOBALS["tc"]["share_on_facebook"] ?></a>
</div>
<script type="text/javascript">
    function load_fb() {
        var container = document.getElementById("share_fb");
        var containerContent = '<iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2F<? echo $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]; ?>&amp;send=false&amp;layout=standard&amp;width=400&amp;show_faces=false&amp;font&amp;colorscheme=light&amp;action=recommend&amp;height=35" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:400px; height:35px;" allowTransparency="true"></iframe>';
        container.innerHTML = containerContent;
    }
</script>

<div class="modal fade" id="inlineContent_fb" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?= $GLOBALS["tc"]["share_on_facebook"] ?></h4>
            </div>
            <div class="modal-body">
                <? get_modal_infobox($item);?>
                <div id="share_fb"></div>
            </div>
        </div>
    </div>
</div>
