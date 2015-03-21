<?php

namespace Assmat\DataSource\Repositories\Mysql;

use Assmat\DataSource\Entities;
use Assmat\DataSource\Domains;
use Assmat\DataSource\Repositories;

class Employeur implements Repositories\Employeur
{
	private
		$contactRepository;
	
	public function __construct(Repositories\Contact $contactRepository)
	{
		$this->contactRepository = $contactRepository;
	}
	
	public function findById($id)
	{
		$employeurEntity = new Entities\Employeur();
		$employeurEntity->pajeEmploiId = 42;
		
		return new Domains\Employeur($employeurEntity, $this->contactRepository);
	}
}
