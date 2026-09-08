<?php

$pdoHost = getenv('MAIN_MYSQL_DB_HOST');
$pdoPort = getenv('MAIN_MYSQL_DB_PORT');
$pdoUser = getenv('MAIN_MYSQL_DB_USER');
$pdoPass = getenv('MAIN_MYSQL_DB_PASS');
$pdoSchema = getenv('MAIN_MYSQL_DB_SCHEMA');

$pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($pdoHost, $pdoPort, $pdoSchema, $pdoUser, $pdoPass);

$currentMainSiteId = $GLOBALS['site']['id'];


if ((!isset($_SESSION['selectedLanguageId']) || $_SESSION['selectedLanguageId'] == '') && $GLOBALS['site']['use_browser_language_detection'] == 1) {

    $siteLanguagesArray = get_language_by_site_id($currentMainSiteId);
    $mainLanguage = array();
    if (count($siteLanguagesArray) > 1) // the site has more than 1 language
    {
        // if main language array is empty then i will do nothing because it should display default language
        // if the mainlanguage array has a language i should redirect the website to this prefared language

        $acceptLangageHeader = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        $negotiator = new \Negotiation\LanguageNegotiator();
        $lastLanguageFilter = array();
        $newPriorities = array();
        for ($i = 0; $i < count($siteLanguagesArray); $i++) {
            if ($siteLanguagesArray[$i]['related_language_codes'] != '') {
                $priorities = explode(",", $siteLanguagesArray[$i]['related_language_codes']);
                $bestLanguage = $negotiator->getBest($acceptLangageHeader, $priorities);
                if (isset($bestLanguage)) {
                    $newPriorities[count($newPriorities)] = $bestLanguage->getType();
                    $lastLanguageFilter[$bestLanguage->getType()] = $i;
                }
            }
        }
        if (count($newPriorities) > 0) {
            $bestLanguage = $negotiator->getBest($acceptLangageHeader, $newPriorities);
            $mainLanguage = $siteLanguagesArray[$lastLanguageFilter[$bestLanguage->getType()]];

            $_SESSION['selectedLanguageId'] = $mainLanguage['id'];

            if ($mainLanguage['id'] != $GLOBALS['language']['id']) // no need to redirect incase the browser language is the same of the current website language
            {
                $prepStatement = '
                      SELECT 
                        code
                      FROM 
                        main_navigation  
                      WHERE 
                            id = :mainNavigationId 
                    ';

                $params = [
                    [':mainNavigationId', $mainLanguage['std_main_navigation_id'], PDO::PARAM_STR],
                ];
                $pdo->setQuery($prepStatement);
                $pdo->prepareQuery();
                $pdo->bindParameters($params);
                $pdo->executePreparedStatement();
                $mainNavigation = $pdo->getResultArray();
                if ($mainNavigation[0]['code'] != '') {
                    $redirectUrl = '/' . customizeUrl(false)  . $mainLanguage['code'] . '/' . $mainNavigation[0]['code'] . '/';
                } else {
                    $redirectUrl = '/' . customizeUrl(false)  . $mainLanguage['code'] . '/';
                }

                universal_redirect($redirectUrl);
            }
        }
    }
}

