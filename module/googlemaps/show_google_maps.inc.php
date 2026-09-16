<?php
//Holt hinterlegte Orte als assoziatives Array
$sql_places = "SELECT address, description, lat, lng, title FROM google_maps_line WHERE header_id = '" . $sitepart_id."'";
$qry_places = mysqli_query($GLOBALS['mysql_con'], $sql_places);
IF (mysqli_num_rows($qry_places) == 1) {
    $places    = array();
    $places[0] = mysqli_fetch_assoc($qry_places);
} ELSE {
    $places = array();
    $i      = 0;
    WHILE ($row = mysqli_fetch_assoc($qry_places)) {
        $places[$i] = $row;
        $i++;
    }
}

//Funktionsaufrufe zur Generierung der benötigten Listen 
$markers     = marker_list($places, $sitepart_id);
$coordinates = coordinate_list($places);
$infos       = info_list($places);
$openers     = info_open_list($places, $sitepart_id);


//Holt Höhe und Breite der Karte als width & height variablen
$sql_map  = "SELECT width, height, icon_location FROM google_maps_header WHERE id = '" . $sitepart_id."'";
$qry_map  = mysqli_query($GLOBALS['mysql_con'], $sql_map);
$map_size = mysqli_fetch_assoc($qry_map);
extract($map_size);


// Der Google-Maps-Schluessel stand hier frueher fest im Quelltext. Er gehoert
// nicht ins Repository - Google hat ihn dort selbst gefunden und gemeldet.
// Jetzt kommt er aus config/.env, die nicht versioniert wird.
//
// Wichtig: Ein Maps-Schluessel fuer den Browser ist immer im Seitenquelltext
// sichtbar, auch jetzt noch. Schuetzen laesst er sich nur ueber eine
// HTTP-Referrer-Beschraenkung in der Google Cloud Console.
$googleMapsApiKey = trim((string)getenv('GOOGLE_MAPS_API_KEY'));

if ($googleMapsApiKey === '') {
    // Ohne Schluessel laedt Google Maps nicht. Lieber gar kein Script-Tag als
    // eines, das im Browser eine Fehlermeldung ueber die Karte legt.
    error_log('Google Maps: GOOGLE_MAPS_API_KEY ist nicht gesetzt, Karte wird nicht eingebunden.');

    return;
}

//Generiert Script-Tags, JQuery-Funktion zum Setzen eines neuen meta-tags im Seiten-Header,
//Deklariert Optionsvariable und beginnt die Definition der Initialisierungsfunktion
$mapcode = "
	<script class='DCCookie_google_maps' type=\"text/plain\" src=\"//maps.google.com/maps/api/js?sensor=false&key=" . rawurlencode($googleMapsApiKey) . "\"></script>
	<script class='DCCookie_google_maps' type=\"text/plain\">\n
	show_maps_$sitepart_id();     
    
    function show_maps_$sitepart_id() {
    if (typeof google !== 'undefined' && document.getElementById(\"map_canvas_" . $sitepart_id . "\") !== null){
	var image = '" . $icon_location . "';
	var bounds = new google.maps.LatLngBounds();
	var myOptions_" . $sitepart_id . ";\n
	function initialize_map_" . $sitepart_id . "() {
";

//Generiert Objekte für Koordinaten
FOR ($i = 0; $i < count($coordinates); $i++) {
    $mapcode .= $coordinates[$i];
}

//Definiert Optionen
$mapcode .= "\n\t\t" . "myOptions_" . $sitepart_id . " = {
	\t\t zoom: 15,
	\t\t center: myLatlng_0,
	\t\t mapTypeId: google.maps.MapTypeId.ROADMAP,
    \t\t scrollwheel: false,
    \t\t styles: [
    {
        \"featureType\": \"administrative.locality\",
        \"elementType\": \"all\",
        \"stylers\": [
            {
                \"hue\": \"#2c2e33\"
            },
            {
                \"saturation\": 7
            },
            {
                \"lightness\": 19
            },
            {
                \"visibility\": \"on\"
            }
        ]
    },
    {
        \"featureType\": \"landscape\",
        \"elementType\": \"all\",
        \"stylers\": [
            {
                \"hue\": \"#ffffff\"
            },
            {
                \"saturation\": -100
            },
            {
                \"lightness\": 100
            },
            {
                \"visibility\": \"simplified\"
            }
        ]
    },
    {
        \"featureType\": \"poi\",
        \"elementType\": \"all\",
        \"stylers\": [
            {
                \"hue\": \"#ffffff\"
            },
            {
                \"saturation\": -100
            },
            {
                \"lightness\": 100
            },
            {
                \"visibility\": \"off\"
            }
        ]
    },
    {
        \"featureType\": \"road\",
        \"elementType\": \"geometry\",
        \"stylers\": [
            {
                \"hue\": \"#bbc0c4\"
            },
            {
                \"saturation\": -93
            },
            {
                \"lightness\": 31
            },
            {
                \"visibility\": \"simplified\"
            }
        ]
    },
    {
        \"featureType\": \"road\",
        \"elementType\": \"labels\",
        \"stylers\": [
            {
                \"hue\": \"#bbc0c4\"
            },
            {
                \"saturation\": -93
            },
            {
                \"lightness\": 31
            },
            {
                \"visibility\": \"on\"
            }
        ]
    },
    {
        \"featureType\": \"road.arterial\",
        \"elementType\": \"labels\",
        \"stylers\": [
            {
                \"hue\": \"#bbc0c4\"
            },
            {
                \"saturation\": -93
            },
            {
                \"lightness\": -2
            },
            {
                \"visibility\": \"simplified\"
            }
        ]
    },
    {
        \"featureType\": \"road.local\",
        \"elementType\": \"geometry\",
        \"stylers\": [
            {
                \"hue\": \"#e9ebed\"
            },
            {
                \"saturation\": -90
            },
            {
                \"lightness\": -8
            },
            {
                \"visibility\": \"simplified\"
            }
        ]
    },
    {
        \"featureType\": \"transit\",
        \"elementType\": \"all\",
        \"stylers\": [
            {
                \"hue\": \"#e9ebed\"
            },
            {
                \"saturation\": 10
            },
            {
                \"lightness\": 69
            },
            {
                \"visibility\": \"on\"
            }
        ]
    },
    {
        \"featureType\": \"water\",
        \"elementType\": \"all\",
        \"stylers\": [
            {
                \"hue\": \"#e9ebed\"
            },
            {
                \"saturation\": -78
            },
            {
                \"lightness\": 67
            },
            {
                \"visibility\": \"simplified\"
            }
        ]
    }
] 
	\t}
	\n\t\t" . "var map_" . $sitepart_id . " = new google.maps.Map(document.getElementById(\"map_canvas_" . $sitepart_id . "\"), myOptions_" . $sitepart_id . ");\n
document.getElementById(\"map_canvas_" . $sitepart_id . "\").setAttribute('style','width: {$width}px; height: {$height}px; color: #000000;');\n";
//Generiert Objekte für Info-Boxen
FOR ($i = 0; $i < count($infos); $i++) {
    $mapcode .= $infos[$i];
}

//Generiert Objekte für Marker
FOR ($i = 0; $i < count($markers); $i++) {
    $mapcode .= $markers[$i];
}

//Generiert Event-Listener/Trigger zum Öffnen der Info-Boxen
FOR ($i = 0; $i < count($openers); $i++) {
    $mapcode .= $openers[$i];
}

IF (count($places) > 1) {
    $mapcode .= "\n\t\t" . "map_" . $sitepart_id . ".fitBounds(bounds);\n";
}

//Schließt Funktionsdefinition. Generiert Listener zum Aufrufen der Initialisierungsfunktion
$mapcode .= "} \n initialize_map_" . $sitepart_id . "();";

$mapcode .= "} else { window.setTimeout(function(){ show_maps_$sitepart_id() },50);}}</script>";

echo $mapcode;
?>

<!--<div class="google_maps" class="sitepart_<?= $sitepart_id ?>">-->
<div class="googlemaps_content DCCookie_google_maps_container" id="map_canvas_<?= $sitepart_id ?>"></div>
<!--</div>-->

