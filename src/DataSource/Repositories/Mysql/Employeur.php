<?php

namespace Assmat\DataSource\Repositories\Mysql;

use Assmat\DataSource\Entities;
use Assmat\DataSource\Domains;
use Assmat\DataSource\Repositories;

class Employeur implements Repositories\Employeur
{
	public function findFromId($id)
	{
		$employeurEntity = new Entities\Employeur();
		$employeurEntity->pajeEmploiId = 42;
		
		return $employeurEntity;
	}
}
