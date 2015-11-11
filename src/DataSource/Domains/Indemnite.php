<?php

namespace Assmat\DataSource\Domains;

use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Repositories;

class Indemnite
{
    private
        $fields;

    public function __construct(DTO\Indemnite $indemniteDTO)
    {
        $this->fields = $indemniteDTO;
    }

    public function getId()
    {
        return $this->fields->id;
    }

    public function getMontant()
    {
        return $this->fields->montant;
    }

    public function setMontant($montant)
    {
        $this->fields->montant = $montant;

        return $this;
    }

    public function getTypeId()
    {
        return $this->fields->typeId;
    }

    public function getContratId()
    {
        return $this->fields->contratId;
    }

    public function setContratId($contratId)
    {
        $this->fields->contratId = $contratId;

        return $this;
    }

    public function persist(Repositories\Indemnite $indemniteRepository)
    {
        return $indemniteRepository->persist($this->fields);
    }
}