<?php

namespace Assmat\Iterators\Filters\Lignes;

class Context extends \FilterIterator
{
    private
        $contexts;

    public function __construct(\Iterator $iterator, array $contexts)
    {
        parent::__construct($iterator);

        $this->contexts = $contexts;
    }

    public function accept()
    {
        $ligne = $this->getInnerIterator()->current();
        if(in_array($ligne->getContext(), $this->contexts))
        {
            return true;
        }
    }
}