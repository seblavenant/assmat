<?php

namespace Assmat\DataSource\Domains;

use Assmat\DataSource\DataTransferObjects as DTO;

class Contact
{
    private
        $fields;

    public function __construct(DTO\Contact $contactDTO)
    {
        $this->fields = $contactDTO;
    }

    public function getId()
    {
        return $this->fields->id;
    }

    public function getEmail()
    {
        return $this->fields->email;
    }

    public function getPassword()
    {
        return $this->fields->password;
    }

    public function getNom()
    {
        return $this->fields->nom;
    }

    public function getPrenom()
    {
        return $this->fields->prenom;
    }
}