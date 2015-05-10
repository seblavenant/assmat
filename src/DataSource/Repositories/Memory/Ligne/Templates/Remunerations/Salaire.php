<?php

namespace Assmat\DataSource\Repositories\Memory\Ligne\Templates\Remunerations;

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
        $ligneDTO->context = Constants\Lignes\Context::REMUNERATION;
        $ligneDTO->computeClosure = function(Domains\Bulletin $bulletin) use($ligneDTO) {
            return $this->hydrateFromBulletin($ligneDTO, $bulletin);
        };

        return new Domains\Ligne($ligneDTO);
    }

    private function hydrateFromBulletin($ligneDTO, $bulletin)
    {
        $contrat = $bulletin->getContrat();
        $ligneDTO->base = $contrat->getSalaireHoraire();

        $heuresPayees = 0;
        $heuresNonPayees = 0;

        foreach($bulletin->getEvenements() as $evenement)
        {
            $evenementHeuresPayees = 0;
            $evenementHeuresNonPayees = 0;
            switch($evenement->getTypeId())
            {
                case Constants\Evenements\Type::GARDE :
                    $evenementHeuresPayees = $evenement->getDuree()->format('%h') + $evenement->getDuree()->format('%i') / 60;
                    break;
                case Constants\Evenements\Type::ABSENCE_PAYEE;
                case Constants\Evenements\Type::CONGE_PAYE :
                    $evenementHeuresPayees = $contrat->getHeuresJour();
                    break;
                case Constants\Evenements\Type::ABSENCE_NON_PAYEE;
                    $evenementHeuresNonPayees = $contrat->getHeuresJour();
                    break;
            }

            $heuresPayees += $evenementHeuresPayees;
            $heuresNonPayees += $evenementHeuresNonPayees;
        }

        switch($contrat->getTypeId())
        {
            case Constants\Contrats\Salaire::MENSUALISE :
                $ligneDTO->quantite = $contrat->getHeuresHebdo() * $contrat->getNombreSemainesAn() / 12;
                $ligneDTO->quantite -= $heuresNonPayees;
                break;
            case Constants\Contrats\Salaire::HEURES :
            default :
                    $ligneDTO->quantite = $heuresPayees;
                    break;
        }

        $ligneDTO->valeur = $ligneDTO->base * $ligneDTO->quantite;
    }
}