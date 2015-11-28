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
        $query = (new Queries\Select())->setEscaper(new SimpleEscaper())
            ->select($this->prefixTableFields(array('id', 'ss_id', 'contact_id')))
            ->from('contrat')
            ->leftJoin('employe')->on('employe.id', 'contrat.employe_id')
            ->leftJoin('contact')->on('contact.id', 'employe.contact_id')
            ->groupBy('employe.id')
            ->where((new Types\Integer('contrat.employeur_id'))->equal($employeurId));

        return $this->fetchAll($query);
    }

    public function findFromKey($key)
    {
        $query = $this->getBaseQuery();
        $query
            ->leftJoin('contact')->on('contact_id', 'contact.id')
            ->where((new Types\String('contact.key'))->equal($key));

        return $this->fetchOne($query);
    }

    public function persist(DTO\Employe $employeDTO)
    {
        if($employeDTO->id !== null)
        {
            return $this->update($employeDTO);
        }

        return $this->create($employeDTO);
    }

    private function update(DTO\Employe $employeDTO)
    {
        $this->db->update(
            self::TABLE_NAME,
            array(
                'ss_id' => $employeDTO->ssId,
            ),
            array(
                'id' => $employeDTO->id,
            ),
            array(
                \PDO::PARAM_STR,
            )
        );

        return new Domains\Employe($employeDTO);
    }

    private function create(DTO\Employe $employeDTO)
    {
        $this->db->insert(
            self::TABLE_NAME,
            array(
                'ss_id' => $employeDTO->ssId,
                'contact_id' => $employeDTO->contactId,
            ),
            array(
                \PDO::PARAM_STR,
            )
        );

        $employeDTO->id = (int) $this->db->lastInsertId();

        return new Domains\Employe($employeDTO);
    }

    private function getBaseQuery()
    {
        $query = (new Queries\Select())->setEscaper(new SimpleEscaper())
            ->select($this->prefixTableFields(array('id', 'ss_id', 'contact_id')))
            ->from(self::TABLE_NAME);

        return $query;
    }

    public function getFields()
    {
        return array(
            'id' => new Fields\NotNullable(new Fields\UnsignedInteger('id')),
            'ssId' => new Fields\String('ss_id'),
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