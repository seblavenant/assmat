<?php

namespace Assmat\Controllers\Admin;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Assmat\DataSource\Repositories;
use Assmat\DataSource\Forms;
use Symfony\Component\HttpFoundation\Request;

class Evenement
{
    private
        $twig,
        $request,
        $formFactory,
        $evenementRepository;

    public function __construct(\Twig_Environment $twig, Request $request, FormFactoryInterface $formFactory, Repositories\Evenement $evenementRepository)
    {
        $this->twig = $twig;
        $this->request = $request;
        $this->formFactory = $formFactory;
        $this->evenementRepository = $evenementRepository;
    }

    public function setAction()
    {
        return new JsonResponse(array('ok'));
    }
}