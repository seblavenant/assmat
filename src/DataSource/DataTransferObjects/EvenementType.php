<?php

namespace Assmat\DataSource\DataTransferObjects;

use Spear\Silex\Persistence\DataTransferObject;

class EvenementType implements DataTransferObject
{
    public
        $id,
        $label,
        $dureeFixe,
        $computeClosure;
}