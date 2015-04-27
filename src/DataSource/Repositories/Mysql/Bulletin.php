<?php

namespace Assmat\DataSource\Repositories\Mysql;

use Assmat\DataSource\Domains;
use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Repositories;
use Assmat\Services\Evenements;
use Muffin\Queries;
use Muffin\Types;
use Muffin\Tests\Escapers\SimpleEscaper;
use Muffin\Queries\Snippets\OrderBy;
use Spear\Silex\Persistence\Fields;
use Spear\Silex\Persistence\DataTransferObject;
use Doctrine\DBAL\Driver\Connection;

class Bulletin extends AbstractMysql implements Repositories\Bulletin
{
    const
        DB_NAME = 'bulletin';

    private
        $evenementRepository;

    public function __construct(Connection $db, Repositories\Evenement $evenementRepository)
    {
        parent::__construct($db);

        $this->evenementRepository = $evenementRepository;
    }

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
            ->select(array('id', 'mois', 'annee'))
            ->from(self::DB_NAME)
            ->orderBy('annee', OrderBy::DESC)
            ->orderBy('mois', OrderBy::DESC);

        return $query;
    }

    public function getFields()
    {
        return array(
            'id' => new Fields\NotNullable(new Fields\UnsignedInteger('id')),
            'mois' => new Fields\NotNullable(new Fields\Integer('mois')),
            'annee' => new Fields\NotNullable(new Fields\Integer('annee')),
        );
    }

    public function getDomain(DataTransferObject $dto)
    {
        $dto->set('evenements', function() use($dto) {
            return $this->evenementRepository->findAllFromContrat($dto->contratId, new Evenements\Periods\Month(new \DateTime($dto->anne . '-' . $dto->mois)));
        });

        $dto->set('lignes', function() use($dto) {

        });

        return new Domains\Bulletin($dto);
    }

    public function getDTO()
    {
        return new DTO\Bulletin();
    }
}