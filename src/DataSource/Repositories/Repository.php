<?php

namespace Assmat\DataSource\Repositories;

use Spear\Silex\Persistence\DataTransferObject;

interface Repository
{
    public function getDomain(DataTransferObject $dto);

    public function getDTO();

    public function getFields();
}