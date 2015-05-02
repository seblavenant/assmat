<?php

namespace Assmat\Iterators\Filters\Lignes;

class Action extends \FilterIterator
{
    private
        $action;

    public function __construct(\Iterator $iterator, $action)
    {
        parent::__construct($iterator);

        $this->action = $action;
    }

    public function accept()
    {
        $ligne = $this->getInnerIterator()->current();
        if($ligne->getAction() === $this->action)
        {
            return true;
        }
    }
}
