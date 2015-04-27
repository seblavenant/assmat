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

class Contact extends AbstractMysql implements Repositories\Contact
{
    const
        DB_NAME = 'contact';

    public function find($id)
    {
        $query = $this->getBaseQuery();
        $query->where((new Types\Integer('id'))->equal($id));

        return $this->fetchOne($query);
    }

    public function findFromEmail($email)
    {
        $query = $this->getBaseQuery();
        $query->where((new Types\String('email'))->equal($email));

        return $this->fetchOne($query);
    }

    private function getBaseQuery()
    {
        $query = (new Queries\Select())->setEscaper(new SimpleEscaper())
            ->select(array('id', 'email', 'password', 'nom', 'prenom', 'adresse', 'code_postal', 'ville'))
            ->from(self::DB_NAME);

        return $query;
    }

    public function getFields()
    {
        return array(
            'id' => new Fields\NotNullable(new Fields\UnsignedInteger('id')),
            'email' => new Fields\NotNullable(new Fields\String('email')),
            'password' => new Fields\NotNullable(new Fields\String('password')),
            'nom' => new Fields\NotNullable(new Fields\String('nom')),
            'prenom' => new Fields\NotNullable(new Fields\String('prenom')),
            'adresse' => new Fields\NotNullable(new Fields\String('adresse')),
            'codePostal' => new Fields\NotNullable(new Fields\String('code_postal')),
            'ville' => new Fields\NotNullable(new Fields\String('ville')),
        );
    }

    public function getDomain(DataTransferObject $dto)
    {
        return new Domains\Contact($dto);
    }

    public function getDTO()
    {
        return new DTO\Contact();
    }
}