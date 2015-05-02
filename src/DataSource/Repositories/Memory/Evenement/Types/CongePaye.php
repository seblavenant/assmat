<?php

namespace Assmat\DataSource\Repositories\Memory\Evenement\Types;

use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Constants;
use Assmat\DataSource\Domains;

class CongePaye
{
    public function getDomain()
    {
        $evenementTypeDTO = new DTO\EvenementType();
        $evenementTypeDTO->id = Constants\Evenements\Type::CONGE_PAYE;
        $evenementTypeDTO->label = 'Congé payé';

        return new Domains\EvenementType($evenementTypeDTO);
    }
}