<?php

namespace Assmat\Controllers\Home;

use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;
use Assmat\DataSource\Repositories;

class Controller
{
    private
        $twig,
        $employeurRepository,
        $employeRepository;

    public function __construct(Twig_Environment $twig, Repositories\Employeur $employeurRepository, Repositories\Employe $employeRepository)
    {
        $this->twig = $twig;
        $this->employeurRepository = $employeurRepository;
        $this->employeRepository = $employeRepository;
    }

    public function indexAction()
    {
		$employeur = $this->employeurRepository->find(1);
		var_dump($employeur->getContact());

		$employe = $this->employeRepository->find(1);
		var_dump($employe->getContact());

        return new Response();
    }

    public function errorAction()
    {
        return $this->twig->render('Error/index.html.twig');
    }
}