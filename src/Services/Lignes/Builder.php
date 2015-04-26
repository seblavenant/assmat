<?php

namespace Assmat\Services\Lignes;

use Assmat\DataSource\Domains;
use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Repositories;

class Builder
{
    private
        $lignes,
        $ligneRepository;

    public function __construct(Repositories\Ligne $ligneRepository)
    {
        $this->ligneRepository = $ligneRepository;
    }

    public function build(array $codes, Domains\Bulletin $bulletin)
    {
        $lignes = $this->ligneRepository->findFromCodes($codes);

        if(empty($lignes))
        {
            return;
        }

        foreach($lignes as $ligne)
        {
            $ligne->getValeur($bulletin);
        }

        return $lignes;
    }
}