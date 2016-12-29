<?php

namespace Assmat\DataSource\Repositories;

use Assmat\DataSource\DataTransferObjects as DTO;

interface CpReference
{
    public function persist(DTO\CpReference $cpReferenceDTO);

    public function findOneFromContratAndDate($contratId, $anneeReference = null);
}
