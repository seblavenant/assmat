<?php

namespace Assmat\Controllers\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Assmat\DataSource\Repositories;

class Contrat
{
    private
        $twig,
        $security,
        $employeurRepository,
        $contratRepository,
        $employeRepository;

    public function __construct(\Twig_Environment $twig, SecurityContextInterface $security, Repositories\Employeur $employeurRepository, Repositories\Employe $employeRepository, Repositories\Contrat $contratRepository)
    {
        $this->twig = $twig;
        $this->security = $security;
        $this->employeurRepository = $employeurRepository;
        $this->contratRepository = $contratRepository;
        $this->employeRepository = $employeRepository;
    }

    public function indexAction()
    {
        $contactId = $this->security->getToken()->getUser()->getContact()->getId();
        var_dump('');
        var_dump($contactId);
        $employeur = $this->employeurRepository->findFromContact($contactId);
        $employe = $this->employeRepository->findFromContact($contactId);

        return new Response($this->twig->render('admin/contrats/list.html.twig', array(
            'employeur' => $employeur,
            'employe' => $employe,
        )));
    }

    public function readAction()
    {

    }
}