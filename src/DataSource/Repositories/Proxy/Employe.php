<?php

namespace Assmat\DataSource\Repositories\Proxy;

use Assmat\DataSource\Repositories;
use Assmat\DataSource\DataTransferObjects as DTO;

class Employe implements Repositories\Employe
{
    private
        $employeRepository,
        $employeRepositoryClosure;

    public function __construct(\Closure $employeRepositoryClosure)
    {
        $this->employeRepositoryClosure = $employeRepositoryClosure;
        $this->employeRepository = null;
    }

    public function find($id)
    {
        return $this->getEmployeRepository()->find($id);
    }

    public function findFromContact($contactId)
    {
        return $this->getEmployeRepository()->findFromContact($contactId);
    }

    public function findFromEmployeur($employeurId)
    {
        return $this->getEmployeRepository()->findFromEmployeur($employeurId);
    }

    public function findFromAuthCode($authCode)
    {
        return $this->getEmployeRepository()->findFromAuthCode($authCode);
    }

    public function persist(DTO\Employe $employeDTO)
    {
        return $this->getEmployeRepository()->persist($employeDTO);
    }

    public function getEmployeRepository()
    {
        if($this->employeRepository === null)
        {
            $employeRepositoryClosure = $this->employeRepositoryClosure;
            $this->employeRepository = $employeRepositoryClosure();
        }

        return $this->employeRepository;
    }
}
