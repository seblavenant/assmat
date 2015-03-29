<?php

namespace Assmat\Controllers\Admin;

use Symfony\Component\HttpFoundation\Response;

class Controller
{
    private
        $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function indexAction()
    {
        return new Response($this->twig->render('admin/dashboard.html.twig'));
    }
}