<?php

namespace Assmat\Services\Lignes;

use Assmat\DataSource\Domains;
use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\Services\Lignes;
use Assmat\DataSource\Repositories;
use Assmat\DataSource\Constants;
use Assmat\DataSource\Constants\Lignes\Code;

class BuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $month = '01';
        $year = '2015';

        $contrat = new DTO\Contrat();
        $contrat->salaireHoraire = 10;
        $contrat->heuresHebdo = 30;
        $contrat->joursGarde = 4;

        $evenementAccueil = new DTO\Evenement();
        $evenementAccueil->date = new \DateTime($year . '-' . $month . '-01');
        $evenementAccueil->heureDebut = new \DateTime($year . '-' . $month . '-01 08:00');
        $evenementAccueil->heureFin = new \DateTime($year . '-' . $month . '-01 16:30');
        $evenementAccueil->typeId = Constants\Evenements\Type::ACCUEIL;

        $evenementCP = new DTO\Evenement();
        $evenementCP->date = new \DateTime($year . '-' . $month . '-02');
        $evenementCP->typeId = Constants\Evenements\Type::CONGE_PAYE;

        $bulletinDTO = new DTO\Bulletin();
        $bulletinDTO->set('contrat', new Domains\Contrat($contrat));
        $bulletinDTO->set('evenements', array(
            new Domains\Evenement($evenementAccueil),
            new Domains\Evenement($evenementCP),
        ));
        $bulletin = new Domains\Bulletin($bulletinDTO);

        $codes = array(
            Code::SALAIRE,
            Code::CSG_RDS,
        );

        $lignesBuilder = new Lignes\Builder(new Repositories\Memory\Ligne());
        $lignes = $lignesBuilder->build($codes, $bulletin);

        $lignes = iterator_to_array($lignes);

        $this->assertArrayHasKey(Constants\Lignes\Code::CSG_RDS, $lignes);
        $this->assertEquals(
            4.55,
            $lignes[Constants\Lignes\Code::CSG_RDS]->getValeur($bulletin),
            '#' . Constants\Lignes\Code::CSG_RDS . ' #getValeur'
        );

        $this->assertArrayHasKey(Constants\Lignes\Code::SALAIRE, $lignes);
        $this->assertEquals(
            16,
            $lignes[Constants\Lignes\Code::SALAIRE]->getQuantite($bulletin),
            '#' . Constants\Lignes\Code::SALAIRE . ' #getQuantite'
        );
        $this->assertEquals(
            160,
            $lignes[Constants\Lignes\Code::SALAIRE]->getValeur($bulletin),
            '#' . Constants\Lignes\Code::SALAIRE . ' getValeur'
        );
    }

}