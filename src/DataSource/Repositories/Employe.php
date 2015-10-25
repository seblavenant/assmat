<?php

namespace Assmat\DataSource\Repositories;

interface Employe
{
    public function find($id);

    public function findFromContact($contactId);

    public function findFromEmployeur($employeurId);

    public function findFromKey($key);
}