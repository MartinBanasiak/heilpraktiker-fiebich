 <?php
/**
 * Klasse mit allen Funktionen fuer alle Marketplace Anbieter.
 * Fuer das Zusammenspiel mit den Workern
 *
 */
abstract class MarketplaceAbstract {

    public $errorMessage = "";

    public $submissionId = null;

    public $submissionResult = false;

    public $submissionReady = false;

    public $callOk = false;

    public $resultFile = "";

    public $queueId = 0;

    protected $marketplace_type;

    protected $parentQueueId = 0;

    protected $lastQueueId = false;

    protected $currentQueueId = 0;

    protected $finishSubmissionNow = false;

    protected $itemInBulk = array();

    protected $payload = null;

    /**
     * Gibt an ob die Worker Zeile nochmal bearbeitet werden soll.
     *
     * @access protected
     * @var bool
     */
    protected $runAgain = false;

    /**
     * @return bool
     */
    public function isFinishSubmissionNow()
    {
        return $this->finishSubmissionNow;
    }

    /**
     * @param bool $finishSubmissionNow
     */
    public function setFinishSubmissionNow($finishSubmissionNow)
    {
        $this->finishSubmissionNow = $finishSubmissionNow;
    }

    /**
     * @return int
     */
    public function getParentQueueId()
    {
        return $this->parentQueueId;
    }

    /**
     * @param int $parentQueueId
     */
    public function setParentQueueId($parentQueueId)
    {
        $this->parentQueueId = $parentQueueId;
    }

    /**
     * @return bool
     */
    public function isLastQueueId()
    {
        return $this->lastQueueId;
    }

    /**
     * @param bool $lastQueueId
     */
    public function setLastQueueId($lastQueueId)
    {
        $this->lastQueueId = $lastQueueId;
    }

    /**
     * @return int
     */
    public function getCurrentQueueId()
    {
        return $this->currentQueueId;
    }

    /**
     * @param int $currentQueueId
     */
    public function setCurrentQueueId($currentQueueId)
    {
        $this->currentQueueId = $currentQueueId;
    }

    /**
     * Shop config und globals vorbereiten,
     * Grossteil uebernommen aus der shop_start.inc.php
     *
     * @param string $company
     * @param string $shop_code
     * @param string $language_code
     */
    public function __construct($company, $shop_code, $language_code, $payload = null) {

        $this->payload = $payload;
        // Textbausteine auslesen
        $text_constant = array();
        $text_constants_file = dirname(__FILE__) . "/../../../dc/common/text_constants.inc.php";
        require_once($text_constants_file);
        $GLOBALS["tc"] = $text_constant["de"];

        $GLOBALS["language"] = $language_code;

        // Shop und Shopsprache auslesen
        $shop            = get_shop($company, $shop_code);
        $GLOBALS['shop'] = $shop;

        // GLOBALS für Artikel- und Kategorie- und Kundenherkunftsshop auslesen
        $GLOBALS['shop']['item_source']     = ($GLOBALS['shop']['use_items_from_shop_code'] != '') ? $GLOBALS['shop']['use_items_from_shop_code'] : $GLOBALS['shop']['item_source'] = $GLOBALS['shop']['code'];
        $GLOBALS['shop']['category_source'] = ($GLOBALS['shop']['use_categorys_from_shop_code'] != '') ? $GLOBALS['shop']['use_categorys_from_shop_code'] : $GLOBALS['shop']['category_source'] = $GLOBALS['shop']['code'];
        $GLOBALS['shop']['customer_source'] = ($GLOBALS['shop']['use_customer_from_shop_code'] != '') ? $GLOBALS['shop']['use_customer_from_shop_code'] : $GLOBALS['shop']['customer_source'] = $GLOBALS['shop']['code'];

        //Shop-Setup des Artikelherkunftsshops holen für MwSt-Berechnung
        $item_source_shop            = get_shop($company, $GLOBALS['shop']['item_source']);
        $GLOBALS['item_source_shop'] = $item_source_shop;

        // Shopsprache auslesen
        $shop_language            = get_shop_language($company, $shop_code, $language_code);
        $GLOBALS['shop_language'] = $shop_language;

        // Shop-Einrichtung auslesen
        $GLOBALS["shop_setup"] = get_shop_setup($company);

        // shop.config einbinden (ergänzt shop_setup -> nie vor get_shop_setup)
        include(dirname(__FILE__) . "/../shop.config.php");

        //shop-spezifische cnfig einbinden (für file_exists immer Pfad von document_root aus - relative Pfade führen immer zu false!!)
        if (file_exists(dirname(__FILE__) . "/../" . $GLOBALS['shop']['code'] . ".config.php")) {
            require_once(dirname(__FILE__) . "/../" . $GLOBALS['shop']['code'] . ".config.php");
        }

        //Shop-Währung auslesen
        $GLOBALS['shop_currency']['code'] = $GLOBALS["shop_setup"]['default_currency_code'];
    }

    public function new_queue($marketplace_type, $operation, $parameter, $parentQueueId = 0, $finishParentQueue = false) {
        if (!$finishParentQueue) {
            $finishParentQueue = 0;
        }
        $query = "INSERT INTO shop_marketplace_queue 
            (
                company, 
                shop_code, 
                language_code, 
                marketplace_type, 
                operation, 
                parameter, 
                timestamp, 
                parent_queue_id, 
                finish_parent_queue
            ) values (
                '" . $GLOBALS["shop"]["company"] . "',
                '" . $GLOBALS["shop"]["code"] . "',
                '" . $GLOBALS["shop_language"]["code"] . "',
                " . $marketplace_type . ",
                '" . $operation . "',
                '" . serialize($parameter) . "',
                " . time() . ",
                " . $parentQueueId . ",
                " . $finishParentQueue . "
		)";

        @mysqli_query($GLOBALS['mysql_con'], $query);

        $mysqlInsertId = mysqli_insert_id($GLOBALS['mysql_con']);

        return $mysqlInsertId;
    }

    public function set_item_submission_error($sku, $errortext) {
        $query = "UPDATE shop_marketplace_item_update SET marketplace_update = 0, marketplace_error = '" . $errortext . "', active_on_marketplace = 0, update_insert = 1 WHERE 
			company = '" . $GLOBALS['shop']['company'] . "'
			and shop_code='" . $GLOBALS['shop']['code'] . "'
			and language_code='" . $GLOBALS['shop_language']['code'] . "'
			and item_shop_code='" . $GLOBALS['shop']['item_source'] . "'
			and item_language_code='" . $GLOBALS['shop_language']['code'] . "'
			and item_no = '" . $sku . "'
		";
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }

    public function set_item_to_update($sku) {
        $query = "UPDATE shop_item SET marketplace_update = 1 WHERE 
			company = '" . $GLOBALS['shop']['company'] . "'
			and shop_code='" . $GLOBALS['shop']['item_source'] . "'
			and language_code='" . $GLOBALS['shop_language']['code'] . "'
			and item_no = '" . $sku . "'
		";
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }

    public function insert_item_error($sku, $marketplace_type, $operation, $errorcode, $errortext) {
        $query = "INSERT INTO shop_marketplace_item_errors (marketplace_type, company, shop_code, language_code, item_no, operation, errorcode, errortext) VALUES (
			" . $marketplace_type . ",
			'" . $GLOBALS["shop"]["company"] . "',
			'" . $GLOBALS["shop"]["item_source"] . "',
			'" . $GLOBALS["shop_language"]["code"] . "',
			'" . $sku . "',
			'" . $operation . "',
			'" . $errorcode . "',
			'" . $errortext . "'
		)";
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }

    public function set_item_submission_success($item,$field) {
        $queryAdd = '';
        if ($field == 'marketplace_update') {
            $queryAdd = ', active_on_marketplace = 1';
        }
        $query = "
			UPDATE shop_marketplace_item_update SET 
				".$field." = 0, 
				update_insert = 1,
				marketplace_error = '',
				marketplace_last_update = NOW()".$queryAdd."
			WHERE 
				company = '" . $GLOBALS['shop']['company'] . "'
				and shop_code='" . $GLOBALS['shop']['code'] . "'
				and language_code='" . $GLOBALS['shop_language']['code'] . "'
				and item_shop_code='" . $GLOBALS['shop']['item_source'] . "'
				and item_language_code='" . $GLOBALS['shop_language']['code'] . "'
				and item_no = '" . $item['item_no'] . "'
		";
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }

    /**
     * Setzt Error Message wenn eine Anfrage Fehlgeschlagen ist
     *
     * @var string $message
     * @access protected
     */
    protected function setErrorMessage($message = "") {
        $this->errorMessage = $message;
    }

    /**
     * Gibt Error Message zurueck
     *
     * @access public
     * @return string
     */
    public function getErrorMessage() {
        return $this->errorMessage;
    }

    /**
     * Setzt callOk ob eine Anfrage erfolgreich war oder nicht
     *
     * @var bool $callOk
     * @access protected
     */
    protected function setCallOk($callOk) {
        $this->callOk = $callOk;
    }

    /**
     * Gibt zurueck ob eine Anfrage erfolgreich war
     *
     * @access public
     * @return bool
     */
    public function callOk() {
        return $this->callOk;
    }

    /**
     * Setzt eine Submission ID, die vom Anbieter erstellt wurde
     *
     * @var mixed $submissionId
     * @access protected
     */
    protected function setSubmissionId($submissionId) {
        $this->submissionId = $submissionId;
    }

    /**
     * Gibt die Submission ID vom Anbieter zurueck
     *
     * @access public
     * @return mixed
     */
    public function getSubmissionId() {
        return $this->submissionId;
    }

    public function getPayload() {
        return $this->payload;
    }

    public function getPayloadAsJson() {
        return json_encode($this->getPayload());
    }

    /**
     * @param null $payload
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;
    }

    /**
     * @return array
     */
    public function getItemInBulk(): array
    {
        return $this->itemInBulk;
    }


    public function setItemInBulk($itemInBulk)
    {
        $this->itemInBulk[] = $itemInBulk;
    }



    /**
     * Gibt an ob es eine Submission ID vom Anbieter gibt
     *
     * @access public
     * @return bool
     */
    public function hasSubmissionId() {
        if($this->submissionId !== null) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Setzt Status fuer eine Submission beim Anbieter.
     * Wenn die Submission beim Anbieter beendet ist, wird true gesetzt.
     *
     * @access protected
     */
    protected function setSubmissionReady($submissionReady) {
        $this->submissionReady = $submissionReady;
    }

    /**
     * Gibt zurueck ob die Submission beim Anbieter durchgelaufen ist.
     *
     * @access public
     * @return bool
     */
    public function isSubmissionReady() {
        return $this->submissionReady;
    }

    /**
     * Setzt das Ergebnis der Submission.
     * Kann auch filename sein, denn das Ergebnis der Submission ist manchmal zu gross fuer die Datenbank
     *
     * @var string $submissionResult
     * @access protected
     */
    protected function setSubmissionResult($submissionResult) {
        $this->submissionResult = $submissionResult;
    }

    /**
     * Gibt das Ergebnis der Submission zurueck
     *
     * @access public
     * @return string
     */
    public function getSubmissionResult() {
        return $this->submissionResult;
    }

    /**
     * Prueft ob ein Ergebnis einer Submission existiert
     *
     * @access public
     * @return bool
     */
    public function hasSubmissionResult() {
        if($this->getSubmissionResult() === false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Setzt den Namen der Datei, in der das Ergebnis der Anfrage gespeichert wird
     *
     * @param string $queue_id
     * @access public
     */
    public function setResultFilename($queue_id, $ending = "xml") {
        $this->resultFile = "result_" . $queue_id . "." . $ending;
        $this->setQueueId($queue_id);
    }

    /**
     * Gibt den Namen der Result Datei zurueck
     *
     * @access public
     * @return string
     */
    public function getResultFilename() {
        return $this->resultFile;
    }

    /**
     * Setzt runAgain
     *
     * @access protected
     */
    public function setRunAgain($runAgain) {
        $this->runAgain = $runAgain;
    }

    public function setQueueId($queueId) {
        $this->queueId = $queueId;
    }

    public function getQueueId() {
        return $this->queueId;
    }

    /**
     * Gibt zurueck, ob Zeile nochmal verarbeitet werden soll
     *
     * @access protected
     * @return bool
     */
    public function runAgain() {
        return $this->runAgain;
    }

    /**
     * Rekursive Funktion
     *
     */
    protected function helper_update_variant_available($result, $variant_available, $marketplace_type) {
        while ($row = mysqli_fetch_assoc($result)) {
            $query = "UPDATE shop_marketplace_categories SET variant_available = " . $variant_available . " where marketplace_type = " . $marketplace_type . " and category_code = '" . $row['category_code'] . "'";
            mysqli_query($GLOBALS['mysql_con'], $query);

            // child categories haben die selbe einstellung:
            $query = "SELECT category_code FROM shop_marketplace_categories where marketplace_type = '" . $marketplace_type . "' and parent_category_code = '" . $row['category_code'] . "' and category_code != '" . $row['category_code'] . "'";
            $tempresult = mysqli_query($GLOBALS['mysql_con'], $query);
            $child_categories = array();

            if(mysqli_num_rows($tempresult) > 0) {
                $this->helper_update_variant_available($tempresult, $variant_available, $marketplace_type);
            }
        }
    }

    public function set_item_exists($sku, $marketplace_type) {
        $query = "UPDATE shop_marketplace_item_update SET marketplace_existing_item = 1, marketplace_update = 1, update_insert = 1 
          WHERE company = '" . $GLOBALS["shop"]["company"] . "'
         AND shop_code = '" . $GLOBALS["shop"]["code"] . "'
         AND language_code = '" . $GLOBALS["shop_language"]["code"] . "'
         AND item_shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
         AND item_language_code = '" . $GLOBALS["shop_language"]["code"] . "'
         AND item_no = '" . $sku . "'";

        @mysqli_query($GLOBALS['mysql_con'], $query);

    }

    public function set_item_not_exists($sku, $marketplace_type) {
        $query = "UPDATE shop_marketplace_item_update SET marketplace_existing_item = 0, update_insert = 1 
          WHERE company = '" . $GLOBALS["shop"]["company"] . "'
         AND shop_code = '" . $GLOBALS["shop"]["code"] . "'
         AND language_code = '" . $GLOBALS["shop_language"]["code"] . "'
         AND item_shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
         AND item_language_code = '" . $GLOBALS["shop_language"]["code"] . "'
         AND item_no = '" . $sku . "'";

        @mysqli_query($GLOBALS['mysql_con'], $query);

    }

    public function set_item_start_relationship($sku, $value) {
        $query = "UPDATE shop_marketplace_item_update SET marketplace_start_relationship = " . (int)$value . "
          WHERE company = '" . $GLOBALS["shop"]["company"] . "'
         AND shop_code = '" . $GLOBALS["shop"]["code"] . "'
         AND language_code = '" . $GLOBALS["shop_language"]["code"] . "'
         AND item_shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
         AND item_language_code = '" . $GLOBALS["shop_language"]["code"] . "'
         AND item_no = '" . $sku . "'";

        @mysqli_query($GLOBALS['mysql_con'], $query);
    }

    public function extend_parameter_queue($new_parameter) {
        $query = "SELECT parameter FROM shop_marketplace_queue WHERE id = " . $this->currentQueueId;
        $tempresult = mysqli_query($GLOBALS['mysql_con'], $query);
        $current_parameter = mysqli_fetch_assoc($tempresult);
        $current_parameter = unserialize($current_parameter['parameter']);

        $parameter = ($new_parameter + $current_parameter);
        $parameter = serialize($parameter);

        $query = "UPDATE shop_marketplace_queue SET parameter = '$parameter' WHERE id = " . $this->currentQueueId;
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }
}