<?php

namespace Assmat\Controllers\Home;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Twig_Environment;
use Assmat\DataSource\Repositories;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Controller
{
    private
        $twig,
        $urlGenerator,
        $employeurRepository,
        $employeRepository;

    public function __construct(Twig_Environment $twig, UrlGeneratorInterface $urlGenerator, Repositories\Employeur $employeurRepository, Repositories\Employe $employeRepository)
    {
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->employeurRepository = $employeurRepository;
        $this->employeRepository = $employeRepository;
    }

    public function indexAction()
    {
        return new RedirectResponse($this->urlGenerator->generate('admin_index'));
    }

    public function errorAction()
    {
        return $this->twig->render('Error/index.html.twig');
    }
}