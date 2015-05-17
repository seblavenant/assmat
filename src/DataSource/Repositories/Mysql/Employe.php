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
        $contratRepository;

    public function __construct(Mysql $db, Repositories\Contact $contactRepository, Repositories\Contrat $contratRepository)
    {
        parent::__construct($db);

        $this->contactRepository = $contactRepository;
        $this->contratRepository = $contratRepository;
    }

    public function find($id)
    {
        $query = $this->getBaseQuery();
        $query->where((new Types\Integer('id'))->equal($id));

        return $this->fetchOne($query);
    }

    public function findFromEmployeur($employeurId)
    {
        $query = $this->getBaseQuery();
        $query->where((new Types\Integer('employeur_id'))->equal($employeurId));

        return $this->fetchAll($query);
    }

    private function getBaseQuery()
    {
        $query = (new Queries\Select())->setEscaper(new SimpleEscaper())
            ->select(array('id', 'ss_id', 'contact_id', 'employeur_id'))
            ->from(self::TABLE_NAME);

        return $query;
    }

    public function getFields()
    {
        return array(
            'id' => new Fields\NotNullable(new Fields\UnsignedInteger('id')),
            'ssId' => new Fields\NotNullable(new Fields\UnsignedInteger('ss_id')),
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

        return new Domains\Employe($dto);
    }

    public function getDTO()
    {
        return new DTO\Employe();
    }
}