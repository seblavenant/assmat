<?php

namespace Assmat\Iterators\Filters\Lignes;

class Type extends \FilterIterator
{
    private
        $types;

    public function __construct(\Iterator $iterator, array $types)
    {
        parent::__construct($iterator);

        $this->types = $types;
    }

    public function accept()
    {
        $ligne = $this->getInnerIterator()->current();
        if(in_array($ligne->getType(), $this->types))
        {
            return true;
        }
    }
}