<?php

namespace Assmat\DataSource\Repositories;

interface Contrat
{
    public function find($id);

    public function findFromEmploye($employeId);
}