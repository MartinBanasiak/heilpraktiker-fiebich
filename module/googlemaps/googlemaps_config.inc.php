<?php
//Generiert ein Array mit Code für Kartenmarkierungen
function marker_list( $array, $sitepart_id ) {
    $marker_instances = array();
    FOR ($i = 0; $i < count($array); $i++) {
        $marker_instances[$i] = "
		var marker_" . $i . " = new google.maps.Marker({\t
			position: myLatlng_" . $i . ",\t
			icon: image,
			title: \"" . $array[$i]['title'] . "\",
			map: map_" . $sitepart_id . "\t
		});\n
		";
    }
    RETURN $marker_instances;
}

//Generiert ein Array mit Code für Info-Boxen und deren Inhalt aus Nutzereingaben im ck-editor
function info_list( $array ) {
    $infos = array();
    FOR ($i = 0; $i < count($array); $i++) {
        $description = str_replace("\r\n", '', $array[$i]['description']);
        $infos[$i]   = "
		var contentString_" . $i . " = '" . $description . "';
		var infowindow_" . $i . " = new google.maps.InfoWindow({
			maxWidth: 350,
			content: contentString_" . $i . ",
			position: marker_" . $i . "
		});
		";
    }
    RETURN $infos;
}

//Generiert ein Array mit Koordinaten für die Markierungen
function coordinate_list( $array ) {
    $coordinates = array();
    IF (count($array) == 1) {
        $coordinates[0] = "
			var myLatlng_0 = new google.maps.LatLng(" . $array[0]['lat'] . ", " . $array[0]['lng'] . ");
		";
    } ELSE {
        FOR ($i = 0; $i < count($array); $i++) {
            $coordinates[$i] = "
			var myLatlng_" . $i . " = new google.maps.LatLng(" . $array[$i]['lat'] . ", " . $array[$i]['lng'] . ");
			bounds.extend(myLatlng_" . $i . ");
			";
        }
    }
    RETURN $coordinates;
}

//Generiert ein Array mit Event-Listenern und -Triggern für das Öffnen der Infoboxen bei Klick auf die Markierung
function info_open_list( $array, $sitepart_id ) {
    $opener = array();
    FOR ($i = 0; $i < count($array); $i++) {
        $opener[$i] = "
		google.maps.event.addListener(marker_" . $i . ", 'click', function(){
			infowindow_" . $i . ".open(map_" . $sitepart_id . ",marker_" . $i . ");
		});";
    }
    RETURN $opener;
}