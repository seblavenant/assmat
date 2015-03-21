<?php

namespace Assmat\DataSource\Repositories;

use Assmat\DataSource\Domains;

interface Contact
{
	public function findByEmployeur(Domains\Employeur $employeur);
}