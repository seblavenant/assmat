<?php

namespace Assmat\Services\Lignes\Builders\CongesPayes;

use Assmat\DataSource\Domains;

class CpAcquis
{
    public function compute(Domains\Ligne $ligne, Domains\Bulletin $bulletin)
    {
        $ligne->setBase($bulletin->getContrat()->getNbCongesPayesMensuel());
        $ligne->computeValeur();
    }
}
