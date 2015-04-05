<?php

namespace Assmat\DataSource\Repositories;

interface Evenement extends Repository
{
    public function find($id);

    public function findFromContrat($contratId);
}