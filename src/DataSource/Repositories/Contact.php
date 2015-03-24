<?php

namespace Assmat\DataSource\Repositories;

use Assmat\DataSource\Domains;

interface Contact extends Repository
{
    public function find($id);
}