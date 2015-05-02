<?php

namespace Assmat\DataSource\Repositories\Memory;

use Assmat\DataSource\Domains;
use Assmat\DataSource\Repositories;
use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Constants;
use Assmat\Iterators;

class Ligne implements Repositories\Ligne
{
    private
        $lignes;

    public function __construct()
    {
        $this->lignes = array(
            Constants\Lignes\Code::SALAIRE => (new Lignes\Salaire())->getDomain(),
            Constants\Lignes\Code::CSG_RDS => (new Lignes\CsgRds())->getDomain(),
        );
    }

    public function find($code)
    {
        if(array_key_exists($code, $this->lignes))
        {
            return $this->lignes[$code];
        }
    }

    public function findFromCodes(array $codes)
    {
        return new Iterators\Filters\Lignes\Code(new \ArrayIterator($this->lignes), $codes);
    }
}