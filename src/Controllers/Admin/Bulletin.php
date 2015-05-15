<?php

namespace Assmat\Controllers\Admin;

use Symfony\Component\HttpFoundation\Response;
use Assmat\DataSource\Repositories;
use Symfony\Component\HttpFoundation\Request;
use Assmat\Services;

class Bulletin
{
    private
        $twig,
        $request,
        $bulletinRepository,
        $evenementRepository,
        $contratRepository,
        $bulletinBuilder,
        $ligneRepository;

    public function __construct(\Twig_Environment $twig, Request $request, Repositories\Bulletin $bulletinRepository, Repositories\Evenement $evenementRepository, Repositories\Contrat $contratRepository, Services\Bulletin\Builder $bulletinBuilder, Repositories\Ligne $ligneRepository)
    {
        $this->twig = $twig;
        $this->request = $request;
        $this->bulletinRepository = $bulletinRepository;
        $this->evenementRepository = $evenementRepository;
        $this->contratRepository = $contratRepository;
        $this->bulletinBuilder = $bulletinBuilder;
        $this->ligneRepository = $ligneRepository;
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

        $contrat = $this->contratRepository->find($contratId);
        $evenements = $this->evenementRepository->findAllFromContrat($contratId, new Services\Evenements\Periods\Month(new \DateTime($annee . '-' . $mois)));
        $bulletin = $this->bulletinBuilder->build($contrat, $evenements, $annee, $mois);

        return new Response($this->twig->render('admin/bulletins/new.html.twig', array(
            'contrat' => $contrat,
            'evenements' => $evenements,
            'annee' => $annee,
            'mois' => $mois,
            'bulletin' => $bulletin,
        )));
    }

    public function createAction($contratId)
    {
        $this->validateDate();
        $mois = $this->request->get('mois');
        $annee = $this->request->get('annee');

        $contrat = $this->contratRepository->find($contratId);
        $evenements = $this->evenementRepository->findAllFromContrat($contratId, new Services\Evenements\Periods\Month(new \DateTime($annee . '-' . $mois)));
        $bulletin = $this->bulletinBuilder->build($contrat, $evenements, $annee, $mois);

        $bulletin = $bulletin->persist($this->bulletinRepository);

        foreach($bulletin->getLignes() as $ligne)
        {
            $ligne->setBulletinId($bulletin->getId());
            $ligne->persist($this->ligneRepository);
        }
    }

    public function readAction($id)
    {
        $bulletin = $this->bulletinRepository->find($id);

        return new Response($this->twig->render('admin/bulletins/read.html.twig', array(
            'bulletin' => $bulletin,
        )));
    }

    private function validateDate()
    {
        if(! $this->request->get('mois') || ! $this->request->get('annee'))
        {
            throw new \Exception('Les parametres "mois" et "annee" sont requis !');
        }
    }

    private function buildBulletin()
    {
        $this->validateDate();
        $mois = $this->request->get('mois');
        $annee = $this->request->get('annee');

        $contrat = $this->contratRepository->find($contratId);
        $evenements = $this->evenementRepository->findAllFromContrat($contratId, new Services\Evenements\Periods\Month(new \DateTime($annee . '-' . $mois)));

        return $this->bulletinBuilder->build($contrat, $evenements, $annee, $mois);
    }
}