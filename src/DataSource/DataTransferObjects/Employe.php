<?php

namespace Assmat\DataSource\DataTransferObjects;

use Spear\Silex\Persistence\DataTransferObjects\Related;

class Employe extends Related
{
    public
        $id,
        $ssId,
        $contactId;

    public function __construct()
    {
        parent::__construct(array(
            'contact',
        ));
    }
}