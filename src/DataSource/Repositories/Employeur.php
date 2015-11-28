<?php

namespace Assmat\DataSource\Repositories;

use Assmat\DataSource\DataTransferObjects as DTO;

interface Employeur
{
    public function find($id);

    public function findFromContact($contactId);

    public function findFromEmploye($employeId);

    public function persist(DTO\Employeur $employeurDTO);
}