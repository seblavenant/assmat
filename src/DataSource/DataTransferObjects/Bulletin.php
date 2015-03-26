<?php

namespace Assmat\DataSource\DataTransferObjects;

use Spear\Silex\Persistence\DataTransferObject;

class Bulletin implements DataTransferObject
{
    public
        $id,
        $dateDebut,
        $dateFin;
}