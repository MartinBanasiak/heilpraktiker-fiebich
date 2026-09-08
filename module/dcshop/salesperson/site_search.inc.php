<div id="search">
    <form id="form_search" name="form_search" method="post"
          action="/<?= $GLOBALS["site"]["code"] ?>/<?= $GLOBALS["language"]["code"] ?>/shop/?shop_category=search">
        <div class="search_field">
            <input type="text" name="input_search" id="input_search" value="<?= $GLOBALS["tc"]["search_term"] ?>"
                   onfocus="this.form.input_search.value=''" />

            <div class="search_button" onclick="document.forms['form_search'].submit();">
            </div>
        </div>

    </form>
</div>


