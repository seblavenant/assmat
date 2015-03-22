<?php

namespace Assmat\DataSource\Domains;

use Assmat\DataSource\Repositories;
use Assmat\DataSource\Entities;
use Assmat\DataSource\Domains;

class Employeur
{
	private
		$fields,
		$employeurRepository,
		$contactRepository;
	
	public function __construct( Repositories\Employeur $employeurRepository, Repositories\Contact $contactRepository)
	{
		$this->employeurRepository = $employeurRepository;
		$this->contactRepository = $contactRepository;
		$this->fields = new Entities\Employeur();
	}
	
	public function loadFromId($id)
	{
		$this->fields = $this->employeurRepository->findFromId($id);
		
		return $this;
	}
	
	public function getId()
	{
		return $this->fields->id;
	}
	
	public function getPageEmploiId()
	{
		return $this->fields->pajeEmploiId;
	}
	
	public function getContact()
	{
		if(! $this->fields->contact instanceof Domains\Contact)
		{
			$this->fields->contact = (new Domains\Contact($this->contactRepository))->loadFromEmployeur($this);
		}
		
		return $this->fields->contact;
	}
}