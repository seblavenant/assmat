<?php

namespace Assmat\Services\Lignes\Builders\Remunerations;

use Assmat\DataSource\Domains;

class HeuresComplementaires
{
    public function compute(Domains\Ligne $ligne, Domains\Bulletin $bulletin)
    {
        if(empty($ligne->getBase()))
        {
            $ligne->setBase($bulletin->getContrat()->getSalaireHoraire());
        }
        
        if(empty($ligne->getQuantite()))
        {
            $ligne->setQuantite($bulletin->getHeuresComplementaires());
        }
        
        $ligne->computeValeur();
    }
}
