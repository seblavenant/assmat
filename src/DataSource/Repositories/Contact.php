<?php

namespace Assmat\DataSource\Repositories;

use Assmat\DataSource\Domains;

interface Contact
{
	public function findFromEmployeur(Domains\Employeur $employeur);
}