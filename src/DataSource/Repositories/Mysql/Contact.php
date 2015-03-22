<?php

namespace Assmat\DataSource\Repositories\Mysql;

use Assmat\DataSource\Domains;
use Assmat\DataSource\Entities;
use Assmat\DataSource\Repositories;

use Muffin\Queries;
use Muffin\Conditions;
use Muffin\Types;
use Muffin\Tests\Escapers\SimpleEscaper;

class Contact implements Repositories\Contact
{
	const
		DB_NAME = 'contact';
	
	private 
		$contactEntity,
		$pdo;
	
	public function __construct(\PDO $pdo)
	{
		$this->pdo = $pdo;
		$this->contactEntity = new Entities\Contact();
	}
	
	public function findFromEmployeur(Domains\Employeur $employeur)
	{
		$employeurId = $employeur->getId();
		
		if($employeurId === null)
		{
			return $this->contactEntity;	
		}

		$queryBuilder = (new Queries\Select())->setEscaper(new SimpleEscaper())
			->select(array('c.id', 'c.nom', 'c.prenom', 'c.adresse', 'c.code_postal', 'c.ville'))
			->from(self::DB_NAME, 'c')
			->leftJoin(Repositories\Mysql\Employeur::DB_NAME, 'e')->on('e.contact_id', 'c.id')
			->where((new Types\Integer('e.contact_id'))->equal($employeurId));

		$statement = $this->pdo->query($queryBuilder->toString());

		if(! $statement instanceof \PDOStatement)
		{
			return $this->contactEntity;	
		}

		$contact = $statement->fetchObject();
		if($contact === false)
		{
			return $this->contactEntity;
		}
		
		$this->contactEntity->id = $contact->id;
		$this->contactEntity->nom = $contact->nom;
		$this->contactEntity->prenom = $contact->prenom;
		$this->contactEntity->adresse = $contact->adresse;
		$this->contactEntity->codePostal = $contact->code_postal;
		$this->contactEntity->ville = $contact->ville;

		return $this->contactEntity;
	}
}