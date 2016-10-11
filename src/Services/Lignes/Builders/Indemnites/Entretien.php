<?php

namespace Assmat\Services\Lignes\Builders\Indemnites;

use Assmat\DataSource\Domains;

class Entretien
{
    public function compute(Domains\Ligne $ligne, Domains\Bulletin $bulletin)
    {
        if(empty($ligne->getBase()))
        {
            $indemnites = $bulletin->getContrat()->getIndemniteByTypeId($ligne->getTypeId());
            if($indemnites instanceof Domains\Indemnite)
            {
                $ligne->setBase($indemnites->getMontant());
            }
        }

        if(empty($ligne->getQuantite()))
        {
            $ligne->setQuantite($bulletin->getJoursGardes());
        }

        $ligne->computeValeur();
    }
}
