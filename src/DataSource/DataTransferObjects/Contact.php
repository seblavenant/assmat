<?php

namespace Assmat\DataSource\DataTransferObjects;

use Spear\Silex\Persistence\DataTransferObject;

class Contact implements DataTransferObject
{
    public
        $id,
        $email,
        $password,
        $nom,
        $prenom,
        $adresse,
        $codePostal,
        $ville;
}