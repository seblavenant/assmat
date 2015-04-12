<?php

namespace Assmat\DataSource\Repositories\Mysql;

use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Domains;
use Assmat\DataSource\Repositories;
use Muffin\Queries;
use Muffin\Types;
use Muffin\Tests\Escapers\SimpleEscaper;

use Spear\Silex\Persistence\Fields;
use Spear\Silex\Persistence\DTOHydrators;
use Spear\Silex\Persistence\DataTransferObject;
use Doctrine\DBAL\Driver\Connection;

class Employeur extends AbstractMysql implements Repositories\Employeur
{
    const
        DB_NAME = 'employeur';

    private
        $contactRepository,
        $employeRepository;

    public function __construct(Connection $db, Repositories\Contact $contactRepository, Repositories\Employe $employeRepository)
    {
        parent::__construct($db);

        $this->contactRepository = $contactRepository;
        $this->employeRepository = $employeRepository;
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

    private function getBaseQuery()
    {
        $query = (new Queries\Select())->setEscaper(new SimpleEscaper())
            ->select(array('id', 'paje_emploi_id', 'contact_id'))
            ->from(self::DB_NAME);

        return $query;
    }

    public function getFields()
    {
        return array(
            'id' => new Fields\NotNullable(new Fields\UnsignedInteger('id')),
            'pajeEmploiId' => new Fields\NotNullable(new Fields\UnsignedInteger('paje_emploi_id')),
            'contactId' => new Fields\NotNullable(new Fields\UnsignedInteger('contact_id')),
        );
    }

    public function getDomain(DataTransferObject $dto)
    {
        $dto->set('contact', function() use($dto) {
            return $this->contactRepository->find($dto->contactId);
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
