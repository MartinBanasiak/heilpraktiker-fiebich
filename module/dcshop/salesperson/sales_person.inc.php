<?php
/**
 * @var $IOCContainer \Dice\Dice
 **/
$templating = $IOCContainer->create('DynCom\dc\common\classes\Templating');
if ($GLOBALS['visitor']['frontend_login'] == 1) {

    $currentCustomer = $IOCContainer->create('$CurrCustomer');

    $pdo = $IOCContainer->create('DynCom\dc\common\classes\PDOQueryWrapper');
    $comapnyName = $GLOBALS["shop"]["company"];
    $salesPersonCode = $currentCustomer->getSalespersonCode();

    $pdoHost = getenv('MAIN_MYSQL_DB_HOST');
    $pdoPort = getenv('MAIN_MYSQL_DB_PORT');
    $pdoUser = getenv('MAIN_MYSQL_DB_USER');
    $pdoPass = getenv('MAIN_MYSQL_DB_PASS');
    $pdoSchema = getenv('MAIN_MYSQL_DB_SCHEMA');

    $resultArray = array();
    $companyHasSalesPerson = false;
    try {

        $prepStatement = '
          SELECT 
            id,
            salesperson_code,
            name,
            email,
            phone_no,
            image             
          FROM 
            shop_salesperson  
          WHERE 
                company = :company 
            AND salesperson_code = :salesPersonCode 
        ';

        $params = [
            [':company', $comapnyName, PDO::PARAM_STR],
            [':salesPersonCode', $salesPersonCode, PDO::PARAM_STR],
        ];
        $pdo->setQuery($prepStatement);
        $pdo->prepareQuery();
        $pdo->bindParameters($params);
        $pdo->executePreparedStatement();
        $resultArray = $pdo->getResultArray();

        if (count($resultArray) > 0) {
            $companyHasSalesPerson = true;
        }

    } catch (Exception $e) {
        exit(1);
    }

    if ($companyHasSalesPerson) {
        $imageFileName = $resultArray[0]['image'] ?? '';
        $imageFileFinalPath = $GLOBALS["shop_setup"]["uploaddir_salesperson_images"] . DIRECTORY_SEPARATOR . $imageFileName;
        $imageFilePath = $imageFileName ? rtrim(dirname(dirname(dirname(__DIR__))), '/') . $GLOBALS["shop_setup"]["uploaddir_salesperson_images"] . DIRECTORY_SEPARATOR . $imageFileName : '';
        $showImageTag = !empty($imageFilePath) && file_exists($imageFilePath) && is_readable($imageFilePath);
        ?>

        <div id="sales_person_content" class="main_content_right_home col-xs-12 col-sm-12 col-md-4 col-lg-2-5">
            <div class="site_headline">
                <div class="textcontent">
                    <h2><? echo $templating->getText('sales_person_data'); ?></h2>

                </div>
            </div>
            <div class="gray_box">
                <div class="row">
                    <div class="textcontent">
                        <?
                        if ($showImageTag) {
                            ?>
                            <img alt="" src="<? echo $imageFileFinalPath; ?>">
                            <?
                        }
                        ?>

                    </div>

                    <div class="textcontent">
                        <strong><? echo $resultArray[0]['name']; ?></strong><br>
                        <? echo $resultArray[0]['salesperson_code']; ?><br>
                        <br>
                        <? echo $templating->getText('sales_person_phone');
                        echo $resultArray[0]['phone_no']; ?><br>

                        <? echo $templating->getText('sales_person_email'); ?> <a
                                href="mailto:<? echo $resultArray[0]['email']; ?>"><? echo $resultArray[0]['email']; ?></a><br>
                        <br>

                    </div>
                </div>
            </div>
        </div>


        <?
    } else {
        ?>
        <div class="row">
            <div class="col-sm-4"><? echo $templating->getText('no_sales_person'); ?></div>
        </div>

        <?
    }

} else {

    ?>

    <div class="row">
        <div class="col-sm-4"><? echo $templating->getText('login_is_needed'); ?> </div>
    </div>
    <?
}

?>