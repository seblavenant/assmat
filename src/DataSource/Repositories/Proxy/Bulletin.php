<?php

namespace Assmat\DataSource\Repositories\Proxy;

use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Repositories;
use Spear\Silex\Persistence\DataTransferObject;

class Bulletin implements Repositories\Bulletin
{
    private
        $bulletinRepository,
        $bulletinRepositoryClosure;

    public function __construct(\Closure $bulletinRepositoryClosure)
    {
        $this->bulletinRepository;
        $this->bulletinRepositoryClosure = $bulletinRepositoryClosure;
    }

    public function find($id)
    {
        $this->getBulletinRepository()->find($id);
    }

    public function findFromContrat($contratId)
    {
        $this->getBulletinRepository()->findFromContrat($contratId);
    }

    public function create(DTO\Bulletin $bulletinDTO)
    {
        $this->getBulletinRepository()->create($bulletinDTO);
    }

    public function getDomain(DataTransferObject $dto){}

    public function getDTO(){}

    public function getFields(){}

    private function getBulletinRepository()
    {
        if($this->bulletinRepository === null)
        {
            $repositoryClosure = $this->bulletinRepositoryClosure();
            $this->bulletinRepository = $repositoryClosure();
        }

        return $this->bulletinRepository;
    }
}