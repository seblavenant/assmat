<?php

namespace Assmat\DataSource\Repositories\Memory\Ligne\Templates\Cotisations;

use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Constants;
use Assmat\DataSource\Domains;

class AssuranceChomage
{
    const
        TAUX = 2.40;

    public function getDomain()
    {
        $ligneDTO = new DTO\Ligne();
        $ligneDTO->label = 'Assurance chômage';
        $ligneDTO->typeId = Constants\Lignes\Type::CSG_RDS;
        $ligneDTO->actionId = Constants\Lignes\Action::RETENUE;
        $ligneDTO->contextId = Constants\Lignes\Context::COTISATION;
        $ligneDTO->taux = self::TAUX;
        $ligneDTO->computeClosure = function(Domains\Bulletin $bulletin) use($ligneDTO) {
            $base = $bulletin->getSalaireBrut();
            $ligneDTO->base = $base;
            $ligneDTO->valeur = round($base * $ligneDTO->taux / 100, 2);
        };

        return new Domains\Ligne($ligneDTO);
    }
}