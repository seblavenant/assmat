CREATE TABLE `bulletin` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `date_debut` date NOT NULL,
 `date_fin` date NOT NULL,
 `contrat_id` int(11) NOT NULL,
 PRIMARY KEY (`id`),
 KEY `contrat_id` (`contrat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;