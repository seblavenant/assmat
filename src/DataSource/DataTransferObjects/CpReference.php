<?php

namespace Assmat\DataSource\DataTransferObjects;

use Spear\Silex\Persistence\DataTransferObjects\Related;

class CpReference extends Related
{
    public
        $id,
        $annee,
        $tauxJournalier,
        $nbJours,
        $contratId;

    public function __construct()
    {
        parent::__construct([
            'details'
        ]);
    }
}
