<?php

namespace Assmat\DataSource\Repositories;

interface Indemnite
{
    public function find($id);

    public function findFromContrat($contratId);
}