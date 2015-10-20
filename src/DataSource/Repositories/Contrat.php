<?php

namespace Assmat\DataSource\Repositories;

use Assmat\DataSource\DataTransferObjects as DTO;
interface Contrat
{
    public function find($id);

    public function findFromEmploye($employeId);

    public function findFromEmployeur($employeurId);

    public function persist(DTO\Contrat $contratDTO);
}