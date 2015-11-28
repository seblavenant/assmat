<?php

namespace Assmat\DataSource\Domains;

use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Repositories;

class Employe
{
    private
        $fields;

    public function __construct(DTO\Employe $employeDTO)
    {
        $this->fields = $employeDTO;
    }

    public function getId()
    {
        return $this->fields->id;
    }

    public function getSSId()
    {
        return $this->fields->ssId;
    }

    public function getContact()
    {
        return $this->fields->load('contact');
    }

    public function getContrats()
    {
        return $this->fields->load('contrats');
    }

    public function getEmployeurs()
    {
        return $this->fields->load('employeurs');
    }

    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'ssId' => $this->getSSId()
        );
    }

    public function persist(Repositories\Employe $employeRepository)
    {
        return $employeRepository->persist($this->fields);
    }
}