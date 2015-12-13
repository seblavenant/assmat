ALTER TABLE `contact` CHANGE `password` `password` VARCHAR( 256 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;
ALTER TABLE `contact` CHANGE `nom` `nom` VARCHAR( 128 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;
ALTER TABLE `contact` CHANGE `prenom` `prenom` VARCHAR( 128 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;
ALTER TABLE `contact` CHANGE `adresse` `adresse` VARCHAR( 256 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;
ALTER TABLE `contact` CHANGE `code_postal` `code_postal` VARCHAR( 6 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;
ALTER TABLE `contact` CHANGE `ville` `ville` VARCHAR( 128 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;

ALTER TABLE `contact` CHANGE `key` `auth_code` VARCHAR( 256 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

ALTER TABLE `assmat`.`contact` DROP INDEX `username`, ADD UNIQUE `username` ( `email` );
ALTER TABLE `assmat`.`contact` ADD UNIQUE `auth_code` ( `auth_code` );