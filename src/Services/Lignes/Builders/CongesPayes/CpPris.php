<?php

namespace Assmat\Services\Lignes\Builders\CongesPayes;

use Assmat\DataSource\Domains;
use Assmat\Iterators\Filters as FilterIterators;
use Assmat\DataSource\Constants;

class CpPris
{
    public function compute(Domains\Ligne $ligne, Domains\Bulletin $bulletin)
    {
        if(empty($ligne->getQuantite()))
        {
            $ligne->setQuantite((int) $this->countCongesPayes($bulletin->getEvenements()));
        }
        $ligne->computeValeur();
    }

    private function countCongesPayes(array $evenements)
    {
        $congesPayes = new FilterIterators\Evenements\Type(new \ArrayIterator($evenements), array(Constants\Evenements\Type::CONGE_PAYE));
    
        return iterator_count($congesPayes);
    }
}
