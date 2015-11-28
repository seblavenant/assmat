<?php

namespace Assmat\DataSource\Domains;

use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Domains;
use Assmat\DataSource\Repositories;

class Ligne
{
    private
        $fields;

    public function __construct(DTO\Ligne $ligneDTO)
    {
        $this->fields = $ligneDTO;
    }

    public function getId()
    {
        return $this->fields->id;
    }

    public function getTypeId()
    {
        return $this->fields->typeId;
    }

    public function getLabel()
    {
        return $this->fields->label;
    }

    public function getActionId()
    {
        return $this->fields->actionId;
    }

    public function getContextId()
    {
        return $this->fields->contextId;
    }

    public function getBase()
    {
        return $this->fields->base;
    }

    public function getTaux()
    {
        return $this->fields->taux;
    }

    public function getQuantite()
    {
        return $this->fields->quantite;
    }

    public function getValeur()
    {
        return $this->fields->valeur;
    }

    public function setBulletinId($bulletinId)
    {
        $this->fields->bulletinId = $bulletinId;
    }

    public function getBulletinId()
    {
        return $this->fields->bulletinId;
    }

    public function compute(Domains\Bulletin $bulletin)
    {
        $computeClosure = $this->fields->computeClosure;
        $computeClosure($bulletin);
    }

    public function persist(Repositories\Ligne $ligneRepository)
    {
        if($this->fields->id !== null)
        {
            return;
        }

        $ligneRepository->create($this->fields);
    }
}