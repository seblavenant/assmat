<?php

namespace Assmat\DataSource\Domains;

use Assmat\DataSource\Entities;
use Assmat\DataSource\Repositories;
use Assmat\DataSource\Domains;

class Contact
{
	private
		$contactRepository,
		$employeur,
		$fields;

	public function __construct(Repositories\Contact $contactRepository)
	{
		$this->contactRepository = $contactRepository;
	}
	
	public function loadFromEmployeur(Domains\Employeur $employeur)
	{
		$this->fields = $this->contactRepository->findFromEmployeur($employeur);
		
		return $this;
	}
	
	public function getNom()
	{
		return $this->fields->nom;
	}
}