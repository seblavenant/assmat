<?php

namespace Assmat\Controllers\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Assmat\DataSource\Repositories;
use Assmat\DataSource\Domains;
use Symfony\Component\HttpFoundation\Request;
use Assmat\Services;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class Bulletin
{
    private
        $twig,
        $request,
        $security,
        $urlGenerator,
        $bulletinRepository,
        $evenementRepository,
        $contratRepository,
        $ligneRepository,
        $bulletinBuilder;

    public function __construct(\Twig_Environment $twig, Request $request, SecurityContextInterface $security, UrlGeneratorInterface $urlGenerator, Repositories\Bulletin $bulletinRepository, Repositories\Evenement $evenementRepository, Repositories\Contrat $contratRepository, Repositories\Ligne $ligneRepository, Services\Bulletin\Builder $bulletinBuilder)
    {
        $this->twig = $twig;
        $this->request = $request;
        $this->security = $security;
        $this->bulletinRepository = $bulletinRepository;
        $this->evenementRepository = $evenementRepository;
        $this->contratRepository = $contratRepository;
        $this->bulletinBuilder = $bulletinBuilder;
        $this->ligneRepository = $ligneRepository;
        $this->urlGenerator = $urlGenerator;
    }

    public function indexAction($contratId)
    {
        $bulletins = $this->bulletinRepository->findFromContrat($contratId);

        return new Response($this->twig->render('admin/bulletins/list.html.twig', array(
            'bulletins' => $bulletins,
            'contratId' => $contratId,
        )));
    }

    public function newAction($contratId)
    {
        $this->validateDate();
        $mois = $this->request->get('mois');
        $annee = $this->request->get('annee');

        $bulletin = $this->bulletinRepository->findOneFromContratAndDate($contratId, $annee, $mois);
        if($bulletin instanceof Domains\Bulletin)
        {
            return new RedirectResponse($this->urlGenerator->generate('admin_bulletins_read', array('id' => $bulletin->getId())));
        }

        $contrat = $this->contratRepository->find($contratId);
        $contrat->validateContactAutorisation($this->getContact());
        $evenements = $this->evenementRepository->findAllFromContrat($contratId, new Services\Evenements\Periods\Month(new \DateTime($annee . '-' . $mois)));
        $bulletin = $this->bulletinBuilder->build($contrat, $evenements, $annee, $mois);

        return new Response($this->twig->render('admin/bulletins/read.html.twig', array(
            'contrat' => $contrat,
            'evenements' => $evenements,
            'annee' => $annee,
            'mois' => $mois,
            'bulletin' => $bulletin,
            'saveEnable' => true,
        )));
    }

    public function createAction($contratId)
    {
        $this->validateDate();
        $mois = $this->request->get('mois');
        $annee = $this->request->get('annee');

        $contrat = $this->contratRepository->find($contratId);
        $contrat->validateContactAutorisation($this->getContact());

        $evenements = $this->evenementRepository->findAllFromContrat($contratId, new Services\Evenements\Periods\Month(new \DateTime($annee . '-' . $mois)));
        $bulletin = $this->bulletinBuilder->build($contrat, $evenements, $annee, $mois);

        try
        {
            $bulletin = $bulletin->persist($this->bulletinRepository);

            foreach($bulletin->getLignes() as $ligne)
            {
                $ligne->setBulletinId($bulletin->getId());
                $ligne->persist($this->ligneRepository);
            }

            return new RedirectResponse($this->urlGenerator->generate('admin_bulletins_read', array('id' => $bulletin->getId())));
        }
        catch(\Exception $e)
        {
            return new RedirectResponse($this->urlGenerator->generate('admin_bulletins_new', array('contratId' => $contratId, 'annee' => $annee, 'mois' => $mois)));
        }
    }

    public function readAction($id)
    {
        $bulletin = $this->bulletinRepository->find($id);
        $bulletin->getContrat()->validateContactAutorisation($this->getContact());

        if(!$bulletin instanceof Domains\Bulletin)
        {
            throw new \Exception('Aucun bulletin ne correspond à cet identifiant');
        }

        return new Response($this->twig->render('admin/bulletins/read.html.twig', array(
            'contrat' => $bulletin->getContrat(),
            'evenements' => $bulletin->getEvenements(),
            'annee' => $bulletin->getAnnee(),
            'mois' => $bulletin->getMois(),
            'bulletin' => $bulletin,
            'saveEnable' => false,
        )));
    }

    private function validateDate()
    {
        if(!$this->request->get('mois') || !$this->request->get('annee'))
        {
            throw new \Exception('Les parametres "mois" et "annee" sont requis !');
        }
    }

    private function getContact()
    {
        return $this->security->getToken()->getUser()->getContact();
    }
}