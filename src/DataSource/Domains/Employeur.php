<?php

namespace Assmat\DataSource\Domains;

use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Repositories;

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

    public function getPajeEmploiId()
    {
        return $this->fields->pajeEmploiId;
    }

    public function getContact()
    {
        return $this->fields->load('contact');
    }

    public function getContrats()
    {
        return $this->fields->load('contrats');
    }

    public function getEmployes()
    {
        return $this->fields->load('employes');
    }

    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'pajeEmploiId' => $this->getPajeEmploiId()
        );
    }

    public function persist(Repositories\Employeur $employeurRepository)
    {
        return $employeurRepository->persist($this->fields);
    }
}