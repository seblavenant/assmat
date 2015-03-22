<?php

namespace Assmat\DataSource\Repositories\Mysql;

use Assmat\DataSource\Domains;
use Assmat\DataSource\Entities;
use Assmat\DataSource\Repositories;

class Contact implements Repositories\Contact
{
	public function findFromEmployeur(Domains\Employeur $employeur)
	{
		$employeurId = $employeur->getId();
		
		$contactEntity = new Entities\Contact();
		$contactEntity->id = 42;
		$contactEntity->nom = 'lavenant';
		
		return $contactEntity;
	}
}