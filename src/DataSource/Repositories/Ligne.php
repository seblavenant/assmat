<?php

namespace Assmat\DataSource\Repositories;

use Assmat\DataSource\DataTransferObjects as DTO;

interface Ligne
{
    public function create(DTO\Ligne $ligneDTO);
}