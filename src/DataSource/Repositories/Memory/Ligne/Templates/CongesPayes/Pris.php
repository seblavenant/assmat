<?php

namespace Assmat\DataSource\Repositories\Memory\Ligne\Templates\CongesPayes;

use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Constants;
use Assmat\DataSource\Domains;
use Assmat\Iterators\Filters as FilterIterators;

class Pris
{
    public function getDomain()
    {
        $ligneDTO = new DTO\Ligne();
        $ligneDTO->label = 'Congés payés pris';
        $ligneDTO->typeId = Constants\Lignes\Type::CONGES_PAYES_PRIS;
        $ligneDTO->actionId = Constants\Lignes\Action::RETENUE;
        $ligneDTO->contextId = Constants\Lignes\Context::CONGE_PAYE;
        $ligneDTO->computeClosure = function(Domains\Bulletin $bulletin) use($ligneDTO) {
            $ligneDTO->valeur = $this->countCongesPayes($bulletin->getEvenements());
        };

        return new Domains\Ligne($ligneDTO);
    }

    private function countCongesPayes(array $evenements)
    {
        $congesPayes = new FilterIterators\Evenements\Type(new \ArrayIterator($evenements), array(Constants\Evenements\Type::CONGE_PAYE));

        return iterator_count($congesPayes);
    }
}