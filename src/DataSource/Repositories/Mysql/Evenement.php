<?php

namespace Assmat\DataSource\Repositories\Mysql;

use Assmat\DataSource\Domains;
use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Repositories;

use Muffin\Queries;
use Muffin\Types;
use Muffin\Tests\Escapers\SimpleEscaper;
use Muffin\Queries\Snippets\OrderBy;
use Spear\Silex\Persistence\Fields;
use Spear\Silex\Persistence\DataTransferObject;
use Doctrine\DBAL\Driver\Connection;

class Evenement extends AbstractMysql implements Repositories\Evenement
{
    const
        DB_NAME = 'evenement';

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

    private function getBaseQuery()
    {
        $query = (new Queries\Select())->setEscaper(new SimpleEscaper())
            ->select(array('id', 'date', 'heure_debut', 'heure_fin', 'type'))
            ->from(self::DB_NAME)
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

    public function getDomain(DataTransferObject $dto)
    {
        return new Domains\Evenement($dto);
    }

    public function getDTO()
    {
        return new DTO\Evenement();
    }
}