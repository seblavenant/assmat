<?php

namespace Assmat\DataSource\Domains;

use Assmat\DataSource\Repositories;
use Assmat\DataSource\Entities;
use Assmat\DataSource\Domains;

class Employeur
{
	private
		$fields,
		$contactRepository;
	
	public function __construct(Entities\Employeur $fields, Repositories\Contact $contactRepository)
	{
		$this->fields = $fields;
		$this->contactRepository = $contactRepository;
	}
	
	public function getPageEmploiId()
	{
		return $this->fields->pajeEmploiId;
	}
	
	public function getContact()
	{
		if(! $this->fields->contact instanceof Domains\Contact)
		{
			$this->fields->contact = $this->contactRepository->findByEmployeur($this);
		}
		
		return $this->fields->contact;
	}
}