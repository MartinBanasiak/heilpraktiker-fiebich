<?
setlocale(LC_TIME, "de_DE");
$translation    = \DynCom\dc\common\classes\Registry::get("translation");

// Variablen definieren
$local_file  = CMS_PATH . 'common/logparser/access.log';
$server_file = 'logs/access.log';

if(isset($_GET['action'])) {

    $jsonresult = array(
        'error'=> false,
        'message' => ''
    );

    switch($_GET['action']) {
        case 'start_update':

            if (isset($GLOBALS["ftp_host"]) && isset($GLOBALS["ftp_login"]) && isset($GLOBALS["ftp_password"])) {
                // Verbindung aufbauen
                $conn_id = ftp_connect($GLOBALS["ftp_host"]);

                // Login mit Benutzername und Passwort
                $login_result = ftp_login($conn_id, $GLOBALS["ftp_login"], $GLOBALS["ftp_password"]);
                ftp_pasv($conn_id, TRUE);
                // Versuche $server_file herunterzuladen und in $local_file zu speichern

                if (ftp_get($conn_id, $local_file, $server_file, FTP_BINARY)) {
                    // Alles OK
                    $jsonresult['message'] = $translation->get("stat_file_download_step2");

                    $file = new SplFileObject($local_file);
                    $file->seek($file->getSize());
                    $linesTotal = $file->key();

                    $jsonresult['lines'] = $linesTotal;
                } else {
                    $jsonresult['error'] = true;
                    $jsonresult['message'] = $translation->get("stat_file_download_error");
                }

                // Verbindung schließen
                ftp_close($conn_id);
            }
            echo json_encode($jsonresult);
            exit();
            break;

        case 'update':
            // letztes update timestamp ermitteln
            $query        = "SELECT MAX(timestamp) AS letztes FROM main_log";
            $result       = @mysqli_query($GLOBALS['mysql_con'], $query);
            $maxTimestamp = 0;
            if (@mysqli_num_rows($result) == 1) {
                $row          = @mysqli_fetch_array($result);
                $maxTimestamp = $row['letztes'];
            }

            ini_set('max_execution_time', '0');

            // include all the classes
            require_once(CMS_PATH . 'common/logparser/class.log.php');
            require_once(CMS_PATH . 'common/logparser/class.log.mysql.php');
            require_once(CMS_PATH . 'common/logparser/class.log.output.php');

            // see class.log.mysql.php for example table setup
            //
            log::$parser = new log_mysql(array(
                'table'        => 'main_log',
                'fields'       => array(
                    'ip'            => 'ip',
                    'identd'        => 'identd',
                    'auth'          => 'auth',
                    'day'           => 'day',
                    'month'         => 'month',
                    'year'          => 'year',
                    'time'          => 'time',
                    'request'       => 'request',
                    'http_version'  => 'http_version',
                    'response_code' => 'response_code',
                    'size'          => 'size',
                    'referrer'      => 'referrer',
                    'navigator'     => 'navigator',
                    'timestamp'     => 'timestamp',
                    'insertdate'    => 'insertdate'
                ),
                'maxTimestamp' => $maxTimestamp,
                'start' => $_POST['start'],
                'end' => $_POST['end']
            ));

            log::parse($local_file);
            echo json_encode($jsonresult);
            exit();
            break;

        default:

            break;
    }
}

function stat_filter( $site_id, $language_id, $from_level = 1, $to_level = 3 ) {
    $current_filter = (isset($_POST['filter_days']) ? $_POST['filter_days'] : 7);
    $translation    = \DynCom\dc\common\classes\Registry::get("translation");

    echo "<div class=\"input\"><select onchange=\"filter_mails(this.value);\" class=\"bigselect\" name=\"page_filter\" id=\"page_filter\">";
    echo "<option value='WEEKOFYEAR' " . ($current_filter == "WEEKOFYEAR" ? 'selected="selected"' : '') . ">" . $translation->get("weekofyear") . "</option>";
    echo "<option value='MONTH' " . ($current_filter == "MONTH" ? 'selected="selected"' : '') . ">" . $translation->get("month") . "</option>";
    echo "<option value='YEAR' " . ($current_filter == "YEAR" ? 'selected="selected"' : '') . ">" . $translation->get("year") . "</option>";
    echo "</select>";
    echo "</div>";
}



$messages = array();
?>
<?

if (isset($_POST['filter_days'])) {
    $where = $_POST['filter_days'];
} else {
    $where = 'WEEKOFYEAR';
}

// where zum filtern der aktuellen seite. 
// hits von /layout /plugins /userdata usw. werden nicht mitgezaehlt
$siteOnlyWhere = " (request = 'GET /' OR request LIKE 'GET /" . $GLOBALS["site"]["code"] . "/" . $GLOBALS["language"]["code"] . "%' ) ";

$formname = "form_stat";

$hits  = array();
$query = "
		SELECT 
			" . $where . "( from_unixtime( `timestamp` , '%Y-%m-%d' ) ) AS datum, 
			YEAR(from_unixtime( `timestamp` , '%Y-%m-%d' )) AS jahr, 
			ip, 
			COUNT( * ) as anzahl 
		FROM 
			`main_log` 
		where 
			" . $siteOnlyWhere . "
		GROUP BY 
			datum,jahr 
		ORDER BY 
			timestamp DESC"; // hits


$result = @mysqli_query($GLOBALS['mysql_con'], $query);
while ($row = mysqli_fetch_array($result)) {
    if (!isset($hits[$row['jahr']])) {
        $hits[$row['jahr']] = array();
    }
    $hits[$row['jahr']][$row['datum']] = $row['anzahl'];
}

$besucher = array();
$query    = "
		SELECT
			COUNT(*) AS anzahl, 
			temptable.datum AS datum,
			temptable.jahr AS jahr
		FROM 
			(SELECT 
				" . $where . "( from_unixtime( `timestamp` , '%Y-%m-%d' ) ) AS datum, 
				YEAR(from_unixtime( `timestamp` , '%Y-%m-%d' )) AS jahr 
			FROM 
				`main_log` 
			WHERE
				" . $siteOnlyWhere . " 
			GROUP BY 
				datum,jahr,ip 
			ORDER BY 
				timestamp DESC
		) AS temptable 
		GROUP BY 
			temptable.datum, temptable.jahr"; // besucher


$result = @mysqli_query($GLOBALS['mysql_con'], $query);
while ($row = mysqli_fetch_array($result)) {
    if (!isset($besucher[$row['jahr']])) {
        $besucher[$row['jahr']] = array();
    }
    $besucher[$row['jahr']][$row['datum']] = $row['anzahl'];
}

?>

<script type="text/javascript">
    var _update_step = 5000;

    function show_google_analytics() {
        window.open("https://accounts.google.com/Login?hl=de&service=analytics");
    }

    function filter_mails( _days ) {
        jQuery('#filter_days').val(_days);
        jQuery('#form_stat').submit();
    }

    function voidFunction() {

    }

    function refresh_stat() {
        if(!confirm("<?php echo $translation->get("refresh_stat_confirm"); ?>")) {
            return;
        }

        $('#refresh_stat').hide();
        $('#stat_info').show();

        $.ajax({
            url: "?action=start_update",
            type: 'POST',
            dataType: 'json',
            success: function(output, status, xhr) {
                $('#stat_text').html(output.message);
                intervall_stat_update(0,_update_step,output.lines);
            },
            error: function(jqXHR, textStatus, errorThrown) {
            }
        });
    }

    function intervall_stat_update(_start, _end, _maxLines) {
        $.ajax({
            url: "?action=update",
            type: 'POST',
            dataType: 'json',
            data: {
                start: _start,
                end: _end,
                max: _maxLines
            },
            success: function(output, status, xhr) {
                var nextStart = _start+_update_step;
                if(nextStart >= _maxLines) {
                    $('#stat_text').html("<?php echo $translation->get("stat_file_download_step3"); ?>");
                    window.location.reload();
                    return;
                }

                $('#stat_text').html(nextStart + " <?php echo $translation->get("of"); ?> " + _maxLines + " <?php echo $translation->get("lines_finished"); ?>");
                intervall_stat_update(nextStart, _update_step, _maxLines);
            },
            error: function(jqXHR, textStatus, errorThrown) {
            }
        });
    }
</script>

<ul class="toolbar_menu">
    <?= button("google", $translation->get("google_analytics"), $formname, "show_google_analytics()"); ?>
</ul>

<div id="mainContent">
    <?php echo current_website_language($site, $language); ?>
    <h1><?php echo get_translation('top_statistic'); ?></h1>

    <h3><?php echo $translation->get("filter"); ?>:</h3>
    <?php
    stat_filter($GLOBALS["site"]['id'], $GLOBALS["language"]['id']);
    ?>
    <div class="clearfix"></div>
    <br />
    <input type="button" id="refresh_stat" value="<?php echo $translation->get("button_refresh_stat"); ?>" onclick="refresh_stat();" />
    <div id="stat_info" style="display:none"><?php echo $translation->get("state"); ?>: <span id="stat_text"><?php echo $translation->get("stat_file_download_step1"); ?></span></div>

    <form id="<?= $formname ?>" name="<?= $formname ?>" method="post" action="?action=filter">
        <input type="hidden" value="" name="filter_days" id="filter_days" />
        <input type="hidden" class="selected_linklist_row" name="input_id" value="" />

        <div class="requestLoader"></div>
        <?php
        ?>
        <table cellpadding=0 cellspacing=0 border=0 class="linklist">
            <?php
            switch ($where) {
                case 'YEAR':
                    $headline = $translation->get("year");
                    break;

                case 'MONTH':
                    $headline = $translation->get("month");
                    break;
                case 'WEEKOFYEAR':
                default:
                    $headline = $translation->get("weekofyear");
                    break;
            }

            echo "<tr>";
            echo format_key($headline, "");
            echo format_key($translation->get("hits"), "");
            echo format_key($translation->get("unique_users"), "");
            echo "</tr>";

            echo "<tr>";
            echo format_seperator("");
            echo format_seperator("");
            echo format_seperator("");
            echo "</tr>";

            foreach ($hits as $year => $hitData) {
                foreach ($hitData as $date => $count) {
                    switch ($where) {
                        case 'YEAR':
                            $datestring = $year;
                            break;

                        case 'MONTH':
                            $monthDate  = mktime(0, 0, 0, $date, 10);
                            $datestring = utf8_encode(strftime("%B", $monthDate)) . " " . $year;
                            break;
                        case 'WEEKOFYEAR':
                        default:
                            $realKW = sprintf('%02d', $date);
                            $timestamp_montag  = strtotime("{$year}-W{$realKW}");
                            $timestamp_sonntag = strtotime("{$year}-W{$realKW}-7");
                            $datestring        = $date . " (" . date('d.m.Y', $timestamp_montag) . " - " . date("d.m.Y", $timestamp_sonntag) . ") ";
                            break;
                    }


                    echo "    <tr class=\"linklist_content\" data-function=\"voidFunction\" \">\n";

                    echo format_value($datestring, "input_id");
                    echo format_value($count, "input_id");
                    echo format_value($besucher[$year][$date], "input_id");

                    echo "    </tr>\n";
                }
            }
            ?>
        </table>

    </form>
</div>