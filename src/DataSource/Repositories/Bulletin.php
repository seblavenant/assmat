<?php

namespace Assmat\DataSource\Repositories;

interface Bulletin extends Repository
{
    public function find($id);

    public function findFromContrat($contratId);
}