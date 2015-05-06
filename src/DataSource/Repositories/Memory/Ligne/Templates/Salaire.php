<?php

namespace Assmat\DataSource\Repositories\Memory\Ligne\Templates;

use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Constants;
use Assmat\DataSource\Domains;

class Salaire
{
    public function getDomain()
    {
        $ligneDTO = new DTO\Ligne();
        $ligneDTO->label = 'Salaire';
        $ligneDTO->type = Constants\Lignes\Type::SALAIRE;
        $ligneDTO->action = Constants\Lignes\Action::GAIN;
        $ligneDTO->computeClosure = function(Domains\Bulletin $bulletin) use($ligneDTO) {
            return $this->hydrateFromBulletin($ligneDTO, $bulletin);
        };

        return new Domains\Ligne($ligneDTO);
    }

    private function hydrateFromBulletin($ligneDTO, $bulletin)
    {
        $contrat = $bulletin->getContrat();
        $base = $contrat->getSalaireHoraire();

        $heures = 0;
        $salaireBrut = 0;

        foreach($bulletin->getEvenements() as $evenement)
        {
            $evenementHeures = 0;
            switch($evenement->getTypeId())
            {
                case Constants\Evenements\Type::GARDE :
                    $evenementHeures = $evenement->getDuree()->format('%h') + $evenement->getDuree()->format('%i') / 60;
                    break;
                case Constants\Evenements\Type::ABSENCE_PAYEE;
                case Constants\Evenements\Type::CONGE_PAYE :
                    $evenementHeures = $contrat->getHeuresJour();
                    break;
                case Constants\Evenements\Type::ABSENCE_NON_PAYEE;
                    break;
            }

            $heures += $evenementHeures;
            $salaireBrut +=  $base * $evenementHeures;
        }

        $ligneDTO->base = $base;
        $ligneDTO->quantite = $heures;
        $ligneDTO->valeur = $salaireBrut;
    }
}