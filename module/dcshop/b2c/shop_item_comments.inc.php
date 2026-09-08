<a class="button comment_button button_action" href="#" data-target="#inlineContent_comment" data-toggle="modal"><?= $GLOBALS["tc"]["rate_link"] ?></a>
<? $formname = "form_item_comments" ?>
<script type="text/javascript">

    function setStars( value ) {
        $('#<?=$formname?>').find('#input_comment_rating').val(value);
        $('#<?=$formname?> .star').children('.fa').removeClass('fa-star').addClass('fa-star-o');
        $('#star-' + value).children('.fa').removeClass('fa-star-o').addClass('fa-star');
        $('#star-' + value).prevAll('.star').children('.fa').removeClass('fa-star-o').addClass('fa-star');
    }
</script>

<div class="modal fade" id="inlineContent_comment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?= $GLOBALS["tc"]["rate_link"] ?></h4>
            </div>
            <div class="modal-body">
                <div id="create_comment">

                    <?// get_modal_infobox($item);?>
                    <div class="modal-item-info">
                        <div class="modal-item-image">
                            <?= get_image($item,2) ?>
                        </div>
                        <div class="modal-item-description text-center">
                            <strong><?= $item['description'] ?></strong>
                            <br />
                            <?= $item['summary'] ?>
                        </div>
                    </div>
                    <h2 ><?= $GLOBALS['tc']['rate_item'] ?></h2>

                    <form name="<?= $formname ?>" id="<?= $formname ?>" action="?action=save"
                          method="POST">
                        <input type="hidden" name="input_comment_rating" id="input_comment_rating" value="5" />

                        <div class="rating_header">
                            <?= $GLOBALS['tc']['rate_infos'] ?><br />
                            <input name="input_comment_name" id="input_comment_name" class="input" type="text" placeholder="<?= $GLOBALS["tc"]["name"] ?>" /><br /><br />
                        </div>
                        <div class="rating_city_headline"><?= $GLOBALS["tc"]["rating_city"] ?></div>
                        <div class="rating_city_input">
                            <input name="input_comment_city" id="input_comment_city" class="input" type="text" placeholder="<?= $GLOBALS["tc"]["city"] ?> " /><br /><br />
                        </div>
                        <div class="rating_hint">
                            <?= $GLOBALS['tc']['rate_anonymous'] ?><br /><br />
                        </div>
                        <div class="rating_stars">
                            <strong><?= $GLOBALS['tc']['rate_stars'] ?></strong>
                            <div class="stars">
                                <a id="star-1" class="star" title="1" href="javascript:void(0);" onclick="setStars(1);"><i aria-hidden="true" class="fa fa-star"></i></a>
                                <a id="star-2" class="star" title="2" href="javascript:void(0);" onclick="setStars(2);"><i aria-hidden="true" class="fa fa-star"></i></a>
                                <a id="star-3" class="star" title="3" href="javascript:void(0);" onclick="setStars(3);"><i aria-hidden="true" class="fa fa-star"></i></a>
                                <a id="star-4" class="star" title="4" href="javascript:void(0);" onclick="setStars(4);"><i aria-hidden="true" class="fa fa-star"></i></a>
                                <a id="star-5" class="star" title="5" href="javascript:void(0);" onclick="setStars(5);"><i aria-hidden="true" class="fa fa-star"></i></a>
                            </div>
                        </div>

                        <div class="rating_text_headline"><?= $GLOBALS['tc']['rate_title'] ?></div>
                        <div class="rating_headline_input">
                            <input name="input_comment_header" id="input_comment_header" class="input" type="text" value="" maxlength="45" /><br /><br />
                        </div>
                        <div class="rating_text_headline"><?= $GLOBALS['tc']['rate_text'] ?></div>
                        <div class="rating_input">
                            <textarea maxlength="250" name="input_comment_text" id="input_comment_text" class="input"></textarea><br />
                        </div>
                        <div class="rating_hint">
                            <?= $GLOBALS['tc']['rate_mandatory'] ?>
                        </div>
                        <div class="label_article_name"></div>

                        <div style="display:none;"><input type="text" name="input_captcha1" id="input_captcha1" value="" /><input
                                type="text" name="input_captcha2" id="input_captcha2" value="<?= time() ?>" /></div>
                        <div class="button_row_bottom">
                            <input class="button button_action" name="submit" type="submit" id="submit_button" value="<?= $GLOBALS["tc"]["send"] ?>" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>