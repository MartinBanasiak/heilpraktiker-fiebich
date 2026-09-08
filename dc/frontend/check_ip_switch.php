<?php

if ((!isset($_SESSION['check_ip_switch']) || ($GLOBALS['site']['code'] != $_SESSION['current_site_code'])) && !$_SESSION['check_ip_switch'] = false) {

    $ipAddress = get_client_ip_address();
    //$USA_ipAddressV4 = "72.229.28.185";
    // $USA_ipAddressV6 = "2607:f0d0:1002:0051:0000:0000:0000:0004";
    $dataBaseFileBath = rtrim(dirname(dirname(__DIR__)), '/') . '/config/geolocation/';
    $countreyName = '';
    $countreyCode = '';

    if (file_exists($dataBaseFileBath . "GeoIP.dat") && file_exists($dataBaseFileBath . "GeoIPv6.dat")) {

        if (!(strpos($ipAddress, ":") > -1)) {
            // Code for IPv4 address $ip_address...
            $gi = geoip_open($dataBaseFileBath . "GeoIP.dat", GEOIP_STANDARD);
            $countryCode = geoip_country_code_by_addr($gi, $ipAddress);

        } else {
            // Code for IPv6 address $ip_address...
            $gi = geoip_open($dataBaseFileBath . "GeoIPv6.dat", GEOIP_STANDARD);
            $countryCode = geoip_country_code_by_addr_v6($gi, $ipAddress);

        }
        $siteData = array();
        if ($countryCode != "") {
            $siteData = get_site_from_domain_countryCode($countryCode);
        }

        //&& $siteData['code'] != $GLOBALS['site']['code']
        if (count($siteData) > 0) {
            $setup["std_main_site_id"] = $GLOBALS['site']['id'];
            $GLOBALS['site']['code'] = $siteData['code'];
            $mainLanguage = get_language_by_site_id($siteData['id']);
            $GLOBALS['language']['id'] = $mainLanguage[0]['id'];
            $redirectUrl = '/' . customizeUrl(false)  . $mainLanguage[0]['code'] . '/';
            $_SESSION['check_ip_switch'] = true;
            $_SESSION['current_site_code'] = $siteData['code'];
            universal_redirect($redirectUrl);
        } else {
            $_SESSION['check_ip_switch'] = false;
            $_SESSION['current_site_code'] = $GLOBALS['site']['code'];

        }
    }

} else {
    $_SESSION['check_ip_switch'] = false;
    $_SESSION['current_site_code'] = $GLOBALS['site']['code'];
}