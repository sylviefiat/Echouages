ALTER TABLE `#__cot_admin` ADD `id_location` VARCHAR(50) NOT NULL AFTER `id`;
ALTER TABLE `#__cot_admin` ADD `form_references` VARCHAR(50) NOT NULL AFTER `id`;
ALTER TABLE `#__cot_admin` ADD `informant_name` VARCHAR(100) NOT NULL AFTER `observer_email`;
ALTER TABLE `#__cot_admin` ADD `informant_tel` VARCHAR(100) NOT NULL AFTER `informant_name`;
ALTER TABLE `#__cot_admin` ADD `informant_email` VARCHAR(100) NOT NULL AFTER `informant_tel`;
