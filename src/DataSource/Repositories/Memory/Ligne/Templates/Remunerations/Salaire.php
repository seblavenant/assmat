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

        switch($contrat->getTypeId())
        {
            case Constants\Contrats\Salaire::MENSUALISE :
                $ligneDTO->quantite = $contrat->getHeuresHebdo() * $contrat->getNombreSemainesAn() / 12;
                $ligneDTO->quantite -= $bulletin->getHeuresNonPayee();
                break;
            case Constants\Contrats\Salaire::HEURES :
            default :
                $ligneDTO->quantite = $bulletin->getHeuresPayee();
                break;
        }

        $ligneDTO->valeur = $ligneDTO->base * $ligneDTO->quantite;
    }
}