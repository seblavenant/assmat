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

class Employe extends AbstractMysql implements Repositories\Employe
{
    const
        TABLE_NAME = 'employe';

    private
        $contactRepository,
        $contratRepository,
        $employeurRepository;

    public function __construct(Mysql $db, Repositories\Contact $contactRepository, Repositories\Contrat $contratRepository, Repositories\Employeur $employeurRepository)
    {
        parent::__construct($db);

        $this->contactRepository = $contactRepository;
        $this->contratRepository = $contratRepository;
        $this->employeurRepository = $employeurRepository;
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

    public function findFromEmployeur($employeurId)
    {
        $fields = array('id', 'ss_id', 'contact_id');
        $fieldsNamed = array_map(array($this, 'addTableName'), $fields);

        $query = (new Queries\Select())->setEscaper(new SimpleEscaper())
            ->select($fieldsNamed)
            ->from('contrat')
            ->leftJoin('employe')->on('employe.id', 'contrat.employe_id')
            ->leftJoin('contact')->on('contact.id', 'employe.contact_id')
            ->groupBy('employe.id')
            ->where((new Types\Integer('contrat.employeur_id'))->equal($employeurId));

        return $this->fetchAll($query);
    }

    public function addTableName($field)
    {
        return self::TABLE_NAME . '.' . $field;
    }

    private function getBaseQuery()
    {
        $query = (new Queries\Select())->setEscaper(new SimpleEscaper())
            ->select(array('id', 'ss_id', 'contact_id'))
            ->from(self::TABLE_NAME);

        return $query;
    }

    public function getFields()
    {
        return array(
            'id' => new Fields\NotNullable(new Fields\UnsignedInteger('id')),
            'ssId' => new Fields\NotNullable(new Fields\String('ss_id')),
            'contactId' => new Fields\NotNullable(new Fields\UnsignedInteger('contact_id')),
        );
    }

    public function getDomain(DataTransferObject $dto)
    {
        $dto->set('contact', function() use($dto) {
            return $this->contactRepository->find($dto->contactId);
        });
        $dto->set('contrats', function() use($dto) {
            return $this->contratRepository->findFromEmploye($dto->id);
        });
        $dto->set('employeurs', function() use($dto) {
            return $this->employeurRepository->findFromEmploye($dto->id);
        });

        return new Domains\Employe($dto);
    }

    public function getDTO()
    {
        return new DTO\Employe();
    }
}