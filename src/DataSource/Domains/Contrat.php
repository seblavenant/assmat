<?php

namespace Assmat\DataSource\Domains;

use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Constants;

class Contrat
{
    const
        NB_SEMAINE_AN_DEFAULT = 52;

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

    public function getNombreSemainesAn()
    {
        if($this->fields->nombreSemainesAn === null)
        {
            $this->fields->nombreSemainesAn = self::NB_SEMAINE_AN_DEFAULT;
        }

        return $this->fields->nombreSemainesAn;
    }

    public function getTypeId()
    {
        return $this->fields->typeId;
    }

    public function isMensualise()
    {
        return $this->fields->typeId === Constants\Contrats\Salaire::MENSUALISE;
    }

    public function getIndemnites()
    {
        return $this->fields->load('indemnites');
    }

}