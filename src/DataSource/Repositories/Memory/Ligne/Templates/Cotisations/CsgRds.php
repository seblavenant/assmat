<?php

namespace Assmat\DataSource\Repositories\Memory\Ligne\Templates\Cotisations;

use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Constants;
use Assmat\DataSource\Domains;

class CsgRds
{
    const
        TAUX = 2.9;

    public function getDomain()
    {
        $ligneDTO = new DTO\Ligne();
        $ligneDTO->label = 'CGS + RDS';
        $ligneDTO->typeId = Constants\Lignes\Type::SECURITE_SOCIALE;
        $ligneDTO->actionId = Constants\Lignes\Action::RETENUE;
        $ligneDTO->contextId = Constants\Lignes\Context::COTISATION;
        $ligneDTO->taux = self::TAUX;
        $ligneDTO->computeClosure = function(Domains\Bulletin $bulletin) use($ligneDTO) {
            $base = $bulletin->getSalaireBrut() * 0.9825;
            $ligneDTO->base = $base;
            $ligneDTO->valeur = round($base * $ligneDTO->taux / 100, 2);
        };

        return new Domains\Ligne($ligneDTO);
    }
}