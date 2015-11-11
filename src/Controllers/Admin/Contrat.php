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
use Assmat\DataSource\Constants;

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
        $employeRepository,
        $ligneTemplateRepository;

    public function __construct(\Twig_Environment $twig, Request $request, SecurityContextInterface $security, UrlGeneratorInterface $urlGenerator, FormFactoryInterface $formFactory, Form\Errors $formErrors, Forms\Contrat $contratForm, Repositories\Employeur $employeurRepository, Repositories\Employe $employeRepository, Repositories\Contrat $contratRepository, Repositories\LigneTemplate $ligneTemplateRepository)
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
        $this->ligneTemplateRepository = $ligneTemplateRepository;
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

        $indemnitesTemplate = $this->ligneTemplateRepository->findFromContexts(array(Constants\Lignes\Context::INDEMNITE));
        $indemnites = array();
        foreach($indemnitesTemplate as $indemniteTemplate)
        {
            $indemniteDTO = new DTO\Indemnite();
            $indemnites[$indemniteTemplate->getTypeId()] = new Domains\Indemnite($indemniteDTO);
        }

        $form = $this->formFactory->create(
            $this->contratForm,
            array(
                'indemnites' => $indemnites,
            ),
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

        $indemnitesTemplate = $this->ligneTemplateRepository->findFromContexts(array(Constants\Lignes\Context::INDEMNITE));
        $indemnites = array();
        foreach($indemnitesTemplate as $indemniteTemplate)
        {
            $indemniteDTO = new DTO\Indemnite();
            $indemnites[$indemniteTemplate->getTypeId()] = new Domains\Indemnite($indemniteDTO);
        }

        $form = $this->formFactory->create(
            $this->contratForm,
            array(
                'indemnites' => $indemnites,
            ),
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

            $indemnites = array();

            foreach($form->get('indemnites')->getData() as $typeId => $indemnite)
            {
                $indemniteDTO = new DTO\Indemnite();
                $indemniteDTO->montant = $indemnite->getMontant();
                $indemniteDTO->typeId = $typeId;

                $indemnites[] = new Domains\Indemnite($indemniteDTO);
            }

            $contratDTO->set('indemnites', $indemnites);

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

    public function editAction($id)
    {
        $contrat = $this->contratRepository->find($id);

        $form = $this->formFactory->create(
            $this->contratForm,
            $contrat,
            array('type' => Forms\Contrat::TYPE_EDIT)
        );

        return new Response($this->twig->render('admin/contrats/edit.html.twig', array(
            'contrat' => $contrat,
            'form' => $form->createView(),
        )));
    }

    public function updateAction($id)
    {
        $form = $this->formFactory->create(
            $this->contratForm,
            null,
            array('type' => Forms\Contrat::TYPE_EDIT)
        );

        $form->bind($this->request);

        if($form->isValid())
        {
            $contrat = $this->contratRepository->find($id);
            $contratDTO = $contrat->getDTO();
            $contratDTO->nom = $form->get('nom')->getData();
            $contratDTO->salaireHoraire = $form->get('salaireHoraire')->getData();
            (new Domains\Contrat($contratDTO))->persist($this->contratRepository);

            $response = new JsonResponse(
                array(
                    'msg' => 'Contrat mis à jour',
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