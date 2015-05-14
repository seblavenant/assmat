<?php

namespace Assmat\Iterators\Filters\Lignes;

class Action extends \FilterIterator
{
    private
        $context;

    public function __construct(\Iterator $iterator, $context)
    {
        parent::__construct($iterator);

        $this->context = $context;
    }

    public function accept()
    {
        $ligne = $this->getInnerIterator()->current();
        if($ligne->getContext() === $this->context)
        {
            return true;
        }
    }
}