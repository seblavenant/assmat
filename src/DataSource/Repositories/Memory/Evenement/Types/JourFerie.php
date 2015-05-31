<?php

namespace Assmat\DataSource\Repositories\Memory\Evenement\Types;

use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Constants;
use Assmat\DataSource\Domains;

class JourFerie
{
    public function getDomain()
    {
        $evenementTypeDTO = new DTO\EvenementType();
        $evenementTypeDTO->id = Constants\Evenements\Type::JOUR_FERIE;
        $evenementTypeDTO->label = 'Jour Férié';
        $evenementTypeDTO->code = 'JF';
        $evenementTypeDTO->dureeFixe = true;
        $evenementTypeDTO->computeClosure = function($evenement) {
            $evenement->setJourPaye();
        };

        return new Domains\EvenementType($evenementTypeDTO);
    }
}