<?php
if (isset($_REQUEST['API_KEY']) && isset($_REQUEST['CUSTOMER_KEY']) && $_REQUEST['API_KEY'] == 'AIzaSyCq5h8A-SYXY9YEcuiSnmGfGZUF33LX470') {
    $baseDirectory = rtrim(dirname(dirname(dirname(__DIR__))), '/');
    include($baseDirectory . '/vendor/autoload.php');

    //Load environment variables from config if exists
    $envDir = rtrim($baseDirectory, '/') . '/config';
    if (is_dir($envDir)) {
        $dotenv = new \Dotenv\Dotenv($envDir);
        $dotenv->load();
    }

    $pdoHost = getenv('MAIN_MYSQL_DB_HOST');
    $pdoPort = getenv('MAIN_MYSQL_DB_PORT');
    $pdoUser = getenv('MAIN_MYSQL_DB_USER');
    $pdoPass = getenv('MAIN_MYSQL_DB_PASS');
    $pdoSchema = getenv('MAIN_MYSQL_DB_SCHEMA');

    $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($pdoHost, $pdoPort, $pdoSchema, $pdoUser, $pdoPass);


    $prepStatement = '
            SELECT
             std_main_language_id
            FROM
              main_site
            WHERE  
                google_data_studio_key = :customerKey
          ';
    $params = [
        [':customerKey', $_REQUEST['CUSTOMER_KEY'], PDO::PARAM_STR],
    ];
    $pdo->setQuery($prepStatement);
    $pdo->prepareQuery();
    $pdo->bindParameters($params);
    $pdo->executePreparedStatement();
    $site = $pdo->getResultArray();
    $site = $site[0];
    if ($site === null || $site['std_main_language_id'] == '') {
        die();
    }

    $prepStatement = '
            SELECT
             main_language.company, main_language.shop_code, shop_shop.shipping_group_code
                FROM
                  main_language
                    INNER JOIN 
                      shop_shop 
                  ON main_language.company = shop_shop.company AND main_language.shop_code = shop_shop.code
              where  
                main_language.id = :languageId
          ';
    $params = [
        [':languageId', $site['std_main_language_id'], PDO::PARAM_STR],
    ];
    $pdo->setQuery($prepStatement);
    $pdo->prepareQuery();
    $pdo->bindParameters($params);
    $pdo->executePreparedStatement();
    $language = $pdo->getResultArray();
    $language = $language[0];


    /*
    // this query should be used with old version for  Shipping option table


    $prepStatement = '
             SELECT
                   shop_sales_header.shop_code, shop_sales_header.language_code , shop_sales_header.order_date, shop_sales_header.order_no, shop_sales_header.subtotal, shop_sales_header.total,
                       shop_shipping_option.description  as \'shipping_description\'  , shop_sales_header.shipping_option_line_no, shop_sales_header.shipping_cost,
                       shop_payment_option.description as \'payment_description\' ,  shop_sales_header.payment_option_line_no, shop_sales_header.ship_to_country,
                        shop_sales_header.coupon_amount, shop_sales_header.coupon_header_code
             FROM
                   shop_sales_header
                       LEFT JOIN
                           shop_shipping_option
                               ON shop_sales_header.company = shop_shipping_option.company
                                 AND
                                     shop_sales_header.shop_code = shop_shipping_option.shop_code
                                 AND
                                     shop_sales_header.shipping_option_line_no = shop_shipping_option.line_no

                     LEFT JOIN
                           shop_payment_option
                             ON shop_sales_header.company = shop_payment_option.company
                                AND
                                     shop_sales_header.shop_code = shop_payment_option.shop_code
                                AND
                                     shop_sales_header.payment_option_line_no = shop_payment_option.line_no

             WHERE shop_sales_header.company = :company AND  shop_sales_header.shop_code = :shopCode ';*/

    // Shipping Option Table Structure
    $prepStatement = '
               
                SELECT 
                  shop_sales_header.shop_code, shop_sales_header.language_code , shop_sales_header.order_date, shop_sales_header.order_no, shop_sales_header.subtotal, shop_sales_header.total,
                      shop_shipping_option.description  as \'shipping_description\'  , shop_sales_header.shipping_option_line_no, shop_sales_header.shipping_cost, 
                      shop_payment_option.description as \'payment_description\' ,  shop_sales_header.payment_option_line_no, shop_sales_header.ship_to_country,
                       shop_sales_header.coupon_amount, shop_sales_header.coupon_header_code
            FROM
                  shop_sales_header  
                      LEFT JOIN
                          shop_shipping_option 
					          ON shop_sales_header.company = shop_shipping_option.company  
				                  AND 
                            shop_sales_header.shipping_option_line_no = shop_shipping_option.line_no
                            AND shop_shipping_option.shipping_group_code = :shippingGroupCode
                           
                    LEFT JOIN
                          shop_payment_option 
					        ON shop_sales_header.company = shop_payment_option.company  
                               AND
					                shop_sales_header.shop_code = shop_payment_option.shop_code 
                               AND 
                                    shop_sales_header.payment_option_line_no = shop_payment_option.line_no
                              AND
                                    shop_sales_header.language_code = shop_payment_option.language_code
              
            WHERE shop_sales_header.company = :company AND  shop_sales_header.shop_code = :shopCode


          ';
    $params = [
        [':company', $language['company'], PDO::PARAM_STR],
        [':shopCode', $language['shop_code'], PDO::PARAM_STR],
        [':shippingGroupCode', $language['shipping_group_code'], PDO::PARAM_STR],
    ];
    $pdo->setQuery($prepStatement);
    $pdo->prepareQuery();
    $pdo->bindParameters($params);
    $pdo->executePreparedStatement();
    $orders = $pdo->getResultArray();

    $jsonCode = '';

    foreach ($orders as $order) {
        $responseArray = array(
            'shopCode' => $order['shop_code'],
            'languageCode' => $order['language_code'],
            'orderDate' => $order['order_date'],
            'orderNo' => $order['order_no'],
            'subTotal' => $order['subtotal'],
            'total' => $order['total'],
            'shippingOptionLineNo' => $order['shipping_option_line_no'],
            'shippingDescription' => $order['shipping_description'],
            'shippingCost' => $order['shipping_cost'],
            'paymentOptionLineNo' => $order['payment_option_line_no'],
            'paymentDescription' => $order['payment_description'],
            'shipToCountry' => $order['ship_to_country'],
            'couponAmount' => $order['coupon_amount'],
            'couponHeaderCode' => $order['coupon_header_code'],

        );
        $jsonCode .= json_encode($responseArray) . ",";
    }

    $jsonCode = rtrim($jsonCode, ',');
    $reponseData = '{"orders":[' . $jsonCode . ']}';
    echo $reponseData;

} else {
    die();
}