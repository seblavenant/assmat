<?php

namespace Assmat\DataSource\Domains;

use Assmat\DataSource\DataTransferObjects as DTO;

class Employeur
{
    private
        $fields;

    public function __construct(DTO\Employeur $employeurDTO)
    {
        $this->fields = $employeurDTO;
    }

    public function getId()
    {
        return $this->fields->id;
    }

    public function getContact()
    {
        return $this->fields->load('contact');
    }

    public function getEmployes()
    {
        return $this->fields->load('employes');
    }

    public function getPajeEmploiId()
    {
        return $this->fields->pajeEmploiId;
    }
}