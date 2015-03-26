CREATE TABLE `indemnites` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `type` int(11) NOT NULL,
 `montant` int(11) NOT NULL,
 `contrat_id` int(11) NOT NULL,
 PRIMARY KEY (`id`),
 KEY `type` (`type`,`contrat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;