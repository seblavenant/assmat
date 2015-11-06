<?php

namespace Assmat\DataSource\Domains;

use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Constants;
use Assmat\DataSource\Domains;
use Assmat\DataSource\Repositories;

class Contrat
{
    const
        NB_SEMAINE_AN_DEFAULT = 52,
        NB_JOUR_HEBDO_DEFAULT = 5,
        CP_AN_DEFAULT = 25;

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

    public function getEmployeur()
    {
        return $this->fields->load('employeur');
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

    public function getHeuresMensuel()
    {
        return $this->getHeuresHebdo() * $this->getNombreSemainesAn() / 12;
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

    public function setTypeId($typeId)
    {
        return $this->fields->typeId = $typeId;
    }

    public function getIndemnites()
    {
        return $this->fields->load('indemnites');
    }

    public function getNbCongesPayesMensuel()
    {
        $baseSemaine = self::CP_AN_DEFAULT * $this->getNombreSemainesAn() / self::NB_SEMAINE_AN_DEFAULT;
        $baseJour = $baseSemaine * $this->getJoursGarde() / self::NB_JOUR_HEBDO_DEFAULT;

        return $baseJour / 12;
    }

    public function isGrantedEmployeur(Domains\Contact $contact)
    {
        return $contact->getId() === $this->getEmployeur()->getContact()->getId();
    }

    public function validateContactAutorisation(Domains\Contact $contact)
    {
        if(
            $contact->getId() !== $this->getEmploye()->getContact()->getId()
            && $contact->getId() !== $this->getEmployeur()->getContact()->getId()
        ) {
            throw new \Exception('Vous n\'êtes pas autorisé à adminitrer ce contrat');
        }
    }

    public function getColorHex()
    {
        $colors = array('00aedb', 'a200ff', 'f47835', 'd41243', '8ec127');

        srand($this->getId());

        return $colors[rand(0, count($colors) - 1)];
    }

    public function validateIsGrantedEmployeur(Domains\Contact $contact)
    {
        if(! $this->isGrantedEmployeur($contact))
        {
            throw new \Exception('Vous devez avoir les droits employeur sur ce contrat pour l\'administrer');
        }
    }

    public function persist(Repositories\Contrat $contratRepository)
    {
        return $contratRepository->persist($this->fields);
    }
}