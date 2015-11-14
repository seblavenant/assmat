<?php

namespace Assmat\DataSource\Repositories;

interface Employeur
{
    public function find($id);

    public function findFromContact($contactId);
}