<?php

namespace Assmat\DataSource\Domains;

use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Domains;

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

    public function getType()
    {
        return $this->fields->type;
    }

    public function getLabel()
    {
        return $this->fields->label;
    }

    public function getAction()
    {
        return $this->fields->action;
    }

    public function getContext()
    {
        return $this->fields->context;
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

    public function compute(Domains\Bulletin $bulletin)
    {
        $computeClosure = $this->fields->computeClosure;
        $computeClosure($bulletin);
    }
}