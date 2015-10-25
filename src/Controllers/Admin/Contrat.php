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
use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\DataSource\Domains;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

class Contrat
{
    private
        $twig,
        $request,
        $security,
        $urlGenerator,
        $formFactory,
        $formErrors,
        $contratForm,
        $employeurRepository,
        $contratRepository,
        $employeRepository;

    public function __construct(\Twig_Environment $twig, Request $request, SecurityContextInterface $security, UrlGeneratorInterface $urlGenerator, FormFactoryInterface $formFactory, Form\Errors $formErrors, Forms\Contrat $contratForm, Repositories\Employeur $employeurRepository, Repositories\Employe $employeRepository, Repositories\Contrat $contratRepository)
    {
        $this->twig = $twig;
        $this->request = $request;
        $this->security = $security;
        $this->urlGenerator = $urlGenerator;
        $this->formFactory = $formFactory;
        $this->formErrors = $formErrors;
        $this->contratForm = $contratForm;
        $this->employeurRepository = $employeurRepository;
        $this->contratRepository = $contratRepository;
        $this->employeRepository = $employeRepository;
    }

    public function indexAction()
    {
        return $this->listAction();
    }

    public function listAction()
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
        $contactId = $this->security->getToken()->getUser()->getContact()->getId();
        $employeur = $this->employeurRepository->findFromContact($contactId);

        $form = $this->formFactory->create(
            $this->contratForm,
            null,
            array('employeur' => $employeur)
        );

        return new Response($this->twig->render('admin/contrats/new.html.twig', array(
           'form' => $form->createView(),
        )));
    }

    public function createAction()
    {
        $contactId = $this->security->getToken()->getUser()->getContact()->getId();
        $employeur = $this->employeurRepository->findFromContact($contactId);

        $form = $this->formFactory->create(
            $this->contratForm,
            null,
            array('employeur' => $employeur)
        );

        $form->bind($this->request);

        $employeId = $this->retreiveEmployeId($form);

        if(empty($employeId))
        {
            $form->addError(new FormError('Vous devez selectionner un employé ou indiquer une clé d\'identification valide'));
        }

        if($form->isValid())
        {
            $contratDTO = new DTO\Contrat();
            $contratDTO->employeId = (int) $employeId;
            $contratDTO->employeurId = (int) $employeur->getId();
            $contratDTO->heuresHebdo = (int) $form->get('heuresHebdo')->getData();
            $contratDTO->indemnites = array();
            $contratDTO->joursGarde = (int) $form->get('joursGarde')->getData();
            $contratDTO->nom = $form->get('nom')->getData();
            $contratDTO->nombreSemainesAn = (int) $form->get('nombreSemainesAn')->getData();
            $contratDTO->salaireHoraire = (float) $form->get('salaireHoraire')->getData();
            $contratDTO->typeId = (int) $form->get('typeId')->getData();

            $contrat = new Domains\Contrat($contratDTO);
            $contrat->persist($this->contratRepository);

            $response = new JsonResponse(
                array(
                    'msg' => 'Contrat créé',
                    'data' => array(),
                    'location' => $this->urlGenerator->generate('admin_contrats_list'),
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

    private function retreiveEmployeId(FormInterface $form)
    {
        $employeId = $form->get('employeId')->getData();
        $employeKey = $form->get('employeKey')->getData();

        $employe = $this->employeRepository->findFromKey($employeKey);
        if($employe instanceof Domains\Employe)
        {
            $employeId = $employe->getId();
        }

        return $employeId;
    }

    public function readAction()
    {

    }
}