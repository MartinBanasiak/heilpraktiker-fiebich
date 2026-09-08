<?

if ( isset($_REQUEST["update_geo_ip_files"])) {
update_geo_ip_files();
}

function update_geo_ip_files()
{
    $baseDirectory = rtrim(dirname(dirname(__DIR__)), '/');
    $envDir = rtrim($baseDirectory, '/') . '/config/geolocation';
    if (is_dir($envDir)) {
        $dotenv = new \Dotenv\Dotenv($envDir);
        $dotenv->load();
    }

    //file_get_contents(getenv('Geo_Lite_Country_File_URL'));
   // file_get_contents(getenv('Geo_Lite_Country_IPV6_File_URL'));
    $fileName = "GeoIP.dat.gz";
    $url = getenv('Geo_Lite_Country_File_URL');
    $destination = $envDir."/".$fileName;
    $fp = fopen ($destination, 'w+');
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, false );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );

    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
    curl_setopt( $ch, CURLOPT_FILE, $fp );
    curl_exec( $ch );
    curl_close( $ch );
    fclose( $fp );

    exec('gunzip '.$destination);

    $fileName = "GeoIPv6.dat.gz";
    $url = getenv('Geo_Lite_Country_IPV6_File_URL');
    $destination = $envDir."/".$fileName;
    $fp = fopen ($destination, 'w+');
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, false );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );

    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
    curl_setopt( $ch, CURLOPT_FILE, $fp );
    curl_exec( $ch );
    curl_close( $ch );
    fclose( $fp );

    exec('gunzip '.$destination);



}

$messages = array();
$error    = FALSE;

$translation = \DynCom\dc\common\classes\Registry::get("translation");
$formname    = "form_geoip";

?>


<ul class="toolbar_menu">
    <?= button("down", $translation->get("update"), $formname, " document.getElementById('".$formname."').submit()"); ?>
</ul>


<div id="mainContent">
    <?php echo current_website_language($site, $language); ?>
    <h1><?php echo get_translation('geoip'); ?></h1>

    <form id="<?= $formname ?>" name="<?= $formname ?>" method="post" enctype="multipart/form-data">

        <input type="submit"  style="visibility: hidden;" class="button_save" id="update_geoip_button" name="update_geo_ip_files" value = "<? echo get_translation("update_geo_ip_files") ?>">  </input>

    </form>

</div>