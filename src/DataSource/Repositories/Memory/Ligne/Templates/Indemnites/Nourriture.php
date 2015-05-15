<?php

namespace Assmat\DataSource\Repositories\Memory\Ligne\Templates\Indemnites;

use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Constants;
use Assmat\DataSource\Domains;

class Nourriture extends AbstractIndemnite
{
    public function getDomain()
    {
        $ligneDTO = new DTO\Ligne();
        $ligneDTO->label = 'Indemnites nourriture';
        $ligneDTO->typeId = Constants\Lignes\Type::INDEMNITES_NOURRITURE;
        $ligneDTO->actionId = Constants\Lignes\Action::GAIN;
        $ligneDTO->contextId = Constants\Lignes\Context::INDEMNITE;
        $ligneDTO->computeClosure = function(Domains\Bulletin $bulletin) use($ligneDTO) {
            $this->compute($bulletin, $ligneDTO);
        };

        return new Domains\Ligne($ligneDTO);
    }
}