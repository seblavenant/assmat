<?php

namespace Assmat\Controllers\Admin;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
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

    public function listAction($contratId)
    {
        $this->validateRangeDateParams();

        $bulletinForm = $this->formFactory->create(new Forms\Bulletin($this->request));
        $evenements = $this->evenementRepository->findFromContrat($contratId);

        return new Response($this->twig->render('admin/evenements/list.html.twig', array(
            'contratId' => $contratId,
            'evenements' => $evenements,
            'mois' => $this->request->get('mois'),
            'annee' => $this->request->get('annee'),
        )));
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

    public function deleteAction()
    {
        $evenement = $this->evenementRepository->findFromDate($this->request->get('date'));

        $this->evenementRepository->delete($evenement->getId());

        return new JsonResponse(array('ok'));
    }

    private function validateRangeDateParams()
    {
        if(! in_array((int) $this->request->get('mois'), range(1, 12)))
        {
            throw new \Exception('Le mois ' . $this->request->get('mois') . ' est invalide !');
        }

        if((int) $this->request->get('annee') < 2000)
        {
            throw new \Exception('L\'année ' . $this->request->get('annee') . ' est invalide !');
        }
    }
}