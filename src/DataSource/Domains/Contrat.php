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

    public function getNom()
    {
        return $this->fields->nom;
    }

    public function getEmploye()
    {
        return $this->fields->load('employe');
    }

    public function getSalaireHoraire()
    {
        return $this->fields->salaireHoraire;
    }

    public function getJoursGarde()
    {
        return $this->fields->joursGarde;
    }

    public function getHeuresHebdo()
    {
        return $this->fields->heuresHebdo;
    }

    public function getHeuresJour()
    {
        return $this->fields->heuresHebdo / $this->fields->joursGarde;
    }

    public function getBulletins()
    {
        return $this->fields->load('bulletins');
    }
}