<?php

namespace Assmat\DataSource\Repositories;

interface Indemnite extends Repository
{
    public function find($id);

    public function findFromContrat($contratId);
}