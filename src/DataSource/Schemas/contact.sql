CREATE TABLE `contact` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `nom` varchar(128) NOT NULL,
 `prenom` varchar(128) NOT NULL,
 `adresse` varchar(256) NOT NULL,
 `code_postal` varchar(6) NOT NULL,
 `ville` varchar(128) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;