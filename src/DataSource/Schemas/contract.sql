CREATE TABLE `contrat` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `base_heure` float NOT NULL,
 `type` int(11) NOT NULL,
 `employe_id` int(11) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1