<?php

namespace Assmat\DataSource\Domains;

use Assmat\DataSource\DataTransferObjects as DTO;

class Evenement
{
    private
        $fields;

    public function __construct(DTO\Evenement $evenementDTO)
    {
        $this->fields = $evenementDTO;
    }

    public function getId()
    {
        return $this->fields->id;
    }

    public function getDate()
    {
        return $this->fields->date;
    }

    public function getHeureDebut()
    {
        return $this->fields->heureDebut;
    }

    public function getHeureFin()
    {
        return $this->fields->heureFin;
    }

    public function getType()
    {
        return $this->fields->type;
    }
}