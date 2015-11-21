<?php

namespace Assmat\DataSource\Repositories;

use Assmat\DataSource\DataTransferObjects as DTO;

interface Contact
{
    public function find($id);

    public function findFromEmail($email);

    public function persist(DTO\Contact $contactDTO);
}