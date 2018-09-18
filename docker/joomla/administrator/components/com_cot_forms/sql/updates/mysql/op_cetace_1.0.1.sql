ALTER TABLE `#__cot_admin_forms` ADD `admin_validation` BOOLEAN NOT NULL default 0;
ALTER TABLE `#__cot_admin_forms` ADD `created_by` INT(11)  NOT NULL;

CREATE TRIGGER `#__trig_cot_admin_insert` BEFORE INSERT ON `#__cot_admin_forms`
FOR EACH ROW SET NEW.localisation = GeomFromText( CONCAT('POINT(', NEW.observation_longitude, ' ', NEW.observation_latitude, ')' ));

CREATE TRIGGER `#__trig_cot_admin_update` BEFORE UPDATE ON `#__cot_admin_forms`
FOR EACH ROW SET NEW.localisation = GeomFromText( CONCAT('POINT(', NEW.observation_longitude, ' ', NEW.observation_latitude, ')' ));

ALTER TABLE `#__cot_admin_forms` ADD `observation_localisation` VARCHAR(100) NOT NULL AFTER `observation_location`;
