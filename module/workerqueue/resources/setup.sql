#CREATE DATABASE  IF NOT EXISTS `workerqueue`;
#USE `workerqueue`;

DROP TABLE IF EXISTS `general_job_queue`;
CREATE TABLE `general_job_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `queue_name` varchar(128) NOT NULL,
  `creation_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `time_to_live` int(11) NOT NULL DEFAULT '0',
  `last_action_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` varchar(20) NOT NULL,
  `failure_code` int(3) NOT NULL DEFAULT '0',
  `no_of_unsuccessful_attempts` int(3) NOT NULL DEFAULT '0',
  `max_no_of_retries` int(3) NOT NULL DEFAULT '0',
  `payload` mediumtext NOT NULL,
  `failure_data` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `scheduled_jobs`;
CREATE TABLE `scheduled_jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cron_spec` varchar(128) NOT NULL,
      `registered_with_pid` varchar(10) NOT NULL DEFAULT '',
  `queue_name` varchar(128) NOT NULL,
  `time_to_live` int(11) NOT NULL DEFAULT '0',
  `max_no_of_retries` int(3) NOT NULL DEFAULT '0',
  `payload` mediumtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`registered_with_pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
