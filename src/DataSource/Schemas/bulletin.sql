 	CREATE TABLE `bulletin` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `mois` tinyint(4) NOT NULL,
 `annee` int(11) NOT NULL,
 `contrat_id` int(11) NOT NULL,
 PRIMARY KEY (`id`),
 KEY `contrat_id` (`contrat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;