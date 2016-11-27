<?php

namespace Assmat\DataSource\Repositories\Memory;

use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Repositories;

class CpReference implements Repositories\CpReference 
{
    public function persist(DTO\CpReference $cpReferenceDTO)
    {
        throw new \RuntimeException('Not implemented');
    }

    public function findOneFromContratAndDate($contratId, $anneeReference = null)
    {
        return null;
    }
}