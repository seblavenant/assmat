<?php

namespace Assmat\DataSource\Domains;

use Spear\Silex\Persistence\DataTransferObjects\Related;
use Assmat\DataSource\DataTransferObjects as DTO;

class Employeur
{
    private
        $fields;

    public function __construct(DTO\Employeur $employeurDTO)
    {
		$this->fields = $employeurDTO;
    }
    
    public function getId()
    {
    	return $this->fields->id;
    }
    
    public function getContact()
    {
    	return $this->fields->load('contact');
    }
}