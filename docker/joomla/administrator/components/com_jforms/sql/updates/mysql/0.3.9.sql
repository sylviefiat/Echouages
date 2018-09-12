ALTER TABLE `#__jforms_submissions` DROP INDEX password;
ALTER TABLE `#__jforms_submissions` CHANGE password passphrase VARCHAR(255);
ALTER TABLE `#__jforms_submissions` ADD UNIQUE(passphrase);