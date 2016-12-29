<?php

namespace Assmat\Services\Bulletin\Builder\FromEvenements;

require_once(__DIR__ . '/../../BuilderHelper.php');
require_once(__DIR__ . '/../../BuilderValidator.php');

use Assmat\Services\Bulletin\BuilderValidator;
use Assmat\Services\Bulletin\BuilderHelper;
use Assmat\DataSource\Domains;
use Assmat\DataSource\Repositories;
use Assmat\DataSource\Constants;
use Assmat\Services\Bulletin;
use Assmat\Services\Providers;

class CotisationsTest extends \PHPUnit_Framework_TestCase
{
    public function testCotisationsBuild()
    {
        $contrat = new Domains\Contrat((new BuilderHelper())->getBaseContratDTO());

        $evenements = array(
            (new BuilderHelper())->getEvenementGarde()
        );

        $bulletinBuilder = new Bulletin\Builders\FromEvenements(new Repositories\Memory\Ligne\Template(), new Repositories\Memory\CongePaye(), new Providers\LigneBuilder());
        $bulletin = $bulletinBuilder->build($contrat, $evenements, 2015, 01);

        $builderValidator = new BuilderValidator($bulletin);
        $builderValidator->assertCotisation(Constants\Lignes\Type::CSG_RDS, 2.42);
        $builderValidator->assertCotisation(Constants\Lignes\Type::CSG_DEDUCTIBLE, 4.26);
        $builderValidator->assertCotisation(Constants\Lignes\Type::SECURITE_SOCIALE, 6.72);
        $builderValidator->assertCotisation(Constants\Lignes\Type::RETRAITE_COMPLEMENTAIRE, 2.64);
        $builderValidator->assertCotisation(Constants\Lignes\Type::PREVOYANCE, 0.98);
        $builderValidator->assertCotisation(Constants\Lignes\Type::AGFF, 0.68);
        $builderValidator->assertCotisation(Constants\Lignes\Type::ASSURANCE_CHOMAGE, 2.04);
    }
}
