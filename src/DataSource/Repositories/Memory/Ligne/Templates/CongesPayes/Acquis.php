<?php

namespace Assmat\DataSource\Repositories\Memory\Ligne\Templates\CongesPayes;

use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Constants;
use Assmat\DataSource\Domains;

class Acquis
{
    public function getDomain()
    {
        $ligneDTO = new DTO\Ligne();
        $ligneDTO->label = 'Congés payés aquis';
        $ligneDTO->typeId = Constants\Lignes\Type::CONGES_PAYES_ACQUIS;
        $ligneDTO->actionId = Constants\Lignes\Action::GAIN;
        $ligneDTO->contextId = Constants\Lignes\Context::CONGE_PAYE;
        $ligneDTO->computeClosure = function(Domains\Bulletin $bulletin) use($ligneDTO) {
            $ligneDTO->valeur = $bulletin->getContrat()->getNbCongesPayesMensuel();
        };

        return new Domains\Ligne($ligneDTO);
    }
}