CREATE TABLE `employe` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `contact_id` int(11) NOT NULL,
 `ss_id` varchar(128) NOT NULL,
 PRIMARY KEY (`id`),
 KEY `contact_id` (`contact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;