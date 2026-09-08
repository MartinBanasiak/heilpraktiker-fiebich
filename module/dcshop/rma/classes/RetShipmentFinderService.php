<?php
namespace DynCom\dc\dcShop\rma\classes;
use DynCom\dc\common\classes\Visitor;
use DynCom\dc\dcShop\classes\Shop;
use DynCom\dc\dcShop\classes\ShopLanguage;
use mysqli;

/**
 * Class RMAShipmentFinderService
 */
class RetShipmentFinderService {

    protected static $findQueryFrame                   = '
            SELECT * FROM shop_view_returnable_shipments_ext
            WHERE (
                    no = ?
              OR
                    order_no = ?
              OR
                    webshop_order_no = ?
              OR (
                            your_reference != \'\'
                      AND
                            your_reference = ?
                    )
              OR
                    invoice_no = ?
              OR
                    nav_order_no = ?
            )
            ';
    protected static $findQueryBaseParamTypeString     = 'ssisss';
    protected static $getAllForCustomerQuery           = '
        SELECT * FROM shop_view_returnable_shipments_ext
        WHERE
          sell_to_customer_no  = ?
          OR
          bill_to_customer_no = ?
    ';
    protected static $getAllForCustomerParamTypeString = 'ss';


    protected $db;
    protected $shop;
    protected $shopLanguage;
    protected $visitor;
    protected $customer;
    protected $inputRequest;
    protected $inputEMail;
    protected $inputPostCode;
    protected $finalFindQuery;
    protected $finalFindQueryParamTypeString;
    protected $foundShipmentCollection;

    /**
     * @param mysqli       $mysqli
     * @param Shop         $shop
     * @param ShopLanguage $shopLanguage
     * @param Visitor      $visitor
     * @param null         $customer
     */
    public function __construct( mysqli $mysqli, Shop $shop, ShopLanguage $shopLanguage, Visitor $visitor, $customer = NULL ) {

        $this->foundShipmentCollection = new RetShipmentCollection();

        if ($mysqli->ping()) {
            $this->db = $mysqli;
        } else {
            throw new \Exception('MySQLi-Connection not pingable');
        }

        if (strlen($shop->code) > 0) {
            $this->shop = $shop;
        } else {
            throw new \Exception('Shop not initialized');
        }

        if (strlen($shopLanguage->code) > 0) {
            $this->shopLanguage = $shopLanguage;
        } else {
            throw new \Exception('ShopLanguage not initialized');
        }

        if (strlen($visitor->id) > 0) {
            $this->visitor = $visitor;
        } else {
            throw new \Exception('Visitor not initialized');
        }

        if (isset($customer) && !(((int)$customer->id) > 0)) {
            throw new \Exception('Optional parameter \'Customer\' set but not a valid user');
        } else {
            $this->customer = $customer;
        }
    }

    /**
     * @param array $userInputArray
     *
     * @return array|null
     */
    public function find( array $userInputArray ) {
        return $this->_find($userInputArray);
    }

    /**
     * @param array $userInputArray
     *
     * @return array|null
     */
    protected function _find( array $userInputArray ) {
        if ($this->shop->shop_typ == 0 || $this->shop->shop_typ == 2) {
            if (!strlen($this->customer->customer_no) > 0) {
                throw new \Exception('Shop-Type is B2B or Salesperson, but no customer given');
                return $this->foundShipmentCollection;
            }
            if ($this->visitor->frontend_login != 1) {
                throw new \Exception('Shop-Typ is B2B or Salesperson, but Visitor is not logged in');
                return $this->foundShipmentCollection;
            }
            if (empty($userInputArray['inputRequest'])) {
                throw new \Exception('No \'inputRequest\' in parameter userInputArray');
                return $this->foundShipmentCollection;
            }
            return $this->_findB2B($userInputArray['inputRequest']);
        } elseif ($this->shop->shop_typ == 1 && $this->visitor->frontend_login == 1) {
            if (empty($userInputArray['inputRequest'])) {
                throw new \Exception('No \'inputRequest\' in parameter userInputArray');
                return $this->foundShipmentCollection;
            }
            return $this->_findB2CLogin($userInputArray['inputRequest']);
        } elseif ($this->shop->shop_typ == 1 && !($this->visitor->frontend_login == 1)) {
            if (empty($userInputArray['inputRequest'])) {
                throw new \Exception('No \'inputRequest\' in parameter userInputArray');
                return $this->foundShipmentCollection;
            }
            if (empty($userInputArray['inputEMail'])) {
                throw new \Exception('Shop is B2C and Visitor has no Login but there is no \'inputEMail\' in parameter userInputArray');
                return $this->foundShipmentCollection;
            }
            if (empty($userInputArray['inputPostCode'])) {
                throw new \Exception('Shop is B2C and Visitor has no Login but there is no \'inputPostCode\' in parameter userInputArray');
                return $this->foundShipmentCollection;
            }
            return $this->_findB2CNoLogin($userInputArray['inputRequest'], $userInputArray['inputEMail'], $userInputArray['inputPostCode']);
        }
        return $this->foundShipmentCollection;
    }

    /**
     * @param $inputRequest
     *
     * @return null|RetShipmentCollection
     */
    protected function _findB2B( $inputRequest ) {

        $query = static::$findQueryFrame;

        //Add to SQL-Statement
        $query .= '
                AND (
                        (
                                sell_to_customer_no != \'\'
                          AND
                                sell_to_customer_no = ?
                        )
                  OR
                        (
                                bill_to_customer_no != \'\'
                          AND
                                bill_to_customer_no = ?
                        )
                )
        ';
        $this->_modifyQueryForDateLimit($query);

        //Prepare statement
        $mysqli = $this->db;
        $stmt   = $mysqli->prepare($query);

        //Check prepared statement
        if ($stmt === FALSE) {
            throw new \Exception('Wrong SQL: ' . $query . ' Error: ' . $mysqli->error, E_USER_ERROR);
            return $this->foundShipmentCollection;
        }

        //Prepare parameter type string
        $paramTypeString = static::$findQueryBaseParamTypeString;
        $paramTypeString .= 'ss';

        //Prepare parameters
        $no         = $mysqli->real_escape_string($inputRequest);
        $custNo     = $mysqli->real_escape_string($this->customer->customer_no);
        $no_numeric = (int)$no;

        //Bind parameters
        $stmt->bind_param($paramTypeString, $no, $no, $no_numeric, $no, $no, $no, $custNo, $custNo);
        //Execute, get result and close
        $stmt->execute();
        $rs  = $stmt->get_result();
        $arr = $rs->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        //Output
        if (empty($arr) || !(count($arr) > 0)) {
            return $this->foundShipmentCollection;
        } else {
            $this->foundShipments = array();
            foreach ($arr as $shipmentArr) {
                $locShpmt = new RetShipmentDocument($this->db);
                if ($locShpmt->mapArrToThis($shipmentArr) > 0) {
                    $locShpmt->setLines();
                    $this->foundShipmentCollection->addShipment($locShpmt);
                } else {
                    throw new \Exception('At least one db-record cannot be mapped to a RetShipmentDocument');
                    return $this->foundShipmentCollection;
                }
            }
            return $this->foundShipmentCollection;
        }
    }

    /**
     * @param $inputRequest
     *
     * @return array|null
     */
    protected function _findB2CLogin( $inputRequest ) {

        $query = static::$findQueryFrame;

        //Add to SQL-Statement
        $query .= '
                AND (
                        shop_code = ?
                  AND
                        language_code = ?
                  AND
                        customer_id = ?
                  AND
                        webshop_order_no > 0
                )
            ';
        $this->_modifyQueryForDateLimit($query);

        //Prepare statement
        $mysqli = $this->db;
        $stmt   = $mysqli->prepare($query);

        //Check prepared statement
        if ($stmt === FALSE) {
            throw new \Exception('Wrong SQL: ' . $query . ' Error: ' . $mysqli->error, E_USER_ERROR);
            return $this->foundShipmentCollection;
        }

        //Prepare parameter type string
        $paramTypeString = static::$findQueryBaseParamTypeString;

        $paramTypeString .= 'ssi';

        //Prepare parameters
        $no        = $mysqli->real_escape_string($inputRequest);
        $shopCode  = $mysqli->real_escape_string($this->shop->code);
        $langCode  = $mysqli->real_escape_string($this->shopLanguage->code);
        $custID    = (int)$mysqli->real_escape_string($this->customer->id);
        $no_number = (int)$no;


        //Bind parameters
        $stmt->bind_param($paramTypeString, $no, $no, $no_number, $no, $no, $no, $shopCode, $langCode, $custID);


        //Execute, get result and close
        $stmt->execute();
        $rs  = $stmt->get_result();
        $arr = $rs->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        //Output
        if (empty($arr) || !(count($arr) > 0)) {
            return $this->foundShipmentCollection;
        } else {
            $this->foundShipments = array();
            foreach ($arr as $shipmentArr) {
                $locShpmt = new RetShipmentDocument($this->db);
                if ($locShpmt->mapArrToThis($shipmentArr) > 0) {
                    $locShpmt->setLines();
                    $this->foundShipmentCollection->addShipment($locShpmt);
                } else {
                    throw new \Exception('At least one db-record cannot be mapped to a RetShipmentDocument');
                    return NULL;
                }
            }
            return $this->foundShipmentCollection;
        }
    }

    /**
     * @param $inputRequest
     * @param $inputEMail
     * @param $inputPostCode
     *
     * @return array|null
     */
    protected function _findB2CNoLogin( $inputRequest, $inputEMail, $inputPostCode ) {

        $query = static::$findQueryFrame;

        //Add to SQL-Statement
        $query .= '
                AND (
                        shop_code = ?
                  AND
                        language_code = ?
                  AND
                        user_email = ?
                  AND
                        ship_to_post_code = ?
                  AND
                        webshop_order_no > 0
                )
            ';
        $this->_modifyQueryForDateLimit($query);

        //Prepare statement
        $mysqli = $this->db;
        $stmt   = $mysqli->prepare($query);

        //Check prepared statement
        if ($stmt === FALSE) {
            throw new \Exception('Wrong SQL: ' . $query . ' Error: ' . $mysqli->error, E_USER_ERROR);
            return $this->foundShipmentCollection;
        }

        //Prepare parameter type string
        $paramTypeString = static::$findQueryBaseParamTypeString;
        $paramTypeString .= 'ssss';

        //Prepare parameters
        $no         = $mysqli->real_escape_string($inputRequest);
        $shopCode   = $mysqli->real_escape_string($this->shop->code);
        $langCode   = $mysqli->real_escape_string($this->shopLanguage->code);
        $email      = addcslashes($mysqli->real_escape_string($inputEMail), '%_');
        $postCode   = addcslashes($mysqli->real_escape_string($inputPostCode), '%_');
        $no_numeric = (int)$no;

        //Bind parameters
        $stmt->bind_param($paramTypeString, $no, $no, $no_numeric, $no, $no, $no, $shopCode, $langCode, $email, $postCode);

        //Execute, get result and close
        $stmt->execute();
        $rs  = $stmt->get_result();
        $arr = $rs->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        //Output
        if (empty($arr) || !(count($arr) > 0)) {
            return $this->foundShipmentCollection;
        } else {
            $this->foundShipments = array();
            foreach ($arr as $shipmentArr) {
                $locShpmt = new RetShipmentDocument($this->db);
                if ($locShpmt->mapArrToThis($shipmentArr) > 0) {
                    $locShpmt->setLines();
                    $this->foundShipmentCollection->addShipment($locShpmt);
                } else {
                    throw new \Exception('At least one db-record cannot be mapped to a RetShipmentDocument');
                    return $this->foundShipmentCollection;
                }
            }
            return $this->foundShipmentCollection;
        }
    }

    /**
     * @param $query
     */
    protected function _modifyQueryForDateLimit( &$query ) {
        //Add posting_date limit for maxDaysBack if necessary
        $maxDays = (int)$this->shop->max_days_shipment_returnable;
        if ($maxDays > 0) {
            $query .= '
                AND
                    posting_date >= (DATE_SUB(NOW(), INTERVAL ' . (string)$maxDays . ' DAY))
                ';
        }
    }

    /**
     * @return null|RetShipmentCollection
     */
    public function getAllForCustomer() {
        return $this->_getAllForCustomer();
    }

    /**
     * @return null|RetShipmentCollection
     */
    protected function _getAllForCustomer() {
        if (!(isset($this->customer) && ($this->customer->id > 0))) {
            return $this->foundShipmentCollection;
        }
        $customerNo       = $this->customer->customer_no;
        $query            = static::$getAllForCustomerQuery;
        $queryParamString = static::$getAllForCustomerParamTypeString;
        $this->_modifyQueryForDateLimit($query);
        $stmt = $this->db->prepare($query);
        $stmt->bind_param($queryParamString, $customerNo, $customerNo);
        //Execute, get result and close
        $stmt->execute();
        $rs  = $stmt->get_result();
        $arr = $rs->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        //Output
        if (empty($arr) || !(count($arr) > 0)) {
            return $this->foundShipmentCollection;
        } else {
            $this->foundShipments = array();
            foreach ($arr as $shipmentArr) {
                $locShpmt = new RetShipmentDocument($this->db);
                if ($locShpmt->mapArrToThis($shipmentArr) > 0) {
                    $locShpmt->setLines();
                    $this->foundShipmentCollection->addShipment($locShpmt);
                } else {
                    throw new \Exception('At least one db-record cannot be mapped to a RetShipmentDocument');
                    return NULL;
                }
            }
            return $this->foundShipmentCollection;
        }

    }
}