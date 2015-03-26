<?php

namespace Assmat\DataSource\Domains;

use Assmat\DataSource\DataTransferObjects as DTO;

class Contrat
{
    private
        $fields;

    public function __construct(DTO\Contrat $contratDTO)
    {
        $this->fields = $contratDTO;
    }

    public function getId()
    {
        return $this->fields->id;
    }

    public function getEmploye()
    {
        return $this->fields->load('employe');
    }
}