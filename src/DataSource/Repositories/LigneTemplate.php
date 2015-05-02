<?php

namespace Assmat\DataSource\Repositories;

interface Ligne
{
    public function find($id);

    public function findFromCodes(array $codes);
}