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
        $query = (new Queries\Select())->setEscaper(new SimpleEscaper())
            ->select(array('c.id', 'c.nom', 'c.prenom', 'c.adresse', 'c.code_postal', 'c.ville'))
            ->from(self::DB_NAME, 'c')
            ->leftJoin(Repositories\Mysql\Employeur::DB_NAME, 'e')->on('e.contact_id', 'c.id')
            ->where((new Types\Integer('e.contact_id'))->equal($id));

        return $this->fetchOne($query);
    }

    public function getFields()
    {
        return array(
            'id' => new Fields\NotNullable(new Fields\UnsignedInteger('id')),
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