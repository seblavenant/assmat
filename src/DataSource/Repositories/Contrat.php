<?php

namespace Assmat\DataSource\Repositories;

interface Contrat extends Repository
{
    public function find($id);

    public function findFromEmploye($employeId);
}