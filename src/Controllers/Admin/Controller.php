<?php

namespace Assmat\Controllers\Admin;

class Controller
{
    private
        $twig;

    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function indexAction()
    {
        return new Response('controller index');
    }
}