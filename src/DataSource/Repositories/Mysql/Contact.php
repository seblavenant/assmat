<?php

namespace Assmat\DataSource\Repositories\Mysql;

use Assmat\DataSource\Domains;
use Assmat\DataSource\Entities;
use Assmat\DataSource\Repositories;

class Contact implements Repositories\Contact
{
	public function findByEmployeur(Domains\Employeur $employeur)
	{
		$contactEntity = new Entities\Contact();
		$contactEntity->id = 42;
		$contactEntity->nom = 'lavenant';
		
		return new Domains\Contact($contactEntity);
	}
}