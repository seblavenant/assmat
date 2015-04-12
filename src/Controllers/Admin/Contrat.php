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
        $contratRepository;

    public function __construct(\Twig_Environment $twig, SecurityContextInterface $security, Repositories\Employeur $employeurRepository, Repositories\Contrat $contratRepository)
    {
        $this->twig = $twig;
        $this->security = $security;
        $this->employeurRepository = $employeurRepository;
        $this->contratRepository = $contratRepository;
    }

    public function indexAction()
    {
        $contactId = $this->security->getToken()->getUser()->getContact()->getId();
        $employeur = $this->employeurRepository->findFromContact($contactId);

        $employes = $employeur->getEmployes();

        return new Response($this->twig->render('admin/contrats/list.html.twig', array(
            'employes' => $employes,
        )));
    }

    public function readAction()
    {

    }
}