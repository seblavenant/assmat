UPDATE `ligne` 
    SET quantite = taux / 100
    WHERE taux IS NOT NULL;

ALTER TABLE `ligne`
    DROP ` taux `;
