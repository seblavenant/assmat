<?php

namespace Assmat\DataSource\Repositories\Memory\Lignes;

use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Constants;
use Assmat\DataSource\Domains;

class Salaire
{
    public function getDomain()
    {
        $ligneDTO = new DTO\Ligne();
        $ligneDTO->code = Constants\Lignes\Code::SALAIRE;
        $ligneDTO->type = Constants\Lignes\Type::GAIN;
        $ligneDTO->hydrateFromBulletinClosure = function(Domains\Bulletin $bulletin) use($ligneDTO) {

            foreach($bulletin->getEvenements() as $evenement)
            {
                $contrat = $bulletin->getContrat();

                switch($evenement->getTypeId())
                {
                    case Constants\Evenements\Type::ACCUEIL :
                        $ligneDTO->quantite = $evenement->getDuree()->format('%h') + $evenement->getDuree()->format('%i') / 60;
                        $ligneDTO->valeur = $contrat->getBaseHeure() * $ligneDTO->quantite;
                        break;

                }
            }
        };

        return new Domains\Ligne($ligneDTO);
    }
}