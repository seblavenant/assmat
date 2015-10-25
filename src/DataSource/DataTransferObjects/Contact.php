<?php

namespace Assmat\DataSource\DataTransferObjects;

use Spear\Silex\Persistence\DataTransferObject;

class Contact implements DataTransferObject
{
    public
        $id,
        $key,
        $email,
        $password,
        $nom,
        $prenom,
        $adresse,
        $codePostal,
        $ville;
}