<?php

namespace Assmat\DataSource\Domains;

use Assmat\DataSource\DataTransferObjects as DTO;

class Contact
{
    private
    	$fields;

    public function __construct(DTO\Contact $contactDTO)
    {
        $this->fields = $contactDTO;
    }
    
	public function getId()
	{
		return $this->fields->id;
	}
}