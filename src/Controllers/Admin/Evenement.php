<?php

namespace Assmat\Controllers\Admin;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormFactoryInterface;
use Assmat\DataSource\Repositories;
use Assmat\DataSource\Domains;
use Assmat\DataSource\Forms;
use Symfony\Component\HttpFoundation\Request;
use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\Iterators\Filters as FilterIterators;

class Evenement
{
    private
        $twig,
        $request,
        $formFactory,
        $evenementRepository,
        $evenementTypeRepository,
        $bulletinRepository;

    public function __construct(\Twig_Environment $twig, Request $request, FormFactoryInterface $formFactory, Repositories\Evenement $evenementRepository, Repositories\EvenementType $evenementTypeRepository, Repositories\Bulletin $bulletinRepository)
    {
        $this->twig = $twig;
        $this->request = $request;
        $this->formFactory = $formFactory;
        $this->evenementRepository = $evenementRepository;
        $this->evenementTypeRepository = $evenementTypeRepository;
        $this->bulletinRepository = $bulletinRepository;
    }

    public function listAction($contratId)
    {
        $this->validateRangeDateParams();
        $mois = $this->request->get('mois');
        $annee = $this->request->get('annee');

        $evenements = $this->evenementRepository->findAllFromContrat($contratId);

        $bulletinId = null;
        $bulletin = $this->bulletinRepository->findOneFromContratAndDate($contratId, $annee, $mois);
        if($bulletin instanceof Domains\Bulletin)
        {
            $bulletinId = $bulletin->getId();
        }

        return new Response($this->twig->render('admin/evenements/list.html.twig', array(
            'contratId' => $contratId,
            'evenements' => $evenements,
            'evenementsType' => new FilterIterators\Evenements\Types\DureeFixe(new \ArrayIterator($this->evenementTypeRepository->findAll())),
            'mois' => $mois,
            'annee' => $annee,
            'bulletinId' => $bulletinId,
        )));
    }

    public function setAction()
    {
        $evenementForm = $this->formFactory->create(new Forms\Evenement());

        $evenementForm->handleRequest($this->request);

        if(!$evenementForm->isValid())
        {
            return new JsonResponse(array('error' => $evenementForm->getErrors(true)), 400);
        }

        $evenementDTO = new DTO\Evenement();
        $evenementDTO->date = $evenementForm->get('date')->getData();
        $evenementDTO->heureDebut = $evenementForm->get('heureDebut')->getData();
        $evenementDTO->heureFin = $evenementForm->get('heureFin')->getData();
        $evenementDTO->contratId = $evenementForm->get('contratId')->getData();
        $evenementDTO->typeId = $evenementForm->get('typeId')->getData();

        try
        {
            $this->evenementRepository->persist($evenementDTO);
        }
        catch(\Exception $e)
        {
            return new JsonResponse(array('error' => $e->getMessage()), 400);
        }

        return new JsonResponse(array('ok'));
    }

    public function deleteAction()
    {
        try
        {
            $evenement = $this->evenementRepository->findOneFromContratAndDay($this->request->get('contratId'), new \DateTime($this->request->get('date')));
            $this->evenementRepository->delete($evenement->getId());
        }
        catch(\Exception $e)
        {
            return new JsonResponse(array('error' => $e->getMessage(), 400));
        }

        return new JsonResponse(array('ok'));
    }

    private function validateRangeDateParams()
    {
        if(!in_array((int) $this->request->get('mois'), range(1, 12)))
        {
            throw new \Exception('Le mois ' . $this->request->get('mois') . ' est invalide !');
        }

        if((int) $this->request->get('annee') < 2000)
        {
            throw new \Exception('L\'année ' . $this->request->get('annee') . ' est invalide !');
        }
    }
}