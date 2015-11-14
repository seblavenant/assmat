<?php

namespace Assmat\DataSource\Repositories\Memory\Ligne\Templates\Remunerations;

use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Constants;
use Assmat\DataSource\Domains;
use Spear\Silex\Persistence\DataTransferObject;

class HeuresComplementaires
{
    public function getDomain()
    {
        $ligneDTO = new DTO\Ligne();
        $ligneDTO->label = 'Heures complementaires';
        $ligneDTO->typeId = Constants\Lignes\Type::SALAIRE;
        $ligneDTO->actionId = Constants\Lignes\Action::GAIN;
        $ligneDTO->contextId = Constants\Lignes\Context::REMUNERATION;
        $ligneDTO->computeClosure = function(Domains\Bulletin $bulletin) use($ligneDTO) {
            return $this->hydrateFromBulletin($ligneDTO, $bulletin);
        };

        return new Domains\Ligne($ligneDTO);
    }

    private function hydrateFromBulletin(DataTransferObject $ligneDTO, Domains\Bulletin $bulletin)
    {
        $contrat = $bulletin->getContrat();
        $ligneDTO->base = $contrat->getSalaireHoraire();
        $ligneDTO->quantite = $bulletin->getHeuresComplementaires();
        $ligneDTO->valeur = $ligneDTO->base * $ligneDTO->quantite;
    }
}