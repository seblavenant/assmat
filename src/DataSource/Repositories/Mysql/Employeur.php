<?php

namespace Assmat\DataSource\Repositories\Mysql;

use Assmat\DataSource\Entities;
use Assmat\DataSource\Domains;
use Assmat\DataSource\Repositories;

use Muffin\Queries;
use Muffin\Conditions;
use Muffin\Types;
use Muffin\Tests\Escapers\SimpleEscaper;

class Employeur implements Repositories\Employeur
{
    const
        DB_NAME = 'employeur';

    private
        $employeurEntity,
        $pdo;
    
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->employeurEntity = new Entities\Employeur();
    }

    public function findFromId($id)
    {
        $queryBuilder = (new Queries\Select())->setEscaper(new SimpleEscaper())
            ->select(array('id', 'paje_emploi_id'))
            ->from(self::DB_NAME)
            ->where((new Types\Integer('id'))->equal($id));

        $statement = $this->pdo->query($queryBuilder->toString());

        if(! $statement instanceof \PDOStatement)
        {
            return $this->employeurEntity;
        }

        $employeur = $statement->fetchObject();
        if($employeur === false)
        {
            return $this->employeurEntity;
        }

        $this->employeurEntity->id = $employeur->id;
        $this->employeurEntity->pajeEmploiId = $employeur->paje_emploi_id;

        return $this->employeurEntity;
    }
}
