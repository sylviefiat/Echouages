ALTER TABLE `#__jforms_forms` CHANGE `access` `access` VARCHAR(255) DEFAULT 1;
ALTER TABLE `#__jforms_forms`  ADD `acl` LONGTEXT AFTER `access`;