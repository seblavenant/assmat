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
use Doctrine\DBAL\Driver\Connection;

class Bulletin extends AbstractMysql implements Repositories\Bulletin
{
    const
        DB_NAME = 'bulletin';

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
            ->select(array('id', 'date_debut', 'date_fin'))
            ->from(self::DB_NAME);

        return $query;
    }

    public function getFields()
    {
        return array(
            'id' => new Fields\NotNullable(new Fields\UnsignedInteger('id')),
            'dateDebut' => new Fields\NotNullable(new Fields\DateTime('date_debut', 'Y-m-d')),
            'dateFin' => new Fields\NotNullable(new Fields\DateTime('date_fin', 'Y-m-d')),
        );
    }

    public function getDomain(DataTransferObject $dto)
    {
        return new Domains\Bulletin($dto);
    }

    public function getDTO()
    {
        return new DTO\Bulletin();
    }
}