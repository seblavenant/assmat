<?php

namespace Assmat\Services\Lignes\Builders\Remunerations;

use Assmat\DataSource\Domains;
use Assmat\Iterators\Filters as FilterIterators;
use Assmat\DataSource\Constants;

class IndemnitesCP
{
    public function compute(Domains\Ligne $ligne, Domains\Bulletin $bulletin)
    {
        if(empty($ligne->getBase()))
        {
            $tauxJournalierContrat = $bulletin->getContrat()->getSalaireJour();
            $tauxJournalierBase = $tauxJournalierContrat;

            $congesPayes = $bulletin->getCongesPayes();
            if(!$congesPayes instanceof Domains\CongePaye)
            {
                return;
            }
            
            $cpReference = $bulletin->getCongesPayes()->getReference();
            if($cpReference instanceof Domains\CpReference)
            {
                $tauxJournalierReference = $bulletin->getCongesPayes()->getReference()->getTauxJournalier();
                if($tauxJournalierReference > $tauxJournalierContrat)
                {
                    $tauxJournalierBase = $tauxJournalierReference;
                }
            }

            $ligne->setBase($tauxJournalierBase);
        }
        
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
