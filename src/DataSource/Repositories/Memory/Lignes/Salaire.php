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
            $ligneDTO->quantite = 12;
            $ligneDTO->valeur = 1000;
        };

        return new Domains\Ligne($ligneDTO);
    }
}