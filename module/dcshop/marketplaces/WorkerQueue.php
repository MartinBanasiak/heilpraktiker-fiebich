<?php
error_reporting (E_ALL^E_NOTICE^E_STRICT);
ini_set('display_errors',1);
$rootDir = dirname(dirname(dirname(__DIR__)));
ignore_user_abort(true);
set_time_limit(0);

include_once "WorkerAbstract.php";
include_once "WorkerInterface.php";

class WorkerQueue extends WorkerAbstract implements WorkerInterface
{
    /**
     * Sleep-Duration between executions in continuous mode
     * Provided in seconds
     * @var int
     */
    protected $sleepDuration = 1;

    protected function getSleepDuration()
    {
        return $this->sleepDuration;
    }


    public function processJob() {


        $uniqid = uniqid();
        $this->uid = $uniqid;

        $query = "UPDATE shop_marketplace_queue SET unique_id = '" . $uniqid . "' where unique_id = '' order by id asc limit 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        $query = "SELECT * FROM shop_marketplace_queue where unique_id = '" . $uniqid . "' limit 1";
        //$query = "SELECT * FROM shop_marketplace_queue where id = '1500' limit 1";
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

    public function releaseJob()
    {
        if (null !== $this->queueRow && ($this->queueRow['id'] > 0)) {
            $query = "UPDATE shop_marketplace_queue SET unique_id = '' where id = " . $this->queueRow['id'];
            @mysqli_query($GLOBALS['mysql_con'], $query);
        }
    }

    /**
     * Zeile verarbeiten
     *
     * @access protected
     * @return bool
     */
    protected function processRow() {
        
        $operation = $this->queueRow['operation'];

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
        // Operation ausfuehren
        $class->setCurrentQueueId($this->queueRow['id']);
        $class->setResultFilename($this->queueRow['id']);
        $class->$operation(unserialize($this->queueRow['parameter'],['allowed_classes' => false]));



        if($class->callOk() === false) {
            $this->insertError($this->queueRow['marketplace_type'], $operation, $this->queueRow['parameter'], $class->getErrorMessage());
            $this->setProcessOk(true);
            return true;
        }



        // Direkt ein result erhalten dann in die result tabelle schreiben
        if($class->hasSubmissionResult()) {
            $this->insertSubmissionResult( $class->getSubmissionResult(), $class->isFinishSubmissionNow() );
            $this->setProcessOk(true);
            return true;
        }



        // erst mal nur eine submission abgesetzt
        if($class->hasSubmissionId()) {
            $this->submissionId = $class->getSubmissionId();
            $this->payload = $class->getPayloadAsJson();
            $this->insertSubmission();
            $this->setProcessOk(true);
            return true;
        }



        // zum spaeteren zeitpunkt nochmal verarbeiten
        if($class->runAgain()) {
            $this->setProcessOk(false);
            return false;
        }

        // keine Abfrage noetig, Zeile loeschen, keine weitere Verarbeitung
        if($class->callOk() === true) {
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
        $query = "DELETE FROM shop_marketplace_queue where id = " . $this->queueRow['id'];
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }

    /**
     * Die Anfrage ist beim Anbieter in der queue. Diese zum pruefen in die Tabelle schreiben
     *
     * @access protected
     * @return void
     */
    protected function insertSubmission() {

        $query = "SELECT * FROM shop_marketplace_queue where id = '" . $this->queueRow['id'] . "' limit 1";
        $result = mysqli_query($GLOBALS['mysql_con'], $query);
        $row = mysqli_fetch_assoc($result);

        $this->queueRow = $row;

        $parentQueueId = $this->queueRow['parent_queue_id'];
        if ($parentQueueId == 0) {
            $parentQueueId = $this->queueRow['id'];
        }

        $query = "INSERT INTO shop_marketplace_submissions (company, shop_code, language_code, marketplace_type, submission_id, operation, parameter, unique_id, queue_id, timestamp, parent_queue_id, finish_parent_queue, payload) VALUES (
			'" . $this->queueRow['company'] . "',
			'" . $this->queueRow['shop_code'] . "',
			'" . $this->queueRow['language_code'] . "',
			" . $this->queueRow['marketplace_type'] . ",
			'" . $this->submissionId . "',
			'" . $this->queueRow['operation'] . "',
			'" . $this->queueRow['parameter'] . "',
			'',
			" . $this->queueRow['id'] . ",
			" . time() . ",
			" . $this->queueRow['parent_queue_id'] . ",
			" . $parentQueueId . ",
			'" . $this->payload . "'
		)";
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }

    /**
     * Die Anfrage hat direkt ein Ergebnis gebracht, in die Tabelle schreiben
     *
     * @access protected
     * @return void
     */
    protected function insertSubmissionResult($resultFile, $finishNow = false) {

        $query = "SELECT * FROM shop_marketplace_queue where id = '" . $this->queueRow['id'] . "' limit 1";
        $result = mysqli_query($GLOBALS['mysql_con'], $query);
        $row = mysqli_fetch_assoc($result);

        $this->queueRow = $row;

        $parentQueueId = $this->queueRow['parent_queue_id'];
        if ($parentQueueId == 0) {
            $parentQueueId = $this->queueRow['id'];
        }

        $query = "INSERT INTO shop_marketplace_submissions_result (company, shop_code, language_code, marketplace_type, operation, parameter, result_file, unique_id, queue_id, timestamp, parent_queue_id, finish_parent_queue) VALUES (
			'" . $this->queueRow['company'] . "',
			'" . $this->queueRow['shop_code'] . "',
			'" . $this->queueRow['language_code'] . "',
			" . $this->queueRow['marketplace_type'] . ",
			'" . $this->queueRow['operation'] . "',
			'" . $this->queueRow['parameter'] . "',
			'" . $resultFile . "',
			'',
			" . $this->queueRow['id'] . ",
			" . time() . ",
			" . $parentQueueId . ",
			" . $this->queueRow['finish_parent_queue'] . "
		)";

        if ($finishNow) {
            $query = "INSERT INTO shop_marketplace_submissions_result (company, shop_code, language_code, marketplace_type, operation, parameter, result_file, unique_id, queue_id, timestamp, processed, parent_queue_id, finish_parent_queue) VALUES (
                '" . $this->queueRow['company'] . "',
                '" . $this->queueRow['shop_code'] . "',
                '" . $this->queueRow['language_code'] . "',
                " . $this->queueRow['marketplace_type'] . ",
                '" . $this->queueRow['operation'] . "',
                '" . $this->queueRow['parameter'] . "',
                '" . $resultFile . "',
                '".uniqid()."',
                " . $this->queueRow['id'] . ",
                " . time() . ",
                1,
                " . $parentQueueId . ",
                " . $this->queueRow['finish_parent_queue'] . "
            )";
        }

        @mysqli_query($GLOBALS['mysql_con'], $query);
    }

}

// Hauptprogramm starten
$worker = new WorkerQueue();
$worker->setContinuous(true);
$worker->run();