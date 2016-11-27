SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));

CREATE TABLE `cp_reference` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `annee` int(11) NOT NULL,
 `taux_journalier` float NOT NULL,
 `nb_jours` float NOT NULL,
 `contrat_id` int(11) NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `annee` (`annee`,`contrat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

ALTER TABLE `cp_reference` ADD UNIQUE( `annee`, `contrat_id`);