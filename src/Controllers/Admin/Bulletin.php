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
        $bulletinBuilder;

    public function __construct(\Twig_Environment $twig, Request $request, Repositories\Bulletin $bulletinRepository, Repositories\Evenement $evenementRepository, Repositories\Contrat $contratRepository, Services\Bulletin\Builder $bulletinBuilder)
    {
        $this->twig = $twig;
        $this->request = $request;
        $this->bulletinRepository = $bulletinRepository;
        $this->evenementRepository = $evenementRepository;
        $this->contratRepository = $contratRepository;
        $this->bulletinBuilder = $bulletinBuilder;
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
        $contrat = $this->contratRepository->find($contratId);
        $evenements = $this->evenementRepository->findAllFromContrat($contratId, new Services\Evenements\Periods\Month(new \DateTime($this->request->get('annee') . '-' . $this->request->get('mois'))));

        return new Response($this->twig->render('admin/bulletins/new.html.twig', array(
            'contrat' => $contrat,
            'evenements' => $evenements,
            'bulletin' => $this->bulletinBuilder->build($contrat, $evenements),
        )));
    }

    public function readAction($id)
    {
        $bulletin = $this->bulletinRepository->find($id);

        return new Response($this->twig->render('admin/bulletins/read.html.twig', array(
            'bulletin' => $bulletin,
        )));
    }
}