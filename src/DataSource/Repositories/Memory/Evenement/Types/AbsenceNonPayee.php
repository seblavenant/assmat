<?php

namespace Assmat\DataSource\Repositories\Memory\Evenement\Types;

use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Constants;
use Assmat\DataSource\Domains;

class AbsenceNonPayee
{
    public function getDomain()
    {
        $evenementTypeDTO = new DTO\EvenementType();
        $evenementTypeDTO->id = Constants\Evenements\Type::ABSENCE_NON_PAYEE;
        $evenementTypeDTO->label = 'Absence non payée';
        $evenementTypeDTO->dureeFixe = true;
        $evenementTypeDTO->computeClosure = function($evenement) {
        };

        return new Domains\EvenementType($evenementTypeDTO);
    }
}