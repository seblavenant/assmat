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
                $quantite = 0;
                $valeur = 0;

                switch($evenement->getTypeId())
                {
                    case Constants\Evenements\Type::ACCUEIL :
                        $quantite = $evenement->getDuree()->format('%h') + $evenement->getDuree()->format('%i') / 60;
                        break;
                    case Constants\Evenements\Type::CONGE_PAYE :
                        $quantite = $contrat->getHeuresJour();
                }

                $ligneDTO->quantite += $quantite;
                $ligneDTO->valeur += $contrat->getSalaireHoraire() * $quantite;
            }
        };

        return new Domains\Ligne($ligneDTO);
    }
}