<?php

namespace Assmat\DataSource\Repositories\Memory\Evenement\Types;

use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Constants;
use Assmat\DataSource\Domains;

class Garde
{
    public function getDomain()
    {
        $evenementTypeDTO = new DTO\EvenementType();
        $evenementTypeDTO->id = Constants\Evenements\Type::GARDE;
        $evenementTypeDTO->label = 'Garde';
        $evenementTypeDTO->code = 'GRD';
        $evenementTypeDTO->dureeFixe = false;
        $evenementTypeDTO->computeClosure = function($evenement) {
            $evenement->setJourPaye();
            $evenement->setjourGarde();
        };

        return new Domains\EvenementType($evenementTypeDTO);
    }
}