<?php

namespace Assmat\DataSource\Domains;

use Assmat\DataSource\DataTransferObjects as DTO;

class Bulletin
{
    private
        $fields;

    public function __construct(DTO\Bulletin $bulletinDTO)
    {
        $this->fields = $bulletinDTO;
    }

    public function getId()
    {
        return $this->fields->id;
    }

    public function getContrats()
    {
        return $this->fields->load('contrats');
    }
}