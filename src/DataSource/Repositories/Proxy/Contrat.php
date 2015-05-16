<?php

namespace Assmat\DataSource\Repositories\Proxy;

use Assmat\DataSource\Repositories;
use Spear\Silex\Persistence\DataTransferObject;

class Contrat implements Repositories\Contrat
{
    private
        $contratRepository,
        $contratRepositoryClosure;

    public function __construct(\Closure $contratRepositoryClosure)
    {
        $this->contratRepositoryClosure = $contratRepositoryClosure;
        $this->contratRepository = null;
    }

    public function find($id)
    {
        $this->getContratRepository()->find($id);
    }

    public function findFromEmploye($employeId)
    {
        $this->getContratRepository()->findFromEmploye($employeId);
    }

    public function getContratRepository()
    {
        if($this->contratRepository === null)
        {
            $contratRepositoryClosure = $this->contratRepositoryClosure;
            $this->contratRepository = $contratRepositoryClosure();
        }

        return $this->contratRepository;
    }

    public function getDomain(DataTransferObject $dto){}

    public function getDTO(){}

    public function getFields(){}
}
