<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'WorkerAbstract.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'WorkerInterface.php';
ignore_user_abort(true);
set_time_limit(0);

class WorkerSubmissions extends WorkerAbstract implements WorkerInterface
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


    public function processJob() {

        $uniqid = uniqid("", true);

        $query = "UPDATE shop_marketplace_submissions SET unique_id = '" . $uniqid . "' where unique_id = '' order by id asc limit 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        $query = "SELECT * FROM shop_marketplace_submissions where unique_id = '" . $uniqid . "' limit 1";
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
        $operation = $this->queueRow['operation'] . "_submission";

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
        $class->$operation($this->queueRow['submission_id']);

        if($class->callOk() === false) {
            $this->insertError($this->queueRow['marketplace_type'], $operation, $this->queueRow['parameter'], $class->getErrorMessage());
            $this->setProcessOk(true);
            return true;
        }

        if($class->isSubmissionReady()) {
            $this->insertSubmissionReady($class->getSubmissionId());
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
        $query = "DELETE FROM shop_marketplace_submissions where id = " . $this->queueRow['id'];
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }

    /**
     * Speichert den Datensatz in die ready tabelle, Wenn Anfrage beim Anbieter verarbeitet wurde
     *
     * @access protected
     */
    protected function insertSubmissionReady($submissionId) {
        $query = "INSERT INTO shop_marketplace_submissions_ready (company, shop_code, language_code, marketplace_type, operation, parameter, generated_id, unique_id, queue_id, timestamp, parent_queue_id, finish_parent_queue, payload) VALUES (
			'" . $this->queueRow['company'] . "',
			'" . $this->queueRow['shop_code'] . "',
			'" . $this->queueRow['language_code'] . "',
			" . $this->queueRow['marketplace_type'] . ",
			'" . $this->queueRow['operation'] . "',
			'" . $this->queueRow['parameter'] . "',
			'" . $submissionId . "',
			'',
			" . $this->queueRow['queue_id'] . ",
			" . time() . ",
			" . $this->queueRow['parent_queue_id'] . ",
			" . $this->queueRow['finish_parent_queue'] . ",
			'" . $this->queueRow['payload'] . "'
		)";
        echo $query;
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }

    protected function releaseJob()
    {
        if (null !== $this->queueRow && ($this->queueRow['id'] > 0)) {
            $query = "UPDATE shop_marketplace_submissions SET unique_id = '' where id = " . $this->queueRow['id'];
            @mysqli_query($GLOBALS['mysql_con'], $query);
        }
    }

}

$worker = new WorkerSubmissions();
$worker->setContinuous(true);
$worker->run();