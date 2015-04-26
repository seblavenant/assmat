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

        $evenementAccueil = new DTO\Evenement();
        $evenementAccueil->id = 1;
        $evenementAccueil->date = new \DateTime($year . '-' . $month . '-01');
        $evenementAccueil->heureDebut = '08:00';
        $evenementAccueil->heureFin = '16:00';
        $evenementAccueil->type = 1;
        $evenementAccueil->contratId = 42;

        $evenementCP = new DTO\Evenement();
        $evenementCP->id = 2;
        $evenementCP->date = new \DateTime($year . '-' . $month . '-02');
        $evenementCP->type = 2;
        $evenementCP->contratId = 42;

        $bulletinDTO = new DTO\Bulletin();
        $bulletinDTO->set('evenements', array(
            $evenementAccueil,
            $evenementCP,
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
            42,
            $lignes[Constants\Lignes\Code::CSG_RDS]->getValeur($bulletin),
            '#' . Constants\Lignes\Code::CSG_RDS . ' #getQuantite'
        );

        $this->assertArrayHasKey(Constants\Lignes\Code::SALAIRE, $lignes);
        $this->assertEquals(
            12,
            $lignes[Constants\Lignes\Code::SALAIRE]->getQuantite($bulletin),
            '#' . Constants\Lignes\Code::SALAIRE . ' #getQuantite'
        );
        $this->assertEquals(
            1000,
            $lignes[Constants\Lignes\Code::SALAIRE]->getValeur($bulletin),
            '#' . Constants\Lignes\Code::SALAIRE . ' getValeur'
        );
    }

}