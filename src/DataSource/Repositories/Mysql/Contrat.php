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
        TABLE_NAME = 'contrat';

    private
        $indemniteRepository,
        $employeRepository,
        $employeurRepository;

    public function __construct(Mysql $db, Repositories\Indemnite $indemniteRepository, Repositories\Employe $employeRepository, Repositories\Employeur $employeurRepository)
    {
        parent::__construct($db);

        $this->indemniteRepository = $indemniteRepository;
        $this->employeRepository = $employeRepository;
        $this->employeurRepository = $employeurRepository;
    }

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
            ->select(array('id', 'nom', 'salaire_horaire', 'jours_garde', 'heures_hebdo', 'type_id', 'employe_id', 'employeur_id'))
            ->from(self::TABLE_NAME);

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
            'employeurId' => new Fields\NotNullable(new Fields\Integer('employeur_id')),
        );
    }

    public function getDomain(DataTransferObject $dto)
    {
        $dto->set('indemnites', function() use($dto) {
            return $this->indemniteRepository->findFromContrat($dto->id);
        });

        $dto->set('employe', function() use($dto) {
            return $this->employeRepository->find($dto->employeId);
        });

        $dto->set('employeur', function() use($dto) {
            return $this->employeurRepository->find($dto->employeurId);
        });

        return new Domains\Contrat($dto);
    }

    public function getDTO()
    {
        return new DTO\Contrat();
    }
}