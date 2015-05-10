<?php

namespace Assmat\DataSource\Domains;

use Assmat\DataSource\DataTransferObjects as DTO;

class Evenement
{
    private
        $jourPaye,
        $congePaye,
        $jourGarde,
        $fields;

    public function __construct(DTO\Evenement $evenementDTO)
    {
        $this->fields = $evenementDTO;

        $this->jourPaye = false;
        $this->congePaye = false;
        $this->jourGarde = false;
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

    public function getContratId()
    {
        return $this->fields->contratId;
    }

    public function getDuree()
    {
        return $this->fields->heureDebut->diff($this->fields->heureFin);
    }

    public function getTypeId()
    {
        return $this->fields->typeId;

    }
    public function getType()
    {
        return $this->fields->load('type');
    }

    public function computeFromType()
    {
        $this->getType()->compute($this);
    }

    public function setJourPaye($jourPaye = true)
    {
        $this->jourPaye = $jourPaye;
    }

    public function isJourPaye()
    {
        return $this->jourPaye === true;
    }

    public function setCongePaye($congePaye = true)
    {
        $this->congePaye = $congePaye;
    }

    public function isCongePaye()
    {
        return $this->congePaye === true;
    }

    public function setJourGarde($jourGarde = true)
    {
        $this->jourGarde = $jourGarde;
    }

    public function isJourGarde()
    {
        return $this->jourGarde === true;
    }
}