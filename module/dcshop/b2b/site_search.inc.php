<div id="search">
    <label><?=$GLOBALS['tc']['search']?></label>
    <form id="form_search" name="form_search" method="get"
          action="/<? echo customizeUrl(); ?>/search/">
        <div class="search_field">
            <input type="text" name="input_search" id="input_search_b2b" placeholder="<?= $GLOBALS["tc"]["search_term"] ?>" />
        </div>
        <div class="search_button" onclick="document.form_search.submit();">
            <i class="fa fa-search" aria-hidden="true"></i>
        </div>
    </form>
    <div id="itemsearch_suggestion_wrapper">
    </div>
</div>