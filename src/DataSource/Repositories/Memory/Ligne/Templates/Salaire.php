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
            return $this->hydrateFromBulletin($ligneDTO, $bulletin);
        };

        return new Domains\Ligne($ligneDTO);
    }

    private function hydrateFromBulletin($ligneDTO, $bulletin)
    {
        $heures = 0;
        $salaireBrut = 0;

        foreach($bulletin->getEvenements() as $evenement)
        {
            $contrat = $bulletin->getContrat();
            $evenementHeures = 0;

            switch($evenement->getTypeId())
            {
                case Constants\Evenements\Type::ACCUEIL :
                    $evenementHeures = $evenement->getDuree()->format('%h') + $evenement->getDuree()->format('%i') / 60;
                    break;
                case Constants\Evenements\Type::CONGE_PAYE :
                    $evenementHeures = $contrat->getHeuresJour();
            }

            $heures += $evenementHeures;
            $salaireBrut += $contrat->getSalaireHoraire() * $evenementHeures;
        }

        $ligneDTO->quantite = $heures;
        $ligneDTO->valeur = $salaireBrut;

        $bulletin->setSalaire(new Domains\Ligne($ligneDTO));
    }
}