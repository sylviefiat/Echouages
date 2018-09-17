DROP TABLE IF EXISTS `#__cot_form`;

CREATE TABLE `#__cot_form` (
	`id`			INT(11)		NOT NULL AUTO_INCREMENT,
	`id_location`		INT(11)		NOT NULL AUTO_INCREMENT,
	`asset_id`		INT(10)     	NOT NULL DEFAULT '0',
	`informant_name` 	VARCHAR(255)	NOT NULL,
	`informant_tel`		VARCHAR(255)	NOT NULL,
	`informant_mail`	VARCHAR(255)	NOT NULL,
	`observer_name`		VARCHAR(255)	NOT NULL,
	`observer_tel`		VARCHAR(255)	NOT NULL,
	`observer_mail`		VARCHAR(255)	NOT NULL,
	`observation_date`	DATETIME 	NOT NULL DEFAULT '0000-00-00 00:00:00',
	`mammal_number`		INT(10)		NOT NULL,
	`mammal_size`		INT(10)		NOT NULL,
	`precise_size`		INT(1)		NULL,
	`approximate_size`	INT(1)		NULL,
	`levies`		VARCHAR(1024)	NOT NULL,
	PRIMARY KEY (`id`)
)
	ENGINE =MyISAM
	AUTO_INCREMENT =0
	DEFAULT CHARSET =utf8;
