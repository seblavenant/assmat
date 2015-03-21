<?php

namespace Assmat\Controllers\Home;

use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;
use Assmat\DataSource\Repositories;

class Controller
{
    private
        $twig,
    	$employeurRepository;

    public function __construct(Twig_Environment $twig, Repositories\Employeur $employeurRepository)
    {
        $this->twig = $twig;
        $this->employeurRepository = $employeurRepository;
    }
    
    public function indexAction()
    {
        $employeur = $this->employeurRepository->findById(42);
        $employeur->getPageEmploiId();
        
        $contact = $employeur->getContact();
        $contact->getNom();
        
        var_dump($employeur);
        var_dump($contact);

        
        return new Response();
    }
    
    public function errorAction()
    {
        return $this->twig->render('Error/index.html.twig');
    }
}