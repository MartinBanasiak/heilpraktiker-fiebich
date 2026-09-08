CREATE TABLE `dc_cookie` (
    `id` VARCHAR(32) NOT NULL,
    `sid` VARCHAR(128) NOT NULL,
    `datetime` DATETIME NOT NULL,
    `action` VARCHAR(50) NOT NULL,
    `state` TEXT NOT NULL,
    `device` VARCHAR(20) NOT NULL,
    `site_code` VARCHAR(20) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `id_UNIQUE` (`id` ASC),
    INDEX `datetime` (`datetime` ASC),
    INDEX `action` (`action` ASC),
    INDEX `sid` (`sid` ASC),
    INDEX `site_code`(`site_code` ASC));
DROP TRIGGER IF EXISTS `dc_cookie_BEFORE_INSERT`;

DELIMITER $$
CREATE DEFINER = CURRENT_USER TRIGGER `dc_cookie_BEFORE_INSERT` BEFORE INSERT ON `dc_cookie` FOR EACH ROW
BEGIN
    SET NEW.datetime = NOW();
END$$
DELIMITER ;
