<?
function shop_dealer_search_show($sitepart_header_id)
{

    $GoogleApikey = '';

    $IOCContainer = $GLOBALS['IOC'];
    $pdo = $IOCContainer->resolve('DynCom\dc\common\classes\PDOQueryWrapper');


    $prepStatement = " Select google_api_key From main_shop_dealer_search where id  = :sitePartId";
    $params = [
        [':sitePartId', $sitepart_header_id, PDO::PARAM_STR],
    ];
    $pdo->setQuery($prepStatement);
    $pdo->prepareQuery();
    $pdo->bindParameters($params);
    $pdo->executePreparedStatement();
    $resArr = $pdo->getResultArray();

    if (is_array($resArr)) {
        if (is_array($resArr[0])) {
            $resArr = $resArr[0];
        }
        if (array_key_exists('google_api_key', $resArr)) {
            $GoogleApikey = $resArr['google_api_key'];
        }
    }

    ?>
    <style>
        #mapholder img {
            max-width: none !important;
        }

        #map img {
            max-width: none !important;
        }

        /* IE 6 does not support max-width so default to width 100% */
        .ie6 img {
            width: 100%;
        }

        .gm-style img {
            max-width: none !important;
        }

        .gm-style label {
            width: auto !important;
            display: inline;
        }
    </style>
    </head>

    <?php


    $company = $GLOBALS['shop']['company'];
    $shopCode = $GLOBALS['shop']['code'];
    $customerSource = $GLOBALS['shop']['customer_source'];
    $languageCode = $GLOBALS['shop_language']['code'];
//add_vendors_geo_coordinates($company, $shopCode, $languageCode);
    $address = '';
    if (isset($_POST['address']) && $_POST['address'] != '') {
        $address = $_POST['address'];
    }
    $latitude = '';
    if (isset($_POST['latitude']) && $_POST['latitude'] != '') {
        $latitude = $_POST['latitude'];
    }
    $longitude = '';
    if (isset($_POST['longitude']) && $_POST['longitude'] != '') {
        $longitude = $_POST['longitude'];
    }
    $radius = 20;

    if (isset($_POST['radius']) && $_POST['radius'] != '') {
        $radius = $_POST['radius'];
    }


    echo '  
            <div class="category_info">
                <h1 class="shop_site_headline">' . $GLOBALS["tc"]["dealer_search"] . '</h1>
            </div>
                        
              <form id="dealer_search_form" name="dealer_search_form" method="post" action="#" class="form-inline">
                      
                     <div id="locationField">
                            <input class="form-control" id="autocomplete" required name="address" style="width: 400px" placeholder="' . $GLOBALS['tc']['address'] . '" value="' . $address . '"   type="text"></input>
                         <div class="input-group">   <input id="radius"  required class="form-control" name="radius" style="width: 70px"  value="' . $radius . '"  type="number"> <div class="input-group-addon">KM</div> </div>   
                     
                     <input type="button" id="dealer_search_button" class="form-control button" value="' . $GLOBALS['tc']['search'] . '"/>
                     </div>
                        
                         <input type="hidden" name="latitude" id="latitude" value="' . $latitude . '" />
                        <input type="hidden"  name="longitude" id="longitude" value="' . $longitude . '" />
       
            
            </form>
            <br>
            <br>
        ';

    $displayMap = "display:none;";
    if (isset($_POST['latitude']) && $_POST['latitude'] != '' && isset($_POST['longitude']) && $_POST['longitude'] != '') {

        add_dealers_geographic_coordinates($company, $shopCode, $languageCode, $customerSource, $GoogleApikey);

        $latlng = $_POST['latitude'] . "," . $_POST['longitude'];

        $apiResponse = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $latlng . '&key=' . $GoogleApikey);

        $responseArray = json_decode($apiResponse, true);
        //11,Von-Lindestr,Kulmbach,95326,DE

        if ($responseArray['status'] == "OK") {
            $latitude = $responseArray['results'][0]['geometry']['location']['lat'];
            $longitude = $responseArray['results'][0]['geometry']['location']['lng'];

            $customers = get_close_dealers_coordinates($company, $shopCode, $languageCode, $latitude, $longitude, $radius, $customerSource);

            if (count($customers) > 0) {
                $displayMap = "";
            }
        }
    }
    ?>


    <div id="map" style="width:700px;height:400px; <?= $displayMap ?>"></div><br>

    <script>
        // This example requires the Places library. Include the libraries=places
        // parameter when you first load the API. For example:
        // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

        var autocomplete;
        var autoCompeteAddressSelected = false;
        var service, displaySuggestions;
        var geocoder;


        $("#dealer_search_button").on('click', function (event) {
            if (!autoCompeteAddressSelected) {
                service.getQueryPredictions({input: document.getElementById('autocomplete').value}, displaySuggestions);
            } else {
                $("#dealer_search_form").submit();
            }
            autoCompeteAddressSelected = false;


        });


        function initMap() {

            var mapCenter = {lat: -33.8688, lng: 151.2195};
            <? if(count($customers) > 0) { ?>
            mapCenter = {lat: <?= $customers[0]['latitude'] ?>, lng: <?= $customers[0]['longitude'] ?>};
            <? } ?>


            var map = new google.maps.Map(document.getElementById('map'), {
                center: mapCenter,
                zoom: 10
            });
            var input = document.getElementById('autocomplete');

            // map.controls[google.maps.ControlPosition.TOP_RIGHT].push(card);

            autocomplete = new google.maps.places.Autocomplete(input);


            displaySuggestions = function (predictions, status) {
                if (status != google.maps.places.PlacesServiceStatus.OK) {
                    alert(status);
                    return;
                }
                var i = 0;
                predictions.forEach(function (prediction) {
                    if (i == 0) {
                        geocoder.geocode({'placeId': prediction.place_id}, function (results, status) {
                            if (status === 'OK') {
                                if (results[0]) {

                                    var latitude = results[0].geometry.location.lat();
                                    var longitude = results[0].geometry.location.lng();

                                    $('#latitude').val(latitude);
                                    $('#longitude').val(longitude);

                                    $("#dealer_search_form").submit();

                                }
                            }
                        });
                    }
                    i++;
                });
            };


            service = new google.maps.places.AutocompleteService();
            geocoder = new google.maps.Geocoder;


            // Bind the map's bounds (viewport) property to the autocomplete object,
            // so that the autocomplete requests use the current map bounds for the
            // bounds option in the request.
            autocomplete.bindTo('bounds', map);

            var infowindow = new google.maps.InfoWindow();
            var infowindowContent = document.getElementById('infowindow-content');
            infowindow.setContent(infowindowContent);
            var marker = new google.maps.Marker({
                map: map,
                anchorPoint: new google.maps.Point(0, -29)
            });

            <? if(count($customers) > 0) { ?>


            var markerCollections = [];
            var objeto_infowindow = [];


            <?
            for ($i = 0; $i < count($customers); $i++) {
            ?>
            var i = <?= $i ?>;


            var contetntHtml = '<b><?=$customers[$i]['name'] ?> </b> <br> <?= $customers[$i]['address'] ?> <br> <?= $customers[$i]['post_code'] . ' ' . $customers[$i]['city'] ?> <br> <b><? if ($customers[$i]['phone_no'] != '') {
                echo $GLOBALS['tc']['phone_no'] . ":";
            } ?></b> <?= $customers[$i]['phone_no'] ?>';
            objeto_infowindow['infowindow' + i] = new google.maps.InfoWindow({
                content: contetntHtml
            });

            // One object marker per one entity
            var object_marker = new google.maps.Marker({
                position: new google.maps.LatLng(<?= $customers[$i]['latitude'] ?>, <?= $customers[$i]['longitude']?>),
                map: map,
                title: '<?= $customers[$i]['name'] ?>',
                address: '<?= $customers[$i]['address'] ?>',
                url: '<?= $customers[$i]['email'] ?>',
            });

            var onclick = function (objeto_infowindow, marker) {
                var obj = objeto_infowindow;
                return function () {
                    obj.open(map, marker);
                }
            }

            google.maps.event.addListener(object_marker, 'click', onclick(objeto_infowindow['infowindow' + i], object_marker));


            // Keep the marker for later clean up if required
            markerCollections.push(object_marker);



            <?
            }


            } ?>


            autocomplete.addListener('place_changed', function () {
                autoCompeteAddressSelected = true;
                infowindow.close();
                marker.setVisible(false);
                var place = autocomplete.getPlace();
                if (!place.geometry) {
                    autoCompeteAddressSelected = false
                    // User entered the name of a Place that was not suggested and
                    // pressed the Enter key, or the Place Details request failed.
                    // window.alert("No details available for input: '" + place.name + "'");
                    // return;
                }

                var latitude = place.geometry.location.lat();
                var longitude = place.geometry.location.lng();

                $('#latitude').val(latitude);
                $('#longitude').val(longitude)


            });


            autocomplete.setTypes([]);

            document.getElementById('use-strict-bounds')
                .addEventListener('click', function () {
                    console.log('Checkbox clicked! New state=' + this.checked);
                    autocomplete.setOptions({strictBounds: this.checked});
                });
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?= $GoogleApikey ?>&libraries=places&callback=initMap"
            async defer></script>

    <div class='shop_item_preview'>
        <div class="itemcard_list10  itemcard_list">
            <div class='row'>

                <?

                $addressMarkers = '';
                for ($i = 0; $i < count($customers); $i++) {
                    $addressMarkers .= '{lat: ' . $customers[$i]['latitude'] . ', lng: ' . $customers[$i]['longitude'] . '},';

                    $distance = round(calculate_distance($latitude, $longitude, $customers[$i]['latitude'], $customers[$i]['longitude'], "K"), 2);

                    ?>
                    <div class="itemlist10 itemlist col-xs-6 col-sm-4 col-md-4 col-lg-4 col-xlg-3">
                        <div class="itemlist_container">

                            <div class="itemlist_content ">
                                <h3><b>  <?= $customers[$i]['name'] ?></b></h3>
                            </div>
                            <div class="itemlist_content ">
                                <?= $distance . ' KM ' . $GLOBALS['tc']['far']; ?>
                            </div>
                            <br>
                            <div class="itemlist_content ">
                                <?= $customers[$i]['address'] ?>
                            </div>
                            <div class="itemlist_content ">
                                <?= $customers[$i]['post_code'] . ' ' . $customers[$i]['city'] ?>
                            </div>
                            <br>
                            <? if ($customers[$i]['phone_no'] != '') { ?>
                                <div class="itemlist_content item_no">
                                    <b><?= $GLOBALS['tc']['phone_no'] . ":" ?></b> <?= $customers[$i]['phone_no'] ?>
                                </div>
                            <? } ?>
                            <? if ($customers[$i]['fax_no'] != '') { ?>
                                <div class="itemlist_content item_no">
                                    <b><?= $GLOBALS['tc']['fax_no'] . ":" ?></b> <?= $customers[$i]['fax_no'] ?>
                                </div>
                            <? } ?>
                            <div class="itemlist_content item_no">
                                <a href="mailto:<?= $customers[$i]['email'] ?>">  <?= $customers[$i]['email'] ?> </a>
                            </div>


                        </div>

                    </div>


                    <?
                }

                ?>
            </div>
        </div>
    </div>
    <?
}

function shop_dealer_search_edit()
{
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_dealer_search.inc.php';
}

