<?php

namespace Assmat\DataSource\Repositories;

interface Contact extends Repository
{
    public function find($id);
}