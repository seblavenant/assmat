<?php

namespace Assmat\Services\Bulletin;

require_once(__DIR__ . '/BuilderValidator.php');

use Assmat\DataSource\Domains;
use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\Services\Lignes;
use Assmat\DataSource\Repositories;
use Assmat\DataSource\Constants;
use Assmat\DataSource\Constants\Lignes\Code;
use Assmat\Services\Bulletin;

class BuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $month = '01';
        $year = '2015';

        $contratDTO = new DTO\Contrat();
        $contratDTO->salaireHoraire = 10;
        $contratDTO->heuresHebdo = 30;
        $contratDTO->joursGarde = 4;
        $contrat = new Domains\Contrat($contratDTO);

        $evenementAccueil = new DTO\Evenement();
        $evenementAccueil->date = new \DateTime($year . '-' . $month . '-01');
        $evenementAccueil->heureDebut = new \DateTime($year . '-' . $month . '-01 08:00');
        $evenementAccueil->heureFin = new \DateTime($year . '-' . $month . '-01 16:30');
        $evenementAccueil->typeId = Constants\Evenements\Type::GARDE;

        $evenementCP = new DTO\Evenement();
        $evenementCP->date = new \DateTime($year . '-' . $month . '-02');
        $evenementCP->typeId = Constants\Evenements\Type::CONGE_PAYE;

        $evenements = array(
            new Domains\Evenement($evenementAccueil),
            new Domains\Evenement($evenementCP),
        );

        $bulletinBuilder = new Bulletin\Builder(new Repositories\Memory\Ligne\Template());
        $bulletin = $bulletinBuilder->build($contrat, $evenements);

        $builderValidator = new BuilderValidator($bulletin);
        $builderValidator->assertCgsRds(4.56);
//         $builderValidator->assertCgsDeductible(8.00);
        $builderValidator->assertSalaire(16, 160);
    }

}