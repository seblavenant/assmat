<?php

namespace Assmat\DataSource\Repositories;

interface Contact
{
    public function find($id);

    public function findFromEmail($email);
}