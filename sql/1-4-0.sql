ALTER TABLE `employeur` MODIFY `paje_emploi_id` VARCHAR( 128 ) DEFAULT NULL;
ALTER TABLE `employe` MODIFY `ss_id` VARCHAR( 128 ) DEFAULT NULL;

ALTER TABLE `employe` DROP INDEX `contact_id` ,ADD UNIQUE `contact_id` ( `contact_id` );
ALTER TABLE `employeur` DROP INDEX `contact_id` ,ADD UNIQUE `contact_id` ( `contact_id` );