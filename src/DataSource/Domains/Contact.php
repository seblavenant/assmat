<?php

namespace Assmat\DataSource\Domains;

use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Repositories;

class Contact
{
    private
        $fields;

    public function __construct(DTO\Contact $contactDTO)
    {
        $this->fields = $contactDTO;
    }

    public function getId()
    {
        return $this->fields->id;
    }

    public function getAuthCode()
    {
        return $this->fields->authCode;
    }

    public function getEmail()
    {
        return $this->fields->email;
    }

    public function getPassword()
    {
        return $this->fields->password;
    }

    public function getNom()
    {
        return $this->fields->nom;
    }

    public function getPrenom()
    {
        return $this->fields->prenom;
    }

    public function getAdresse()
    {
        return $this->fields->adresse;
    }

    public function getCodePostal()
    {
        return $this->fields->codePostal;
    }

    public function getVille()
    {
        return $this->fields->ville;
    }

    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'authCode' => $this->getAuthCode(),
            'email' => $this->getEmail(),
            'password' => $this->getPassword(),
            'nom' => $this->getNom(),
            'prenom' => $this->getPrenom(),
            'adresse' => $this->getAdresse(),
            'codePostal' => $this->getCodePostal(),
            'ville' => $this->getVille(),
        );
    }

    public function persist(Repositories\Contact $contactRepository)
    {
        return $contactRepository->persist($this->fields);
    }

    public function savePassword($password)
    {
        $this->fields->password = $password;

        return $this;
    }
}