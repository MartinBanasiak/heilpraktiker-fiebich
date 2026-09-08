<?
function language_switch_show($sitepart_id)
{
    $pdoHost = getenv('MAIN_MYSQL_DB_HOST');
    $pdoPort = getenv('MAIN_MYSQL_DB_PORT');
    $pdoUser = getenv('MAIN_MYSQL_DB_USER');
    $pdoPass = getenv('MAIN_MYSQL_DB_PASS');
    $pdoSchema = getenv('MAIN_MYSQL_DB_SCHEMA');

    $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($pdoHost, $pdoPort, $pdoSchema, $pdoUser, $pdoPass);

    $currentMainSiteId = $GLOBALS['site']['id'];
    $prepStatement = '
          SELECT 
            id,
            code,
            name,
            std_main_navigation_id,
            related_language_codes
          FROM 
            main_language  
          WHERE 
                main_site_id = :mainSiteId 
              AND 
                active = 1
        ';

    $params = [
        [':mainSiteId', $currentMainSiteId, PDO::PARAM_STR],
    ];
    $pdo->setQuery($prepStatement);
    $pdo->prepareQuery();
    $pdo->bindParameters($params);
    $pdo->executePreparedStatement();
    $siteLanguagesArray = $pdo->getResultArray();
    $languageListHtml = '';
    $currentLanguageId = $GLOBALS['language']['id'];
    if (count($siteLanguagesArray) > 1) // the site has more than 1 language
    {
        $selectedList = '';
        $languageOptionListHtml = '';
        if (isset($_SESSION['selectedLanguageId']) && $_SESSION['selectedLanguageId'] != '') {
            for ($i = 0; $i < count($siteLanguagesArray); $i++) {

                $mainNavigation = get_main_navigation_code($siteLanguagesArray[$i]['std_main_navigation_id'], $pdo);

                if ($mainNavigation[0]['code'] != '') {
                    $url = '/' . customizeUrl(false) . $siteLanguagesArray[$i]['code'] . '/' . $mainNavigation[0]['code'] . '/';
                } else {
                    $url = '/' . customizeUrl(false) . $siteLanguagesArray[$i]['code'] . '/';
                }


                if ($siteLanguagesArray[$i]['id'] == $currentLanguageId) {
                    $selectedList = ' <li class="active"><a href="' . $url . '">' . $siteLanguagesArray[$i]['name'] . '</a></li> ';
                } else {
                    $languageOptionListHtml .= ' <li><a href="' . $url . '">' . $siteLanguagesArray[$i]['name'] . '</a></li> ';
                }
            }
        } else {
            for ($i = 0; $i < count($siteLanguagesArray); $i++) {


                $mainNavigation = get_main_navigation_code($siteLanguagesArray[$i]['std_main_navigation_id'], $pdo);

                if ($mainNavigation[0]['code'] != '') {
                    $url = '/' . customizeUrl(false) . $siteLanguagesArray[$i]['code'] . '/' . $mainNavigation[0]['code'] . '/';
                } else {
                    $url = '/' . customizeUrl(false) . $siteLanguagesArray[$i]['code'] . '/';
                }

                if ($siteLanguagesArray[$i]['id'] == $currentLanguageId) {
                    $selectedList = ' <li class="active"><a href="' . $url . '">' . $siteLanguagesArray[$i]['name'] . '</a></li> ';
                } else {
                    $languageOptionListHtml .= ' <li><a href="' . $url . '">' . $siteLanguagesArray[$i]['name'] . '</a></li> ';
                }
            }
        }


        $languageListHtml = '<div class="language_switch">
            <a href="#" class="language_switch_button">
                '.$GLOBALS['tc']['language_switch_button'].' <i class="fa fa-angle-down" aria-hidden="true"></i>
            </a>
        <div class="list_language_switch"><ul id="language_switch" >' . $selectedList . $languageOptionListHtml . '</ul></div></div>';
    }
    echo $languageListHtml;

}

function language_switch_edit()
{
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_language_switch.inc.php';
}

function get_main_navigation_code($mainNavigationId, $pdo)
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
        [':mainNavigationId', $mainNavigationId, PDO::PARAM_STR],
    ];
    $pdo->setQuery($prepStatement);
    $pdo->prepareQuery();
    $pdo->bindParameters($params);
    $pdo->executePreparedStatement();
    $mainNavigation = $pdo->getResultArray();
    return $mainNavigation;
}
