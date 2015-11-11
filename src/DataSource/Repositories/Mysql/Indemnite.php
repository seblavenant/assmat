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

class Indemnite extends AbstractMysql implements Repositories\Indemnite
{
    const
        TABLE_NAME = 'indemnite';

    public function find($id)
    {
        $query = $this->getBaseQuery();
        $query->where((new Types\Integer('id'))->equal($id));

        return $this->fetchOne($query);
    }

    public function findFromContrat($contratId)
    {
        $query = $this->getBaseQuery();
        $query->where((new Types\String('contrat_id'))->equal($contratId));

        return $this->fetchAll($query);
    }

    public function persist(DTO\Indemnite $indemniteDTO)
    {
        if($indemniteDTO->id !== null)
        {
            return $this->update($indemniteDTO);
        }

        return $this->create($indemniteDTO);
    }

    private function create(DTO\Indemnite $indemniteDTO)
    {
        $this->db->insert(
            self::TABLE_NAME,
            array(
                'type_id' => $indemniteDTO->typeId,
                'montant' => $indemniteDTO->montant,
                'contrat_id' => $indemniteDTO->contratId,
            ),
            array(
                \PDO::PARAM_INT,
                \PDO::PARAM_STR,
                \PDO::PARAM_INT,
            )
        );

        $indemniteDTO->id = (int) $this->db->lastInsertId();

        return new Domains\Indemnite($indemniteDTO);
    }

    private function update(DTO\Indemnite $indemniteDTO)
    {

    }

    private function getBaseQuery()
    {
        $query = (new Queries\Select())->setEscaper(new SimpleEscaper())
            ->select(array('id', 'montant', 'type_id', 'contrat_id'))
            ->from(self::TABLE_NAME);

        return $query;
    }

    public function getFields()
    {
        return array(
            'id' => new Fields\NotNullable(new Fields\Integer('id')),
            'montant' => new Fields\NotNullable(new Fields\Float('montant')),
            'typeId' => new Fields\NotNullable(new Fields\Integer('type_id')),
            'contratId' => new Fields\NotNullable(new Fields\Integer('contrat_id')),
        );
    }

    public function getDomain(DataTransferObject $dto)
    {
        return new Domains\Indemnite($dto);
    }

    public function getDTO()
    {
        return new DTO\Indemnite();
    }
}