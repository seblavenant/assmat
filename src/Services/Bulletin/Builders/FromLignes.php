<?php

namespace Assmat\Services\Bulletin\Builders;

use Assmat\DataSource\Domains;
use Assmat\DataSource\Repositories;
use Assmat\Services\Providers\ServiceProvider;

class FromLignes
{
    private
        $ligneRepository,
        $ligneBuilderProvider;
    
    public function __construct(Repositories\Ligne $ligneRepository, ServiceProvider $ligneBuilderProvider)
    {
        $this->ligneRepository = $ligneRepository;
        $this->ligneBuilderProvider = $ligneBuilderProvider;
    }
    
    public function build(Domains\Bulletin $bulletin, array $lignesData)
    {
        $lignes = $bulletin->getLignes();

        foreach($lignes as $ligne)
        {
            $ligneId = $ligne->getTypeId();

            if(isset($lignesData[$ligneId]['base']))
            {
                $ligne->setBase($lignesData[$ligneId]['base']);
            }

            if(isset($lignesData[$ligneId]['quantite']))
            {
                $ligne->setQuantite($lignesData[$ligneId]['quantite']);
            }

            $ligneBuilder = $this->ligneBuilderProvider->get($ligne->getTypeId());
            $ligneBuilder()->compute($ligne, $bulletin);

            $ligne->persist($this->ligneRepository);
            $bulletin->refreshLignes($this->ligneRepository);
        }

        return $bulletin;
    }
}
