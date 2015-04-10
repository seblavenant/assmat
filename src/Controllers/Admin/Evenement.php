<?php

namespace Assmat\Controllers\Admin;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Assmat\DataSource\Repositories;
use Assmat\DataSource\Forms;
use Symfony\Component\HttpFoundation\Request;
use Assmat\DataSource\DataTransferObjects as DTO;

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
        $evenementForm = $this->formFactory->create(new Forms\Evenement());

        $evenementForm->handleRequest($this->request);

        if(! $evenementForm->isValid())
        {
            return new JsonResponse(array('error' => $evenementForm->getErrors(true)), 400);
        }

        $evenementDTO = new DTO\Evenement();
        $evenementDTO->date = $evenementForm->get('date')->getData();
        $evenementDTO->heureDebut = $evenementForm->get('heureDebut')->getData();
        $evenementDTO->heureFin = $evenementForm->get('heureFin')->getData();
        $evenementDTO->contratId = $evenementForm->get('contratId')->getData();
        $evenementDTO->type =  $evenementForm->get('type')->getData();

        try
        {
            $this->evenementRepository->persist($evenementDTO);
        }
        catch(\Exception $e)
        {
            return new JsonResponse(array('error' => $e->getMessage(), 400));
        }

        return new JsonResponse(array('ok'));
    }
}