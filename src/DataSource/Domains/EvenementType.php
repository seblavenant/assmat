<?php

namespace Assmat\DataSource\Domains;

use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Domains;

class EvenementType
{
    private
        $fields;

    public function __construct(DTO\EvenementType $evenementTypeDTO)
    {
        $this->fields = $evenementTypeDTO;
    }

    public function getId()
    {
        return $this->fields->id;
    }

    public function getLabel()
    {
        return $this->fields->label;
    }

    public function isDureeFixe()
    {
        return $this->fields->dureeFixe === true;
    }

    public function compute($evenement)
    {
        $computeClosure = $this->fields->computeClosure;
        $computeClosure($evenement);
    }
}