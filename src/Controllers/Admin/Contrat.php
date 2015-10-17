<?php

namespace Assmat\Controllers\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Assmat\DataSource\Repositories;
use Symfony\Component\Form\FormFactoryInterface;
use Assmat\DataSource\Forms;
use Assmat\Services\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class Contrat
{
    private
        $twig,
        $request,
        $security,
        $formFactory,
        $formErrors,
        $employeurRepository,
        $contratRepository,
        $employeRepository;

    public function __construct(\Twig_Environment $twig, Request $request, SecurityContextInterface $security, FormFactoryInterface $formFactory, Form\Errors $formErrors, Repositories\Employeur $employeurRepository, Repositories\Employe $employeRepository, Repositories\Contrat $contratRepository)
    {
        $this->twig = $twig;
        $this->request = $request;
        $this->security = $security;
        $this->formFactory = $formFactory;
        $this->formErrors = $formErrors;
        $this->employeurRepository = $employeurRepository;
        $this->contratRepository = $contratRepository;
        $this->employeRepository = $employeRepository;
    }

    public function indexAction()
    {
        $contactId = $this->security->getToken()->getUser()->getContact()->getId();
        $employeur = $this->employeurRepository->findFromContact($contactId);
        $employe = $this->employeRepository->findFromContact($contactId);

        return new Response($this->twig->render('admin/contrats/list.html.twig', array(
            'employeur' => $employeur,
            'employe' => $employe,
        )));
    }

    public function newAction()
    {
        $form = $this->formFactory->create(new Forms\Contrat());

        return new Response($this->twig->render('admin/contrats/new.html.twig', array(
           'form' => $form->createView(),
        )));
    }

    public function createAction()
    {
        $form = $this->formFactory->create(new Forms\Contrat());

        $form->bind($this->request);

        if($form->isValid())
        {
            $response = new JsonResponse(
                array(
                    'msg' => 'Contrat créé',
                    'data' => array(),
                )
                , 200
            );
        }
        else
        {
            $response = new JsonResponse(
                array(
                    'msg' => 'Une erreur s\'est produite lors de l\'enregistrement',
                    'data' => $this->formErrors->getMessages($form),
                )
                , 400
            );
        }

        return $response;

    }

    public function readAction()
    {

    }
}