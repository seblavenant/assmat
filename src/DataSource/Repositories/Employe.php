<?php

namespace Assmat\DataSource\Repositories;

use Assmat\DataSource\DataTransferObjects as DTO;

interface Employe
{
    public function find($id);

    public function findFromContact($contactId);

    public function findFromEmployeur($employeurId);

    public function findFromAuthCode($authCode);

    public function persist(DTO\Employe $employeDTO);
}