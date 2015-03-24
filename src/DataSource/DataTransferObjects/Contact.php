<?php

namespace Assmat\DataSource\DataTransferObjects;

use Spear\Silex\Persistence\DataTransferObject;

class Contact implements DataTransferObject
{
    public
        $id,
        $nom,
        $prenom,
        $adresse,
        $codePostal,
        $ville;
}