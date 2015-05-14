<?php

namespace Assmat\DataSource\DataTransferObjects;

use Spear\Silex\Persistence\DataTransferObject;

class Indemnite implements DataTransferObject
{
    public
        $id,
        $montant,
        $typeId,
        $contratId;
}