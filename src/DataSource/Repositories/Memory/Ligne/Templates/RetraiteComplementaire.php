<?php

namespace Assmat\DataSource\Repositories\Memory\Ligne\Templates;

use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Constants;
use Assmat\DataSource\Domains;

class RetraiteComplementaire
{
    const
        TAUX = 3.10;

    public function getDomain()
    {
        $ligneDTO = new DTO\Ligne();
        $ligneDTO->label = 'Retraite complementaire';
        $ligneDTO->type = Constants\Lignes\Type::CSG_RDS;
        $ligneDTO->action = Constants\Lignes\Action::RETENUE;
        $ligneDTO->context = Constants\Lignes\Context::COTISATION;
        $ligneDTO->taux = self::TAUX;
        $ligneDTO->computeClosure = function(Domains\Bulletin $bulletin) use($ligneDTO) {
            $base = $bulletin->getSalaireBrut();
            $ligneDTO->base = $base;
            $ligneDTO->valeur = round($base * $ligneDTO->taux / 100, 2);
        };

        return new Domains\Ligne($ligneDTO);
    }
}