<?php

namespace Assmat\DataSource\Repositories;

interface LigneTemplate
{
    public function find($id);

    public function findAll();

    public function findFromTypes(array $types);

    public function findFromContexts(array $contexts);
}