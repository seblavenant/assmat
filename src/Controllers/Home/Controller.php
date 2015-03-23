<?php

namespace Assmat\Controllers\Home;

use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;
use Assmat\DataSource\Domains;

class Controller
{
    private
        $twig,
        $employeur;

    public function __construct(Twig_Environment $twig, Domains\Employeur $employeur)
    {
        $this->twig = $twig;
        $this->employeur = $employeur;
    }

    public function indexAction()
    {
        $employeur = $this->employeur->loadFromId(1);
        $employeur->getPageEmploiId();

        $contact = $employeur->getContact();
        $contact->getNom();

        return new Response();
    }

    public function errorAction()
    {
        return $this->twig->render('Error/index.html.twig');
    }
}