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
use Symfony\Component\Security\Core\SecurityContextInterface;

class Evenement
{
    private
        $twig,
        $request,
        $security,
        $formFactory,
        $evenementRepository,
        $evenementTypeRepository,
        $bulletinRepository,
        $contratRepository;

    public function __construct(\Twig_Environment $twig, Request $request, SecurityContextInterface $security, FormFactoryInterface $formFactory, Repositories\Evenement $evenementRepository, Repositories\EvenementType $evenementTypeRepository, Repositories\Bulletin $bulletinRepository, Repositories\Contrat $contratRepository)
    {
        $this->twig = $twig;
        $this->request = $request;
        $this->security = $security;
        $this->formFactory = $formFactory;
        $this->evenementRepository = $evenementRepository;
        $this->evenementTypeRepository = $evenementTypeRepository;
        $this->bulletinRepository = $bulletinRepository;
        $this->contratRepository = $contratRepository;
    }

    public function listAction($contratId)
    {
        $contrat = $this->contratRepository->find($contratId);
        $contrat->validateContactAutorisation($this->getContact());

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
            'contrat' => $contrat,
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
            return new JsonResponse(array('message' => $evenementForm->getErrors(true)), 400);
        }

        $contratId = $evenementForm->get('contratId')->getData();
        $date = $evenementForm->get('date')->getData();

        $evenementDTO = new DTO\Evenement();
        $evenementDTO->date = $date;
        $evenementDTO->heureDebut = $evenementForm->get('heureDebut')->getData();
        $evenementDTO->heureFin = $evenementForm->get('heureFin')->getData();
        $evenementDTO->contratId = $contratId;
        $evenementDTO->typeId = $evenementForm->get('typeId')->getData();

        try
        {
            $contrat = $this->contratRepository->find($contratId);
            $contrat->validateContactAutorisation($this->getContact());

            $this->validateEvenementHasNoActiveBulletin($contratId, $date);
            $this->evenementRepository->persist($evenementDTO);
        }
        catch(\Exception $e)
        {
            return new JsonResponse(array('message' => $e->getMessage()), 400);
        }

        return new JsonResponse(array('ok'));
    }

    public function deleteAction()
    {
        $contratId = $this->request->get('contratId');
        $date = $this->request->get('date');

        try
        {
            $contrat = $this->contratRepository->find($contratId);
            $contrat->validateContactAutorisation($this->getContact());

            $this->validateEvenementHasNoActiveBulletin($contratId, $date);
            $evenement = $this->evenementRepository->findOneFromContratAndDay($contratId, new \DateTime($date));
            $this->evenementRepository->delete($evenement->getId());
        }
        catch(\Exception $e)
        {
            return new JsonResponse(array('message' => $e->getMessage()), 400);
        }

        return new JsonResponse(array('ok'));
    }

    private function validateEvenementHasNoActiveBulletin($contratId, $date)
    {
        $date = new \DateTime($date);
        $bulletin = $this->bulletinRepository->findOneFromContratAndDate($contratId, (int) $date->format('Y'), (int) $date->format('m'));

        if($bulletin instanceof Domains\Bulletin)
        {
            throw new \Exception('Il est interdit de modifier les évenements d\'un mois dont le bulletin a été créé');
        }
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

    private function getContact()
    {
        return $this->security->getToken()->getUser()->getContact();
    }
}