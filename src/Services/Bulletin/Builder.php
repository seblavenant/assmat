<?php

namespace Assmat\Services\Lignes;

use Assmat\DataSource\Domains;
use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Repositories;
use Assmat\DataSource\Constants;
use Assmat\Iterators\Filters as FilterIterator;
use Assmat\DataSource\Repositories\Memory\Lignes\Salaire;

class Builder
{
    private
        $ligneRepository;

    public function __construct(Repositories\Ligne $ligneRepository)
    {
        $this->ligneRepository = $ligneRepository;
    }

    public function build(array $codes, Domains\Bulletin $bulletin)
    {
        $lignes = $this->ligneRepository->findFromCodes($codes);

        if(iterator_count($lignes) === 0)
        {
            return;
        }

        $ligneSalaire = $this->getLigneSalaire($lignes);
        $ligneSalaire->hydrateFromBulletin($bulletin);

        foreach($lignes as $ligne)
        {
            if($ligne->getCode() === Constants\Lignes\Code::SALAIRE)
            {
                continue;
            }

            $ligne->hydrateFromBulletin($bulletin);
        }

        return $lignes;
    }

    private function getLigneSalaire($lignes)
    {
        $ligneSalaireIterator = new FilterIterator\Lignes\Code($lignes, array(Constants\Lignes\Code::SALAIRE));

        if(iterator_count($ligneSalaireIterator) === 0)
        {
            throw new \Exception('La ligne salaire est requise !');
        }

        return current(iterator_to_array($ligneSalaireIterator));
    }
}