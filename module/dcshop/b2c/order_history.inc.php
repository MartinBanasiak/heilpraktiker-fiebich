<?
switch ($_GET["action_id"]) {
    case 'card':
        show_order_history();
        break;
    default:
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'order_history_listform.inc.php';
        break;
}
function show_order_history() {
    if ($_REQUEST["input_id"] <> '') {

        $pdoHost = getenv('MAIN_MYSQL_DB_HOST');
        $pdoPort = getenv('MAIN_MYSQL_DB_PORT');
        $pdoUser = getenv('MAIN_MYSQL_DB_USER');
        $pdoPass = getenv('MAIN_MYSQL_DB_PASS');
        $pdoSchema = getenv('MAIN_MYSQL_DB_SCHEMA');

        $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($pdoHost, $pdoPort, $pdoSchema, $pdoUser, $pdoPass);

        $prepStatement = " SELECT *
				  FROM shop_sales_header
				  WHERE id = :id
				  	AND shop_customer_id = :shop_customer_id
				  	AND shop_customer_id != ''
				  	AND shop_customer_id != 0
				  	AND shop_customer_id IS NOT NULL
				  	AND order_error != 1
				  LIMIT 1
        ";
        $params = [
            [':id', $_POST["input_id"], PDO::PARAM_STR],
            [':shop_customer_id', $GLOBALS["shop_customer"]["id"], PDO::PARAM_STR],
        ];
        $pdo->setQuery($prepStatement);
        $pdo->prepareQuery();
        $pdo->bindParameters($params);
        $pdo->executePreparedStatement();
        $result = $pdo->getResultArray();


        if (count($result) == 1) {
            $sales_header      = $result[0];
            if(!$sales_header['dc_order'])
            {
                $query             = "SELECT shop_item_id AS 'id', '" . $GLOBALS["site"]["id"] . "' AS 'main_site_id', '" . $GLOBALS["language"]["id"] . "' AS 'main_language_id',
							 item_no, description, summary, allow_invoice_disc, list_price, unit_price AS 'customer_price', quantity AS 'basket_quantity',
							 line_amount
					  FROM shop_sales_line
					  WHERE shop_sales_header_id = '" . $sales_header["id"] . "'";
                $sales_line_result = @mysqli_query($GLOBALS['mysql_con'], $query);
                $query2            = "SELECT shop_sales_line.quantity AS 'basket_quantity',shop_sales_line.unit_price AS 'customer_price',shop_item.vat_prod_posting_group FROM shop_sales_line LEFT JOIN shop_item ON shop_item.id=shop_sales_line.shop_item_id WHERE shop_sales_line.shop_sales_header_id = " . $sales_header["id"];#
                $slr               = @mysqli_query($GLOBALS['mysql_con'], $query2);
            }
            else
            {
                $isCouponOrder =  true;
                $currentShopLang = $GLOBALS['shop_language'];
                $digitalCouponActive = $currentShopLang['digital_coupon_active'];
                if ($digitalCouponActive == 0) {
                    $digitalCouponActive = 1;
                }

                $couponQuery = "
                
                    select 
                        shop_coupon_line.coupon_code,
                        shop_digital_coupon.message,
                        shop_language.digital_coupon_background_".$digitalCouponActive." as image_data,
                        shop_sales_header.total 
                    
                    from 
                        shop_digital_coupon
                        
                            join shop_coupon_line 
                                on shop_digital_coupon.shop_coupon_line_id = shop_coupon_line.id
                            join
                                shop_sales_header
                                    on shop_coupon_line.coupon_code = shop_sales_header.coupon_code and shop_coupon_line.coupon_code = '".$sales_header['coupon_code']."'
                            join 
                                shop_language
                                on shop_digital_coupon.background_image = shop_language.digital_coupon_active
                        
                     where 
                     shop_coupon_line.company = '".$GLOBALS['shop']['company']."' 
                        and shop_coupon_line.shop_code =  '".$GLOBALS['shop']['code']."' 
                        and shop_coupon_line.language_code = '".$currentShopLang['code']."' 
                ";

                $couponData   = @mysqli_query($GLOBALS['mysql_con'], $couponQuery);

            }


            require_once __DIR__ . DIRECTORY_SEPARATOR . 'order_history_cardform.inc.php';
        }
    } else {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'order_history_listform.inc.php';
    }
}

?>