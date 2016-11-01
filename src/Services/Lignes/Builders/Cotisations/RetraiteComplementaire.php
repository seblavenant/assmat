<?php

namespace Assmat\Services\Lignes\Builders\Cotisations;

use Assmat\DataSource\Domains;

class RetraiteComplementaire
{
    public function compute(Domains\Ligne $ligne, Domains\Bulletin $bulletin)
    {
        $ligne->setBase($bulletin->getSalaireBrut());
        $ligne->computeValeur();
    }
}
