<?php

namespace Assmat\DataSource\Repositories;

interface EvenementType
{
    public function find($id);

    public function findAll();
}