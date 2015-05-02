<?php

namespace Assmat\Services\Bulletin;

use Assmat\DataSource\Constants;
use Assmat\DataSource\Domains\Bulletin;

class BuilderValidator extends \PHPUnit_Framework_TestCase
{
    private
        $bulletin;

    public function __construct(Bulletin $bulletin)
    {
        $this->bulletin = $bulletin;
    }

    public function assertCgsRds($expected)
    {
        $this->assertCgs(Constants\Lignes\Type::CSG_RDS, $expected);
    }

    public function assertCgsDeductible($expected)
    {
        $this->assertCgs(Constants\Lignes\Type::CSG_DECUCTIBLE, $expected);
    }

    private function assertCgs($code, $expected)
    {
        $lignes = $this->bulletin->getLignes();
        $this->assertArrayHasKey($code, $lignes);
        $this->assertEquals(
            $expected,
            $lignes[$code]->getValeur($this->bulletin),
            '#' . $code . ' #getValeur'
        );
    }

    public function assertSalaire($quantiteExpected, $valeurExpected)
    {
        $lignes = $this->bulletin->getLignes();
        $this->assertArrayHasKey(Constants\Lignes\Type::SALAIRE, $lignes);
        $this->assertEquals(
            $quantiteExpected,
            $lignes[Constants\Lignes\Type::SALAIRE]->getQuantite($this->bulletin),
            '#' . Constants\Lignes\Type::SALAIRE . ' #getQuantite'
        );
        $this->assertEquals(
            $valeurExpected,
            $lignes[Constants\Lignes\Type::SALAIRE]->getValeur($this->bulletin),
            '#' . Constants\Lignes\Type::SALAIRE . ' getValeur'
        );
    }
}