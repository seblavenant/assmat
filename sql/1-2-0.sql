ALTER TABLE `evenement` DROP INDEX `UNIQUE` ,
ADD UNIQUE `date_contrat` ( `date` , `contrat_id` );

ALTER TABLE `assmat`.`evenement` DROP INDEX `date`;