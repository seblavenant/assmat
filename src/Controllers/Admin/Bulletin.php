<?php

namespace Assmat\Controllers\Admin;

use Symfony\Component\HttpFoundation\Response;
use Assmat\DataSource\Repositories;
use Assmat\DataSource\Forms;
use Symfony\Component\HttpFoundation\Request;
use Assmat\Services\Evenements;

class Bulletin
{
    private
        $twig,
        $request,
        $bulletinRepository,
        $evenementRepository;

    public function __construct(\Twig_Environment $twig, Request $request, Repositories\Bulletin $bulletinRepository, Repositories\Evenement $evenementRepository)
    {
        $this->twig = $twig;
        $this->request = $request;
        $this->bulletinRepository = $bulletinRepository;
        $this->evenementRepository = $evenementRepository;
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
        $evenements = $this->evenementRepository->findAllFromContrat($contratId, new Evenements\Periods\Month(new \DateTime()));

        return new Response($this->twig->render('admin/bulletins/new.html.twig', array(
            'contratId' => $contratId,
            'evenements' => $evenements,
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