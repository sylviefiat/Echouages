ALTER TABLE `#__jforms_submissions` ADD `status` VARCHAR(255) DEFAULT 'na';
ALTER TABLE `#__jforms_submissions` ADD `payment_status` VARCHAR(255) DEFAULT 'na';
ALTER TABLE `#__jforms_submissions` ADD `payment_details` LONGTEXT;
