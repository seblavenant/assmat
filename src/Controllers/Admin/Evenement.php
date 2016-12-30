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
        $contratRepository,
        $congePayeRepository;

    public function __construct(
        \Twig_Environment $twig,
        Request $request,
        SecurityContextInterface $security,
        FormFactoryInterface $formFactory,
        Repositories\Evenement $evenementRepository,
        Repositories\EvenementType $evenementTypeRepository,
        Repositories\Bulletin $bulletinRepository,
        Repositories\Contrat $contratRepository,
        Repositories\CongePaye $congePayeRepository
    )
    {
        $this->twig = $twig;
        $this->request = $request;
        $this->security = $security;
        $this->formFactory = $formFactory;
        $this->evenementRepository = $evenementRepository;
        $this->evenementTypeRepository = $evenementTypeRepository;
        $this->bulletinRepository = $bulletinRepository;
        $this->contratRepository = $contratRepository;
        $this->congePayeRepository = $congePayeRepository;
    }

    public function listAction($contratId)
    {
        $contrat = $this->contratRepository->find($contratId);
        $contrat->validateContactAutorisation($this->getContact());

        $mois = $this->request->get('mois') ? $this->request->get('mois') : date('m');
        $annee = $this->request->get('annee') ? $this->request->get('annee') : date('Y');
        $this->validateRangeDateParams($mois, $annee);

        $evenements = $this->evenementRepository->findAllFromContrat($contratId, new \DateTime(sprintf('%s-%s', $annee, $mois)));

        $bulletinId = null;
        $bulletin = $this->bulletinRepository->findOneFromContratAndDate($contratId, $annee, $mois);
        if($bulletin instanceof Domains\Bulletin)
        {
            $bulletinId = $bulletin->getId();
        }

        return new Response($this->twig->render('admin/evenements/contrat.html.twig', array(
            'contrat' => $contrat,
            'evenements' => $evenements,
            'evenementsType' => new FilterIterators\Evenements\Types\DureeFixe(new \ArrayIterator($this->evenementTypeRepository->findAll())),
            'mois' => $mois,
            'annee' => $annee,
            'bulletinId' => $bulletinId,
        )));
    }

    public function contactListAction()
    {
        $mois = $this->request->get('mois') ? $this->request->get('mois') : date('m');
        $annee = $this->request->get('annee') ? $this->request->get('annee') : date('Y');
        $this->validateRangeDateParams($mois, $annee);

        $contactId = $this->getContact()->getId();
        $evenements = $this->evenementRepository->findAllFromContact($contactId, new \DateTime(sprintf('%s-%s', $annee, $mois)));
        $contrats = $this->contratRepository->findFromContact($contactId);

        return new Response($this->twig->render('admin/evenements/contact.html.twig', array(
            'contrats' => $contrats,
            'evenements' => $evenements,
            'evenementsType' => new FilterIterators\Evenements\Types\DureeFixe(new \ArrayIterator($this->evenementTypeRepository->findAll())),
            'mois' => $mois,
            'annee' => $annee,
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
        $contrat = $this->contratRepository->find($contratId);
        $date = $evenementForm->get('date')->getData();

        $evenementDTO = new DTO\Evenement();
        $evenementDTO->date = $date;
        $evenementDTO->heureDebut = $evenementForm->get('heureDebut')->getData();
        $evenementDTO->heureFin = $evenementForm->get('heureFin')->getData();
        $evenementDTO->contratId = $contratId;
        $evenementDTO->typeId = $evenementForm->get('typeId')->getData();

        $dateTime = new \DateTime($date);
        try
        {
            $this->validateCPHebdo($contrat, $dateTime);
            $contrat->validateContactAutorisation($this->getContact());
            $this->validateEvenementHasNoActiveBulletin($contratId, $dateTime);

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
        $date = new \DateTime($this->request->get('date'));

        try
        {
            $contrat = $this->contratRepository->find($contratId);
            $contrat->validateContactAutorisation($this->getContact());

            $this->validateEvenementHasNoActiveBulletin($contratId, $date);
            $evenement = $this->evenementRepository->findOneFromContratAndDay($contratId, $date);
            $this->evenementRepository->delete($evenement->getId());
        }
        catch(\Exception $e)
        {
            return new JsonResponse(array('message' => $e->getMessage()), 400);
        }

        return new JsonResponse(array('ok'));
    }

    private function validateCPHebdo(Domains\Contrat $contrat, \DateTime $date)
    {
        $cpCount = $this->congePayeRepository->coundAllFromContratAndWeek($contrat->getId(), $date);

        if($cpCount >= $contrat->getJoursGarde())
        {
            throw new \RuntimeException(sprintf('Vous ne pouvez poser plus de CP par semaine que le nombre de jours de garde définit au contrat (%s)', $contrat->getJoursGarde()));
        }
    }

    private function validateEvenementHasNoActiveBulletin($contratId, \DateTime $date)
    {
        $bulletin = $this->bulletinRepository->findOneFromContratAndDate($contratId, (int) $date->format('Y'), (int) $date->format('m'));

        if($bulletin instanceof Domains\Bulletin)
        {
            throw new \Exception('Il est interdit de modifier les évenements d\'un mois dont le bulletin a été créé');
        }
    }

    private function validateRangeDateParams($mois, $annee)
    {
        if(!in_array((int) $mois, range(1, 12)))
        {
            throw new \Exception('Le mois ' . $this->request->get('mois') . ' est invalide !');
        }

        if((int) $annee < 2000)
        {
            throw new \Exception('L\'année ' . $this->request->get('annee') . ' est invalide !');
        }
    }

    private function getContact()
    {
        return $this->security->getToken()->getUser()->getContact();
    }
}