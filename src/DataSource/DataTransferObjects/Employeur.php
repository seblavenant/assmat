<?php

namespace Assmat\DataSource\DataTransferObjects;

use Spear\Silex\Persistence\DataTransferObjects\Related;

class Employeur extends Related
{
    public
        $id,
        $pajeEmploiId,
        $contactId;

    public function __construct()
    {
        parent::__construct(array(
            'contact',
        ));
    }
}