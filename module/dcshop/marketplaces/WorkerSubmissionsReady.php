<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'WorkerAbstract.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'WorkerInterface.php';
ignore_user_abort(true);
set_time_limit(0);

class WorkerSubmissionsReady extends WorkerAbstract implements WorkerInterface
{

    /**
     * Sleep-Duration between executions in continuous mode
     * Provided in seconds
     * @var int
     */
    protected $sleepDuration = 30;

    protected function getSleepDuration()
    {
        return $this->sleepDuration;
    }

    /**
     * Funktion fuer das Hauptprogramm
     *
     * @access public
     * @return void
     */
    public function processJob() {

        $uniqid = uniqid();

        $query = "UPDATE shop_marketplace_submissions_ready SET unique_id = '" . $uniqid . "' where unique_id = '' order by id asc limit 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        $query = "SELECT * FROM shop_marketplace_submissions_ready where unique_id = '" . $uniqid . "' limit 1";
        $result = mysqli_query($GLOBALS['mysql_con'], $query);
        $row = mysqli_fetch_assoc($result);

        if(@mysqli_num_rows($result) == 0) {
            return;
        }

        $this->queueRow = $row;

        $this->processRow();

        if($this->processOk()) {
            $this->deleteJob();
        } else {
            $this->releaseJob();
        }

    }

    /**
     * Zeile verarbeiten
     *
     * @access protected
     * @return bool
     */
    protected function processRow() {
        $operation = $this->queueRow['operation'] . "_submission_ready";

        switch($this->queueRow['marketplace_type']) {

            case self::TYPE_AMAZON:
                require_once __DIR__ . DIRECTORY_SEPARATOR . 'amazon/AmazonMarketplace.php';
                $class = new AmazonMarketplace($this->queueRow['company'], $this->queueRow['shop_code'], $this->queueRow['language_code']);
                break;

            case self::TYPE_EBAY:
                require_once __DIR__ . DIRECTORY_SEPARATOR . 'ebay/EbayMarketplace.php';
                $class = new EbayMarketplace($this->queueRow['company'], $this->queueRow['shop_code'], $this->queueRow['language_code']);
                break;

        }

        $class->setResultFilename($this->queueRow['queue_id']);
        $class->$operation($this->queueRow['generated_id']);

        if($class->callOk() === false) {
            $this->insertError($this->queueRow['marketplace_type'], $operation, $this->queueRow['parameter'], $class->getErrorMessage());
            $this->setProcessOk(true);
            return true;
        }

        if($class->hasSubmissionResult()) {
            $this->insertSubmissionResult($class->getSubmissionResult());
            $this->setProcessOk(true);
            return true;
        }

        $this->setProcessOk(false);
        return false;
    }

    /**
     * Zeile entfernen
     *
     * @access protected
     * @return void
     */
    protected function deleteJob() {
        $query = "DELETE FROM shop_marketplace_submissions_ready where id = " . $this->queueRow['id'];
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }

    /**
     * Zeile Speichern, dass es ein Result von der Anfrage gibt
     *
     * @access protected
     * @return void
     */
    protected function insertSubmissionResult($result="") {
        $query = "INSERT INTO shop_marketplace_submissions_result (company, shop_code, language_code, marketplace_type, operation, parameter, result_file, unique_id, queue_id, timestamp, parent_queue_id, finish_parent_queue, payload) VALUES (
			'" . $this->queueRow['company'] . "',
			'" . $this->queueRow['shop_code'] . "',
			'" . $this->queueRow['language_code'] . "',
			" . $this->queueRow['marketplace_type'] . ",
			'" . $this->queueRow['operation'] . "',
			'" . $this->queueRow['parameter'] . "',
			'" . $result . "',
			'',
			" . $this->queueRow['queue_id'] . ",
			" . time() . ",
			" . $this->queueRow['parent_queue_id'] . ",
			" . $this->queueRow['finish_parent_queue'] . ",
			'" . $this->queueRow['payload'] . "'
		)";
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }

    protected function releaseJob()
    {
        if (null !== $this->queueRow && ($this->queueRow['id'] > 0)) {
            $query = "UPDATE shop_marketplace_submissions_ready SET unique_id = '' where id = " . $this->queueRow['id'];
            @mysqli_query($GLOBALS['mysql_con'], $query);
        }
    }
}

$worker = new WorkerSubmissionsReady();
$worker->setContinuous(true);
$worker->run();
