<?
$messages = array();

switch ($_GET["action"]) {
    case 'save':
        save_licence($messages);
        break;
    default:
        show_licence();
        break;
}

function show_licence() {
    $query  = "SELECT * FROM main_setup";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $input_licence_data = @mysqli_fetch_array($result);
        return $input_licence_data;
    }
}

function save_licence( &$messages ) {
    $translation = \DynCom\dc\common\classes\Registry::get("translation");

    $query  = "SELECT * FROM main_setup";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) < 1) {
        $query = "INSERT INTO main_setup(licence_no, licence_licensee, licence_content, licence_extended_licences, licence_hosting, licence_activation_date ) VALUES ('" . $_POST['input_licence_no'] . "', '" . $_POST['input_licensee'] . "','" . $_POST['licence_content'] . "', '" . $_POST['input_extended_licence_no'] . "', '" . $_POST['input_licence_hosting'] . "','" . $_POST['input_licence_activation_date'] . "')";
    } else {
        $query = "UPDATE main_setup SET licence_no = '" . $_POST['input_licence_no'] . "', licence_licensee = '" . $_POST['input_licensee'] . "', licence_content = '" . $_POST['input_licence_content'] . "', licence_extended_licences = '" . $_POST['input_extended_licence_no'] . "', licence_hosting = '" . $_POST['input_licence_hosting'] . "', licence_activation_date = '" . $_POST['input_licence_activation_date'] . "' WHERE id = 1";
    }
    if (@mysqli_query($GLOBALS['mysql_con'], $query)) {
        $messages[] = "<div class=\"successbox\">" . $translation->get("license_save_success") . "</div>";
    } else {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("license_save_error") . "</div>";
    }
}


$disabled = TRUE;

?>
<? $formname = "form_layout_list"; ?>

<div id="mainContent">
    <?php echo current_website_language($site, $language); ?>
    <h1><?php echo get_translation('top_license'); ?></h1>

    <?php
    if (count($messages)) {
        echo '<div id="overlayMessages">' . join("\r\n", $messages) . '</div>';
    }

    // include license
    include __DIR__ . DIRECTORY_SEPARATOR . 'licence.php';
    ?>

    <form id="<?= $formname ?>" name="<?= $formname ?>" method="post">

        <table class="cardform" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td>
                    <?= input($translation->get("licensenumber"), "input_licence_no", "text", $licence['lizenznummer'], 45, $disabled) ?>
                    <?= input($translation->get("licensed_party"), "input_licensee", "smalltextarea", html_entity_decode($licence['lizenznehmer']), 250, $disabled) ?>

                    <?= input($translation->get("activation_date"), "input_licence_activation_date", "date", $licence['freischaltdatum'], 45, $disabled) ?>
                </td>
            </tr>
        </table>

    </form>
</div>
