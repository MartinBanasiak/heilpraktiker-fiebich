<?
$formname = "form_live_page_edit";
$translation = \DynCom\dc\common\classes\Registry::get("translation");
$inputname = "input_id";
$input_page_id = (isset($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");

$page_navigation = array();
$query = "SELECT id FROM main_navigation WHERE forward_type = 1 and forward_page_id = '" . $input_page_id . "' LIMIT 1";
$result = @mysqli_query($GLOBALS['mysql_con'],$query);
if(@mysqli_num_rows($result) == 1) {
    $page_navigation = @mysqli_fetch_array($result);
}
?>

<div id="overlaycrumb">
    <?php
    echo $translation->get("live_page_edit");
    ?>
    <div id="closeoverlay" onclick="disableOverlay();"></div>
</div>

<?php
if(count($page_navigation) == 0 && !on_shopping_world()) {
    echo "<div class=\"infobox\">" . $translation->get("live_edit_not_possible") . "</div>";
    exit();
}
if (on_shopping_world()) {
    require_once (rtrim(dirname(dirname(__DIR__)),'/') . "/module/dcshop/common/category_functions.inc.php");
    $pdo = get_main_db_pdo_from_env_single_instance();
    $company = $GLOBALS['language']['company'];
    $shop_code = $GLOBALS['language']['shop_code'];
    $language_code = $GLOBALS['language']['shop_language_code'];
    $shop = get_shop($company,$shop_code);
    $category_shop_code = !empty($shop['use_categorys_from_shop_code']) ? $shop['use_categorys_from_shop_code'] : $shop_code;

    /** @var \DynCom\dc\dcShop\classes\CategoryTreeBuilder $treeBuilder */
    $treeBuilder = new \DynCom\dc\dcShop\classes\CategoryTreeBuilder($pdo);
    /** @var \DynCom\dc\dcShop\classes\CategoryTreeNode $root */
    $root = $treeBuilder->getCategoryTree($company,$category_shop_code,$language_code);

    $GLOBALS['curr_category_tree'] = $root;

    $categoryLineNo = get_linked_category_line_no_for_page_id($pdo, $input_page_id);
    $category = get_category($company, $shop_code, $language_code, $categoryLineNo);
    if ($category === null) {
        echo "<div class=\"infobox\">" . $translation->get("live_edit_not_possible_shopping_world") . "</div>";
        exit();
    }
    $GLOBALS['shop']['company'] = $company;
    $GLOBALS['shop']['category_source'] = $category_shop_code;
    $GLOBALS['shop_language']['code'] = $language_code;
    $iframeUrl = "//" . $_SERVER['HTTP_HOST'] . category_get_path($category) . "?live_edit=1";
} else {
    $iframeUrl = "//" . $_SERVER['HTTP_HOST'] . get_link_to_navigation($page_navigation['id']) . "?live_edit=1";
}

?>

<script type="text/javascript">
    var config_CKEDITOR_custom_config = 'layout/frontend/<?php echo $GLOBALS["layout"]["code"]; ?>/dist/css/fck.css';

    var component_url = '<?php echo get_menu_link('structure/components',$GLOBALS["site"],$GLOBALS["language"], "?action=open_card_component&input_component_id="); ?>';

    function build_collection_edit_url(edit_collection_setup_id, collection_id) {
        var collection_url = '<?php echo get_menu_link("collections/' + edit_collection_setup_id + '",$GLOBALS["site"],$GLOBALS["language"], "?action=open_card_collection&input_collection_id="); ?>';
        collection_url += collection_id;
        return collection_url;
    }
</script>
<style>
    #overlayWrapper {
        overflow:hidden;
        width:80%;
        margin-left:auto;
        left:10%;
    }

    #overlayLoader {
        width:80%;
        margin-left:auto;
        left:10%;
    }

    #overlayContent {
        height:100%;
        position:relative;
    }
</style>
<div class="clearfix"></div>
<div class="liveedit">
    <iframe id="live_edit_frame" src="<?php echo $iframeUrl; ?>"></iframe>
    <script type="text/javascript">
        var height = window.innerHeight - 180;
        $('#live_edit_frame').css('height', height);
        $(window).resize(function(){
            var height = window.innerHeight - 180;
            $('#live_edit_frame').css('height', height);
        });
    </script>
</div>