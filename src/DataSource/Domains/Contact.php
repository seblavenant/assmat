<?php

namespace Assmat\DataSource\Domains;

use Assmat\DataSource\Entities;

class Contact
{
	private
		$fields;
	
	public function __construct(Entities\Contact $contactEntity)
	{
		$this->fields = $contactEntity;
	}
	
	public function getNom()
	{
		return $this->fields->nom;
	}
}