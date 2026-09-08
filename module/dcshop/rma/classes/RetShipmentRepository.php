<?php
namespace DynCom\dc\dcShop\rma\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericDBQueryWrapperInterface;
use DynCom\dc\dcShop\classes\Customer;
use DynCom\dc\dcShop\classes\ShopLanguage;
use DynCom\dc\dcShop\interfaces\DocumentRepository;
use DynCom\dc\dcShop\interfaces\GenericDocumentInterface;
use DynCom\dc\dcShop\traits\documentRepositoryTrait;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 14.01.2015
 * Time: 03:24
 */
class RetShipmentRepository implements DocumentRepository {

    use documentRepositoryTrait;

    /**
     * RetShipmentRepository constructor.
     * @param GenericDBQueryWrapperInterface $db
     * @param RetShipmentConfig $config
     * @param RetShipmentLineRepository $lineRepository
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param RetShipmentCollection $collection
     * @param $cacheAll
     */
    public function __construct( GenericDBQueryWrapperInterface $db, RetShipmentConfig $config, RetShipmentLineRepository $lineRepository, CriteriaHelperInterface $criteriaValidationService, RetShipmentCollection $collection, $cacheAll ) {
        $this->db                        = $db;
        $this->config                    = $config;
        $this->lineRepository            = $lineRepository;
        $this->linesConfig               = $this->lineRepository->getConfig();
        $this->collection                = $collection;
        $this->collectionEntryClassName  = $this->collection->getEntryClassName();
        $this->criteriaValidationService = $criteriaValidationService;

        $this->cacheAll                  = $cacheAll;
    }

    /**
     * @param GenericDocumentInterface $instance
     * @return bool
     */
    public function setLinesForDocument( GenericDocumentInterface $instance ) {
        return $this->_setLinesForRetShipmentDocument($instance);
    }

    /**
     * @param RetShipmentDocument $instance
     * @return bool
     */
    protected function _setLinesForRetShipmentDocument( RetShipmentDocument $instance ) {
        return $this->_setLinesForDocument($instance);
    }

    /**
     * @return RetShipmentDocument
     */
    public function getNullObject() {
        return new RetShipmentDocument($this->config,$this->linesConfig,$this->criteriaValidationService);
    }

    /**
     * @param RetShipmentDocument $shipment
     * @return bool|null
     */
    public function unsetReturnOrder( RetShipmentDocument $shipment ) {
        return $this->_unsetReturnOrder($shipment);
    }

    /**
     * @param RetShipmentDocument $shipment
     */
    protected function _unsetReturnOrder( RetShipmentDocument $shipment ) {
        $orderNo = $shipment->return_order_shop_no;
        $shipment->unsetReturnOrder();
        $this->_updateSingle($shipment);
        if(!empty($orderNo)) {
            $this->_releaseOrderNo($orderNo);
        }
    }

    /**
     * @param $instance
     * @return bool
     * @throws \Exception
     */
    protected function _updateSingle( $instance ) {
        $className = $this->_getCollectionEntryClassName();
        if (!($instance instanceof $className)) {
            throw new \Exception('Parameter \'instance\' is not an instance of class \'' . $className . '\'');
            return FALSE;
        }

        $tableName = $this->config->getBaseTableName();

        $mappedFieldsArr = array(
            array('name' => 'id', 'type' => 'INT'),
            array('name' => 'company', 'type' => 'VARCHAR'),
            array('name' => 'no', 'type' => 'VARCHAR'),
            array('name' => 'posting_date', 'type' => 'DATE'),
            array('name' => 'order_no', 'type' => 'VARCHAR'),
            array('name' => 'webshop_order_no', 'type' => 'INT'),
            array('name' => 'your_reference', 'type' => 'VARCHAR'),
            array('name' => 'sell_to_customer_no', 'type' => 'VARCHAR'),
            array('name' => 'bill_to_customer_no', 'type' => 'VARCHAR'),
            array('name' => 'sell_to_name', 'type' => 'VARCHAR'),
            array('name' => 'sell_to_name_2', 'type' => 'VARCHAR'),
            array('name' => 'sell_to_address', 'type' => 'VARCHAR'),
            array('name' => 'sell_to_address_2', 'type' => 'VARCHAR'),
            array('name' => 'sell_to_post_code', 'type' => 'VARCHAR'),
            array('name' => 'sell_to_city', 'type' => 'VARCHAR'),
            array('name' => 'sell_to_country', 'type' => 'VARCHAR'),
            array('name' => 'sell_to_contact', 'type' => 'VARCHAR'),
            array('name' => 'bill_to_name', 'type' => 'VARCHAR'),
            array('name' => 'bill_to_name_2', 'type' => 'VARCHAR'),
            array('name' => 'bill_to_address', 'type' => 'VARCHAR'),
            array('name' => 'bill_to_address_2', 'type' => 'VARCHAR'),
            array('name' => 'bill_to_post_code', 'type' => 'VARCHAR'),
            array('name' => 'bill_to_city', 'type' => 'VARCHAR'),
            array('name' => 'bill_to_country', 'type' => 'VARCHAR'),
            array('name' => 'bill_to_contact', 'type' => 'VARCHAR'),
            array('name' => 'ship_to_name', 'type' => 'VARCHAR'),
            array('name' => 'ship_to_name_2', 'type' => 'VARCHAR'),
            array('name' => 'ship_to_address', 'type' => 'VARCHAR'),
            array('name' => 'ship_to_address_2', 'type' => 'VARCHAR'),
            array('name' => 'ship_to_post_code', 'type' => 'VARCHAR'),
            array('name' => 'ship_to_city', 'type' => 'VARCHAR'),
            array('name' => 'ship_to_country', 'type' => 'VARCHAR'),
            array('name' => 'ship_to_contact', 'type' => 'VARCHAR'),
            array('name' => 'shipment_method', 'type' => 'VARCHAR'),
            array('name' => 'request_mail', 'type' => 'VARCHAR'),
            array('name' => 'request_shop_code', 'type' => 'VARCHAR'),
            array('name' => 'request_language_code', 'type' => 'VARCHAR'),
            array('name' => 'send_request', 'type' => 'TINYINT'),
            array('name' => 'return_order_insert', 'type' => 'TINYINT'),
            array('name' => 'return_order', 'type' => 'TINYINT'),
            array('name' => 'return_shop_code', 'type' => 'VARCHAR'),
            array('name' => 'return_language_code', 'type' => 'VARCHAR'),
            array('name' => 'return_order_reference', 'type' => 'VARCHAR'),
            array('name' => 'return_order_shop_no', 'type' => 'VARCHAR'),
            array('name' => 'return_order_token', 'type' => 'VARCHAR'),
            array('name' => 'to_delete', 'type' => 'TINYINT')
        );

        $primary = array('id');
        if (is_array($this->config->getAltPrimary())) {
            $primary = $this->config->getAltPrimary();
        }
        if (count($primary) < 1) {
            throw new \Exception('No primary key available');
            return FALSE;
        }
        $primaryArr = array();
        foreach ($primary as $keyFieldName) {
            if (!isset($instance->$keyFieldName)) {
                throw new \Exception('Instance lacks value for unique key field \'' . $keyFieldName . '\'');
                return FALSE;
            } else {
                $primaryArr[$keyFieldName] = $instance->$keyFieldName;
            }
        }
        $query = 'UPDATE `' . $tableName . '` SET ';
        $i     = 0;
        foreach ($mappedFieldsArr as $fieldArr) {
            $propName = $fieldArr['name'];
            if (!in_array($propName, $primary) && isset($instance->$propName)) {
                if ($i > 0) {
                    $query .= ',';
                }
                if (MySQLTypeIsText($fieldArr['type'])) {
                    $query .= '`' . $propName . '` = ' . $this->db->escapeString($instance->$propName) . ' ';
                } elseif ($fieldArr['type'] == 'DECIMAL' || $fieldArr['type'] == 'FLOAT' || $fieldArr['type'] == 'DOUBLE') {
                    $query .= '`' . $propName . '` = ' . (float)$instance->$propName . ' ';
                } elseif (stringEndsWith($fieldArr['type'], 'INT')) {
                    $query .= '`' . $propName . '` = ' . (int)$instance->$propName . ' ';
                } elseif (MySQLTypeIsDate($fieldArr['type'])) {
                    $query .= '`' . $propName . '` = \'' . $instance->$propName . '\' ';
                }
                ++$i;
            }
        }
        $query .= 'WHERE ';
        $i = 0;
        foreach ($primaryArr as $name => $value) {
            foreach ($mappedFieldsArr as $fieldArr) {
                if ($fieldArr['name'] == $name) {
                    $type = $fieldArr['type'];
                    break;
                }
            }
            if (empty($type)) {
                throw new \Exception('No type for primary key field \'' . $name . '\'');
                return FALSE;
            }
            if ($i > 0) {
                $query .= 'AND ';
            }
            if (MySQLTypeIsText($type)) {
                $query .= '`' . $name . '` = ' . $this->db->escapeString($value) . ' ';
            } elseif ($type == 'DECIMAL' || $type == 'FLOAT' || $type == 'DOUBLE') {
                $query .= '`' . $name . '` = \'' . (float)$value . '\' ';
            } elseif (stringEndsWith($type, 'INT')) {
                $query .= '`' . $name . '` = \'' . (int)$value . '\' ';
            } elseif (MySQLTypeIsDate($type)) {
                $query .= '`' . $name . '` = \'' . $value . '\' ';
            }
            ++$i;
        }
        if (!$this->db->setQuery($query)) {
            throw new \Exception('Query could not be prepared: ' . $query);
            return FALSE;
        }
        if (!$this->db->doQuery()) {
            $this->db->closeStatement();
            throw new \Exception('Query could not be executed: ' . $query);
            return FALSE;
        }
        $this->db->closeStatement();
        $this->_add($instance, TRUE);
        return TRUE;
    }

    /**
     * @param RetShipmentDocument $shipment
     * @param $insert
     * @param ShopLanguage $shopLanguage
     * @param $reference
     * @param string $token
     * @return bool
     */
    public function setReturnOrder( RetShipmentDocument $shipment, $insert, ShopLanguage $shopLanguage, $reference, $token = '' ) {
        return $this->_setReturnOrder($shipment, $insert, $shopLanguage, $reference, $token);
    }

    /**
     * @param RetShipmentDocument $shipment
     * @param bool                $insert
     * @param ShopLanguage        $shopLanguage
     * @param string              $reference
     * @param string              $token
     *
     * @return bool
     */
    protected function _setReturnOrder( RetShipmentDocument &$shipment, $insert, ShopLanguage $shopLanguage, $reference, $token = '' ) {
        $orderNo = $this->_getNextOrderNo();
        $this->_setOrderNoUsed($orderNo);
        if ($shipment->setReturnOrder($orderNo, $insert, $shopLanguage, $reference, $token)) {
            return $shipment;
        }
        $this->_releaseOrderNo($orderNo);
        return FALSE;
    }

    /**
     * @return int
     * @throws \Exception
     */
    protected function _getNextOrderNo() {
        $query = 'SELECT `ret_order_no` AS \'max_no\' FROM `shop_used_ret_shipment_nos` ORDER BY `ret_order_no` DESC LIMIT 1';
        $this->db->setQuery($query);
        if (!$this->db->doQuery()) {
            $this->db->closeStatement();
            throw new \Exception('Query could not be executed: ' . $query);
        }
        if(!($resArr = $this->db->getResultArray())) {
            $this->db->closeStatement();
            return 1001;
            //throw new \Exception('No array was returned from the query: ' . $this->db->getErrorMessage() . ' || ' . $query);
        }

        $maxNo = (int)$resArr[0]['max_no'];
        if (!$maxNo > 0) {
            $nextNo = 1001;
        } else {
            $nextNo = $maxNo + 1;
        }
        $this->db->closeStatement();
        return $nextNo;
    }

    /**
     * @param $orderNo
     * @throws \Exception
     */
    protected function _setOrderNoUsed($orderNo) {
        $query = 'INSERT INTO shop_used_ret_shipment_nos (ret_order_no,datetime_used) VALUES ('.$this->db->escapeString($orderNo).',CURDATE())';
        $this->db->setQuery($query);
        if (!($this->db->doQuery())) {
            throw new \Exception('Could not execute query. DB-Error: ' . $this->db->getErrorMessage() . ' Query: ' . $query);
        }
        $this->db->closeStatement();
    }

    /**
     * @param $orderNo
     * @throws \Exception
     */
    protected function _releaseOrderNo($orderNo) {
        $query = 'DELETE FROM shop_used_ret_shipment_nos WHERE ret_order_no = ' . $this->db->escapeString($orderNo);
        $this->db->setQuery($query);
        if (!($this->db->doQuery())) {
            throw new \Exception('Could not execute query. DB-Error: ' . $this->db->getErrorMessage() . ' Query: ' . $query);
        }
        $this->db->closeStatement();
    }

    /**
     * @param Customer $customer
     * @param int $maxDaysBack
     * @return mixed|RetShipmentCollection
     */
    public function getAllForCustomer( Customer $customer, $maxDaysBack = 0 ) {
        return $this->_getAllForCustomer($customer,$maxDaysBack);
    }

    /**
     * @param Customer $customer
     * @param int $maxDaysBack
     * @return RetShipmentCollection
     */
    protected function _getAllForCustomer( Customer $customer, $maxDaysBack = 0 ) {

        $resultCollection = $this->collection->getEmptyCollection();
        if (!strlen($customer->customer_no) > 0) {
            return $resultCollection;
        }
        $customerNo = $customer->customer_no;

        $criteria = array(
            array(
                array('sell_to_customer_no', '!=', ''),
                /*AND*/
                array('sell_to_customer_no', '=', $customerNo)
            ),
            /*OR*/
            array(
                array('bill_to_customer_no', '!=', ''),
                /*AND*/
                array('bill_to_customer_no', '=', $customerNo)
            )
        );
        if ((int)$maxDaysBack > 0) {
            $curDate       = new \DateTime();
            $cutoffDate    = $curDate->sub(new \DateInterval('P' . (int)$maxDaysBack . 'D'));
            $criteria[0][] = array('posting_date', '>=', $cutoffDate->format('Y-m-d'));
            $criteria[1][] = array('posting_date', '>=', $cutoffDate->format('Y-m-d'));
        }
        $coll = $this->_findByCriteria($criteria);
        //print_r($coll);
        return $coll;
    }

    /**
     * @param |Customer $customer
     * @param $searchTerm
     * @param int $maxDaysBack
     * @return mixed
     */
    public function searchB2B( Customer $customer, $searchTerm, $maxDaysBack = 0 ) {
        return $this->_searchB2B($customer, $searchTerm, $maxDaysBack);
    }

    /**
     * @param |Customer $customer
     * @param $searchTerm
     * @param int $maxDaysBack
     * @return mixed
     */
    protected function _searchB2B( Customer $customer, $searchTerm, $maxDaysBack = 0 ) {
        $resultCollection = $this->collection->getEmptyCollection();
        if (!strlen($customer->customer_no) > 0) {
            return $resultCollection;
        }
        if (!(strlen($searchTerm) > 0)) {
            return $this->_getAllForCustomer($customer);
        }
        $criteria = array(
            array(
                array('no', '=', $searchTerm),
                /*AND*/
                array('sell_to_customer_no', '!=', ''),
                /*AND*/
                array('sell_to_customer_no', '=', $customer->customer_no)
            ),
            /*OR*/
            array(
                array('order_no', '=', $searchTerm),
                /*AND*/
                array('sell_to_customer_no', '!=', ''),
                /*AND*/
                array('sell_to_customer_no', '=', $customer->customer_no)
            ),
            /*OR*/
            array(
                array('webshop_order_no', '=', $searchTerm),
                /*AND*/
                array('sell_to_customer_no', '!=', ''),
                /*AND*/
                array('sell_to_customer_no', '=', $customer->customer_no)
            ),
            /*OR*/
            array(
                array('your_reference', '!=', ''),
                /*AND*/
                array('your_reference', '=', $searchTerm),
                /*AND*/
                array('sell_to_customer_no', '!=', ''),
                /*AND*/
                array('sell_to_customer_no', '=', $customer->customer_no)
            ),
            /*OR*/
            array(
                array('invoice_no', '=', $searchTerm),
                /*AND*/
                array('sell_to_customer_no', '!=', ''),
                /*AND*/
                array('sell_to_customer_no', '=', $customer->customer_no)
            ),
            /*OR*/
            array(
                array('nav_order_no', '=', $searchTerm),
                /*AND*/
                array('sell_to_customer_no', '!=', ''),
                /*AND*/
                array('sell_to_customer_no', '=', $customer->customer_no)
            ),
            /*OR*/
            array(
                array('no', '=', $searchTerm),
                /*AND*/
                array('bill_to_customer_no', '!=', ''),
                /*AND*/
                array('bill_to_customer_no', '=', $customer->customer_no)
            ),
            /*OR*/
            array(
                array('order_no', '=', $searchTerm),
                /*AND*/
                array('bill_to_customer_no', '!=', ''),
                /*AND*/
                array('bill_to_customer_no', '=', $customer->customer_no)
            ),
            /*OR*/
            array(
                array('webshop_order_no', '=', $searchTerm),
                /*AND*/
                array('bill_to_customer_no', '!=', ''),
                /*AND*/
                array('bill_to_customer_no', '=', $customer->customer_no)
            ),
            /*OR*/
            array(
                array('your_reference', '!=', ''),
                /*AND*/
                array('your_reference', '=', $searchTerm),
                /*AND*/
                array('bill_to_customer_no', '!=', ''),
                /*AND*/
                array('bill_to_customer_no', '=', $customer->customer_no)
            ),
            /*OR*/
            array(
                array('invoice_no', '=', $searchTerm),
                /*AND*/
                array('bill_to_customer_no', '!=', ''),
                /*AND*/
                array('bill_to_customer_no', '=', $customer->customer_no)
            ),
            /*OR*/
            array(
                array('nav_order_no', '=', $searchTerm),
                /*AND*/
                array('bill_to_customer_no', '!=', ''),
                /*AND*/
                array('bill_to_customer_no', '=', $customer->customer_no)
            )
        );
        if ((int)$maxDaysBack > 0) {
            $curDate    = new \DateTime();
            $cutoffDate = $curDate->sub(new \DateInterval('P' . (int)$maxDaysBack . 'D'));
            foreach ($criteria as $disjunction) {
                $disjunction[] = array('posting_date', '>=', $cutoffDate->format('Y-m-d'));
            }
        }
        return $this->_findByCriteria($criteria);
    }

    /**
     * @param     $searchTerm
     * @param     $email
     * @param     $postCode
     * @param     $shopCode
     * @param     $languageCode
     * @param int $maxDaysBack
     *
     * @return RetShipmentCollection
     */
    public function searchB2CWithoutLogin( $searchTerm, $email, $postCode, $shopCode, $languageCode, $maxDaysBack = 0 ) {
        return $this->_searchB2CWithoutLogin($searchTerm, $email, $postCode, $shopCode, $languageCode, $maxDaysBack);
    }

    /**
     * @param     $searchTerm
     * @param     $email
     * @param     $postCode
     * @param     $shopCode
     * @param     $languageCode
     * @param int $maxDaysBack
     *
     * @return RetShipmentCollection
     */
    protected function _searchB2CWithoutLogin( $searchTerm, $email, $postCode, $shopCode, $languageCode, $maxDaysBack = 0 ) {

        $resultCollection = $this->collection->getEmptyCollection();

        if (empty($searchTerm) || empty($email) || empty($postCode) || empty($shopCode) || empty($languageCode)) {
            return $resultCollection;
        }

        $criteria = array(
            array(
                array('no', '=', $searchTerm),
                /*AND*/
                array('user_email', '=', $email),
                /*AND*/
                array('ship_to_post_code', '=', $postCode),
                /*AND*/
                array('shop_code', '=', $shopCode),
                /*AND*/
                array('language_code', '=', $languageCode),
                /*AND*/
                array('webshop_order_no', '>', 0)
            ),
            /*OR*/
            array(
                array('order_no', '=', $searchTerm),
                /*AND*/
                array('user_email', '=', $email),
                /*AND*/
                array('ship_to_post_code', '=', $postCode),
                /*AND*/
                array('shop_code', '=', $shopCode),
                /*AND*/
                array('language_code', '=', $languageCode),
                /*AND*/
                array('webshop_order_no', '>', 0)
            ),
            /*OR*/
            array(
                array('webshop_order_no', '=', $searchTerm),
                /*AND*/
                array('user_email', '=', $email),
                /*AND*/
                array('ship_to_post_code', '=', $postCode),
                /*AND*/
                array('shop_code', '=', $shopCode),
                /*AND*/
                array('language_code', '=', $languageCode),
                /*AND*/
                array('webshop_order_no', '>', 0)
            ),
            /*OR*/
            array(
                array('your_reference', '!=', ''),
                /*AND*/
                array('your_reference', '=', $searchTerm),
                /*AND*/
                array('user_email', '=', $email),
                /*AND*/
                array('ship_to_post_code', '=', $postCode),
                /*AND*/
                array('shop_code', '=', $shopCode),
                /*AND*/
                array('language_code', '=', $languageCode),
                /*AND*/
                array('webshop_order_no', '>', 0)
            ),
            /*OR*/
            array(
                array('invoice_no', '=', $searchTerm),
                /*AND*/
                array('user_email', '=', $email),
                /*AND*/
                array('ship_to_post_code', '=', $postCode),
                /*AND*/
                array('shop_code', '=', $shopCode),
                /*AND*/
                array('language_code', '=', $languageCode),
                /*AND*/
                array('webshop_order_no', '>', 0)
            ),
            /*OR*/
            array(
                array('nav_order_no', '=', $searchTerm),
                /*AND*/
                array('user_email', '=', $email),
                /*AND*/
                array('ship_to_post_code', '=', $postCode),
                /*AND*/
                array('shop_code', '=', $shopCode),
                /*AND*/
                array('language_code', '=', $languageCode),
                /*AND*/
                array('webshop_order_no', '>', 0)
            )
        );

        if ((int)$maxDaysBack > 0) {
            $curDate    = new \DateTime();
            $cutoffDate = $curDate->sub(new \DateInterval('P' . (int)$maxDaysBack . 'D'));
            foreach ($criteria as $disjunction) {
                $disjunction[] = array('posting_date', '>=', $cutoffDate->format('Y-m-d'));
            }
        }
        return $this->_findByCriteria($criteria);

    }

    /**
     * @param |Customer $customer
     * @param $searchTerm
     * @param int $maxDaysBack
     * @return RetShipmentCollection
     */
    public function searchB2CWithLogin( Customer $customer, $searchTerm, $maxDaysBack = 0 ) {
        return $this->_searchB2CWithLogin($customer,$searchTerm,$maxDaysBack);
    }

    /**
     * @param |Customer $customer
     * @param $searchTerm
     * @param int $maxDaysBack
     * @return RetShipmentCollection
     */
    protected function _searchB2CWithLogin( Customer $customer, $searchTerm, $maxDaysBack = 0 ) {
        $returnCollection = $this->collection->getEmptyCollection();

        if (empty($customer->customer_no) || empty($customer->shop_code) || empty($customer->language_code)) {
            return $returnCollection;
        }

        $customerNo   = $customer->customer_no;
        $shopCode     = $customer->shop_code;
        $languageCode = $customer->language_code;

        $criteria = array(
            array(
                array('no', '=', $searchTerm),
                /*AND*/
                array('shop_code', '=', $shopCode),
                /*AND*/
                array('language_code', '=', $languageCode),
                /*AND*/
                array('webshop_order_no', '>', 0),
                /*AND*/
                array('bill_to_customer_no', '=', $customerNo)
            ),
            /*OR*/
            array(
                array('order_no', '=', $searchTerm),
                /*AND*/
                array('shop_code', '=', $shopCode),
                /*AND*/
                array('language_code', '=', $languageCode),
                /*AND*/
                array('webshop_order_no', '>', 0),
                /*AND*/
                array('bill_to_customer_no', '=', $customerNo)
            ),
            /*OR*/
            array(
                array('webshop_order_no', '=', $searchTerm),
                /*AND*/
                array('shop_code', '=', $shopCode),
                /*AND*/
                array('language_code', '=', $languageCode),
                /*AND*/
                array('webshop_order_no', '>', 0),
                /*AND*/
                array('bill_to_customer_no', '=', $customerNo)
            ),
            /*OR*/
            array(
                array('your_reference', '!=', ''),
                /*AND*/
                array('your_reference', '=', $searchTerm),
                /*AND*/
                array('shop_code', '=', $shopCode),
                /*AND*/
                array('language_code', '=', $languageCode),
                /*AND*/
                array('webshop_order_no', '>', 0),
                /*AND*/
                array('bill_to_customer_no', '=', $customerNo)
            ),
            /*OR*/
            array(
                array('invoice_no', '=', $searchTerm),
                /*AND*/
                array('shop_code', '=', $shopCode),
                /*AND*/
                array('language_code', '=', $languageCode),
                /*AND*/
                array('webshop_order_no', '>', 0),
                /*AND*/
                array('bill_to_customer_no', '=', $customerNo)
            ),
            /*OR*/
            array(
                array('nav_order_no', '=', $searchTerm),
                /*AND*/
                array('shop_code', '=', $shopCode),
                /*AND*/
                array('language_code', '=', $languageCode),
                /*AND*/
                array('webshop_order_no', '>', 0),
                /*AND*/
                array('bill_to_customer_no', '=', $customerNo)
            ),
            /*OR*/
            array(
                array('no', '=', $searchTerm),
                /*AND*/
                array('shop_code', '=', $shopCode),
                /*AND*/
                array('language_code', '=', $languageCode),
                /*AND*/
                array('webshop_order_no', '>', 0),
                /*AND*/
                array('sell_to_customer_no', '=', $customerNo)
            ),
            /*OR*/
            array(
                array('order_no', '=', $searchTerm),
                /*AND*/
                array('shop_code', '=', $shopCode),
                /*AND*/
                array('language_code', '=', $languageCode),
                /*AND*/
                array('webshop_order_no', '>', 0),
                /*AND*/
                array('sell_to_customer_no', '=', $customerNo)
            ),
            /*OR*/
            array(
                array('webshop_order_no', '=', $searchTerm),
                /*AND*/
                array('shop_code', '=', $shopCode),
                /*AND*/
                array('language_code', '=', $languageCode),
                /*AND*/
                array('webshop_order_no', '>', 0),
                /*AND*/
                array('sell_to_customer_no', '=', $customerNo)
            ),
            /*OR*/
            array(
                array('your_reference', '!=', ''),
                /*AND*/
                array('your_reference', '=', $searchTerm),
                /*AND*/
                array('shop_code', '=', $shopCode),
                /*AND*/
                array('language_code', '=', $languageCode),
                /*AND*/
                array('webshop_order_no', '>', 0),
                /*AND*/
                array('sell_to_customer_no', '=', $customerNo)
            ),
            /*OR*/
            array(
                array('invoice_no', '=', $searchTerm),
                /*AND*/
                array('shop_code', '=', $shopCode),
                /*AND*/
                array('language_code', '=', $languageCode),
                /*AND*/
                array('webshop_order_no', '>', 0),
                /*AND*/
                array('sell_to_customer_no', '=', $customerNo)
            ),
            /*OR*/
            array(
                array('nav_order_no', '=', $searchTerm),
                /*AND*/
                array('shop_code', '=', $shopCode),
                /*AND*/
                array('language_code', '=', $languageCode),
                /*AND*/
                array('webshop_order_no', '>', 0),
                /*AND*/
                array('sell_to_customer_no', '=', $customerNo)
            )
        );

        if ((int)$maxDaysBack > 0) {
            $curDate    = new \DateTime();
            $cutoffDate = $curDate->sub(new \DateInterval('P' . (int)$maxDaysBack . 'D'));
            foreach ($criteria as $disjunction) {
                $disjunction[] = array('posting_date', '>=', $cutoffDate->format('Y-m-d'));
            }
        }
        return $this->_findByCriteria($criteria);

    }
	
	public function getObjectClass() {
		return RetShipmentDocument::class;
	}

}