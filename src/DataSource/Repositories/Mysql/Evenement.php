<?php

namespace Assmat\DataSource\Repositories\Mysql;

use Assmat\DataSource\Domains;
use Assmat\DataSource\DataTransferObjects;
use Assmat\DataSource\Repositories;

use Muffin\Queries;
use Muffin\Types;
use Muffin\Tests\Escapers\SimpleEscaper;
use Muffin\Queries\Snippets\OrderBy;
use Spear\Silex\Persistence\Fields;
use Spear\Silex\Persistence\DataTransferObject as DTO;
use Doctrine\DBAL\Driver\Connection;
use Muffin\QueryBuilder;
use Muffin\Muffin;

class Evenement extends AbstractMysql implements Repositories\Evenement
{
    const
        TABLE_NAME = 'evenement';

    public function find($id)
    {
        $query = $this->getBaseQuery();
        $query->where((new Types\Integer('id'))->equal($id));

        return $this->fetchOne($query);
    }

    public function findFromContrat($contratId)
    {
        $query = $this->getBaseQuery();
        $query->where((new Types\Integer('contrat_id'))->equal($contratId));

        return $this->fetchAll($query);
    }

    public function findFromDate($date)
    {
        $query = $this->getBaseQuery();
        $query->where((new Types\Datetime('date'))->equal($date));

        return $this->fetchOne($query);
    }

    public function persist(DTO $evenementDTO)
    {
        $this->db->executeQuery('
            INSERT INTO evenement (date, heure_debut, heure_fin, type, contrat_id)
            VALUES (:date, :heureDebut, :heureFin, :type, :contratId)
            ON DUPLICATE KEY UPDATE
            date = :date, heure_debut = :heureDebut, heure_fin = :heureFin, type = :type, contrat_id = :contratId
            ',
            array(
                'date' => $evenementDTO->date,
                'heureDebut' => $evenementDTO->heureDebut,
                'heureFin' => $evenementDTO->heureFin,
                'type' => $evenementDTO->type,
                'contratId' => $evenementDTO->contratId,
            ),
            array(
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
                \PDO::PARAM_STR,
                \PDO::PARAM_INT,
                \PDO::PARAM_INT,
            )
        );
    }

    public function delete($id)
    {
        $this->db->delete(self::TABLE_NAME, array('id' => $id));
    }

    private function getBaseQuery()
    {
        $query = (new Queries\Select())->setEscaper(new SimpleEscaper())
            ->select(array('id', 'date', 'heure_debut', 'heure_fin', 'type'))
            ->from(self::TABLE_NAME)
            ->orderBy('date', OrderBy::DESC)
            ->orderBy('heure_debut', OrderBy::DESC);

        return $query;
    }

    public function getFields()
    {
        return array(
            'id' => new Fields\NotNullable(new Fields\UnsignedInteger('id')),
            'date' => new Fields\NotNullable(new Fields\DateTime('date', 'Y-m-d')),
            'heureDebut' => new Fields\NotNullable(new Fields\DateTime('heure_debut', 'H:i:s')),
            'heureFin' => new Fields\NotNullable(new Fields\DateTime('heure_fin', 'H:i:s')),
            'type' => new Fields\NotNullable(new Fields\Integer('type')),
        );
    }

    public function getDomain(DTO $dto)
    {
        return new Domains\Evenement($dto);
    }

    public function getDTO()
    {
        return new DataTransferObjects\Evenement();
    }
}