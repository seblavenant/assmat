<?php

namespace Assmat\DataSource\Repositories;

interface Employeur extends Repository
{
    public function find($id);

    public function findFromContact($contactId);
}