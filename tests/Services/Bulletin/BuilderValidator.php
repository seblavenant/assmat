<?php

namespace Assmat\Services\Bulletin;

use Assmat\DataSource\Constants;
use Assmat\DataSource\Domains\Bulletin;

class BuilderValidator extends \PHPUnit_Framework_TestCase
{
    private
        $bulletin,
        $lignes;

    public function __construct(Bulletin $bulletin)
    {
        $this->bulletin = $bulletin;
        $this->lignes = $bulletin->getLignes();
    }

    public function assertCotisation($code, $expected)
    {
        $this->assertArrayHasKey($code, $this->lignes);
        $this->assertEquals(
            $expected,
            $this->lignes[$code]->getValeur($this->bulletin),
            '#' . $code . ' #getValeur'
        );
    }

    public function assertIndemnites($code, $quantiteExpected, $valeurExpected)
    {
        $this->assertArrayHasKey($code, $this->lignes);
        $this->assertEquals(
            $quantiteExpected,
            $this->lignes[$code]->getQuantite($this->bulletin),
            '#' . $code . ' #getQuantite'
        );
        $this->assertEquals(
            $valeurExpected,
            $this->lignes[$code]->getValeur($this->bulletin),
            '#' . $code . ' #getValeur'
        );
    }

    public function assertSalaire($quantiteExpected, $salaireBrut, $salaireNet)
    {
        $this->assertArrayHasKey(Constants\Lignes\Type::SALAIRE, $this->lignes);
        $this->assertEquals(
            $quantiteExpected,
            $this->lignes[Constants\Lignes\Type::SALAIRE]->getQuantite($this->bulletin),
            '#' . Constants\Lignes\Type::SALAIRE . ' #getQuantite'
        );
        $this->assertEquals(
            $salaireBrut,
            $this->lignes[Constants\Lignes\Type::SALAIRE]->getValeur($this->bulletin),
            '#' . Constants\Lignes\Type::SALAIRE . ' #getValeur'
        );

        $this->assertEquals($this->bulletin->getSalaireBrut(), $salaireBrut);
        $this->assertEquals($this->bulletin->getSalaireNet(), $salaireNet);
    }
}