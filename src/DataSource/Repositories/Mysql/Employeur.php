<?php

namespace Assmat\DataSource\Repositories\Mysql;

use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Domains;
use Assmat\DataSource\Repositories;
use Muffin\Queries;
use Muffin\Types;
use Muffin\Tests\Escapers\SimpleEscaper;
use Spear\Silex\Persistence\Fields;
use Spear\Silex\Persistence\DataTransferObject;

class Employeur extends AbstractMysql implements Repositories\Employeur
{
    const
        TABLE_NAME = 'employeur';

    private
        $contactRepository,
        $employeRepository,
        $contratRepository;

    public function __construct(Mysql $db, Repositories\Contact $contactRepository, Repositories\Employe $employeRepository, Repositories\Contrat $contratRepository)
    {
        parent::__construct($db);

        $this->contactRepository = $contactRepository;
        $this->employeRepository = $employeRepository;
        $this->contratRepository = $contratRepository;
    }

    public function find($id)
    {
        $query = $this->getBaseQuery();
        $query->where((new Types\Integer('id'))->equal($id));

        return $this->fetchOne($query);
    }

    public function findFromContact($contactId)
    {
        $query = $this->getBaseQuery();
        $query->where((new Types\Integer('contact_id'))->equal($contactId));

        return $this->fetchOne($query);
    }

    public function findFromEmploye($employeId)
    {
        $fields = array('id', 'paje_emploi_id', 'contact_id');
        $fieldsNamed = array_map(array($this, 'addTableName'), $fields);

        $query = (new Queries\Select())->setEscaper(new SimpleEscaper())
            ->select($fieldsNamed)
            ->from('contrat')
            ->leftJoin('employeur')->on('employeur.id', 'contrat.employeur_id')
            ->leftJoin('contact')->on('contact.id', 'employeur.contact_id')
            ->groupBy('employeur.id')
            ->where((new Types\Integer('contrat.employe_id'))->equal($employeId));

        return $this->fetchAll($query);
    }

    public function addTableName($field)
    {
        return self::TABLE_NAME . '.' . $field;
    }

    private function getBaseQuery()
    {
        $query = (new Queries\Select())->setEscaper(new SimpleEscaper())
            ->select(array('id', 'paje_emploi_id', 'contact_id'))
            ->from(self::TABLE_NAME);

        return $query;
    }

    public function getFields()
    {
        return array(
            'id' => new Fields\NotNullable(new Fields\UnsignedInteger('id')),
            'pajeEmploiId' => new Fields\NotNullable(new Fields\String('paje_emploi_id')),
            'contactId' => new Fields\NotNullable(new Fields\UnsignedInteger('contact_id')),
        );
    }

    public function getDomain(DataTransferObject $dto)
    {
        $dto->set('contact', function() use($dto) {
            return $this->contactRepository->find($dto->contactId);
        });
        $dto->set('contrats', function() use($dto) {
            return $this->contratRepository->findFromEmployeur($dto->id);
        });
        $dto->set('employes', function() use($dto) {
            return $this->employeRepository->findFromEmployeur($dto->id);
        });

        return new Domains\Employeur($dto);
    }

    public function getDTO()
    {
        return new DTO\Employeur();
    }
}