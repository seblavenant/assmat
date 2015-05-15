<?php

namespace Assmat\Iterators\Filters\Lignes;

class Type extends \FilterIterator
{
    private
        $typesId;

    public function __construct(\Iterator $iterator, array $typesId)
    {
        parent::__construct($iterator);

        $this->typesId = $typesId;
    }

    public function accept()
    {
        $ligne = $this->getInnerIterator()->current();
        if(in_array($ligne->getTypeId(), $this->typesId))
        {
            return true;
        }
    }
}