<?php

namespace Assmat\DataSource\Repositories\Memory\Evenement\Types;

use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Constants;
use Assmat\DataSource\Domains;

class AbsencePayee
{
    public function getDomain()
    {
        $evenementTypeDTO = new DTO\EvenementType();
        $evenementTypeDTO->id = Constants\Evenements\Type::ABSENCE_PAYEE;
        $evenementTypeDTO->label = 'Absence payée';
        $evenementTypeDTO->code = 'ABSP';
        $evenementTypeDTO->dureeFixe = true;
        $evenementTypeDTO->computeClosure = function($evenement) {
            $evenement->setJourPaye();
        };

        return new Domains\EvenementType($evenementTypeDTO);
    }
}