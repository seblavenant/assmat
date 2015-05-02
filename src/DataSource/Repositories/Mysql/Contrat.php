<?php

namespace Assmat\DataSource\Repositories\Mysql;

use Assmat\DataSource\Domains;
use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Repositories;
use Muffin\Queries;
use Muffin\Types;
use Muffin\Tests\Escapers\SimpleEscaper;
use Spear\Silex\Persistence\Fields;
use Spear\Silex\Persistence\DataTransferObject;

class Contrat extends AbstractMysql implements Repositories\Contrat
{
    const
        DB_NAME = 'contrat';

    public function find($id)
    {
        $query = $this->getBaseQuery();
        $query->where((new Types\Integer('id'))->equal($id));

        return $this->fetchOne($query);
    }

    public function findFromEmploye($employeId)
    {
        $query = $this->getBaseQuery();
        $query->where((new Types\Integer('employe_id'))->equal($employeId));

        return $this->fetchAll($query);
    }

    private function getBaseQuery()
    {
        $query = (new Queries\Select())->setEscaper(new SimpleEscaper())
            ->select(array('id', 'nom', 'salaire_horaire', 'jours_garde', 'heures_hebdo', 'type_id', 'employe_id'))
            ->from(self::DB_NAME);

        return $query;
    }

    public function getFields()
    {
        return array(
            'id' => new Fields\NotNullable(new Fields\UnsignedInteger('id')),
            'nom' => new Fields\NotNullable(new Fields\String('nom')),
            'salaireHoraire' => new Fields\NotNullable(new Fields\Float('salaire_horaire')),
            'joursGarde' => new Fields\NotNullable(new Fields\Integer('jours_garde')),
            'heuresHebdo' => new Fields\NotNullable(new Fields\Integer('heures_hebdo')),
            'typeId' => new Fields\NotNullable(new Fields\Integer('type_id')),
            'employeId' => new Fields\NotNullable(new Fields\Integer('employe_id')),
        );
    }

    public function getDomain(DataTransferObject $dto)
    {
        return new Domains\Contrat($dto);
    }

    public function getDTO()
    {
        return new DTO\Contrat();
    }
}