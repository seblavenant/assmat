<?php

namespace Assmat\DataSource\Repositories;

use Assmat\DataSource\DataTransferObjects as DTO;

interface Indemnite
{
    public function find($id);

    public function findFromContrat($contratId);

    public function persist(DTO\Indemnite $indemniteDTO);
}