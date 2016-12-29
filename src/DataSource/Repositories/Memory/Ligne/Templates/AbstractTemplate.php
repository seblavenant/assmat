<?php

namespace Assmat\DataSource\Repositories\Memory\Ligne\Templates;

use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Domains;

abstract class AbstractTemplate
{
    protected
        $ligneDTO;
    
    public function __construct()
    {
        $this->ligneDTO = new DTO\Ligne();
        
        $this->ligneDTO->baseEditable = false;
        $this->ligneDTO->quantiteEditable = false;
        $this->ligneDTO->taux = false;
    }
        
    public function setBase($base)
    {
        if(! $this->ligneDTO->baseEditable)
        {
            throw new \RuntimeException(sprintf('Base is not editable for "%s"', $this->ligneDTO->label));
        }
        
        $this->ligneDTO->base = $base;
    }
    
    public function setQuantite($quantite)
    {
        if(! $this->ligneDTO->quantiteEditable)
        {
            throw new \RuntimeException(sprintf('Quantite is not editable for "%s"', $this->ligneDTO->label));
        }
    
        $this->ligneDTO->quantite = $quantite;
    }

    public function getDomain()
    {
        return new Domains\Ligne($this->ligneDTO);
    }
}
