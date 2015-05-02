<?php

namespace Assmat\DataSource\Repositories\Memory\Lignes;

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
        $ligneDTO->code = Constants\Lignes\Code::CSG_RDS;
        $ligneDTO->type = Constants\Lignes\Type::RETENUE;
        $ligneDTO->taux = self::TAUX;
        $ligneDTO->hydrateFromBulletinClosure = function(Domains\Bulletin $bulletin) use($ligneDTO) {
            $ligneDTO->valeur = round($bulletin->getSalaire()->getValeur($bulletin) * 0.98 * $ligneDTO->taux / 100, 2);
        };

        return new Domains\Ligne($ligneDTO);
    }
}