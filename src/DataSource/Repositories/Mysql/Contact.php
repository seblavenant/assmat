<?php

namespace Assmat\DataSource\Repositories\Mysql;

use Assmat\DataSource\Domains;
use Assmat\DataSource\Entities;
use Assmat\DataSource\Repositories;

class Contact implements Repositories\Contact
{
	public function findFromEmployeur(Domains\Employeur $employeur)
	{
		$contactEntity = new Entities\Contact();
		$employeurId = $employeur->getId();
		
		if($employeurId !== null)
		{
			$contactEntity->id = 42;
			$contactEntity->nom = 'lavenant';
		}

		return $contactEntity;
	}
}