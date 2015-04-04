<?php

namespace Assmat\Controllers\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Assmat\DataSource\Repositories;

class Bulletin
{
    private
        $twig,
        $bulletinRepository;

    public function __construct(\Twig_Environment $twig, Repositories\Bulletin $bulletinRepository)
    {
        $this->twig = $twig;
        $this->bulletinRepository = $bulletinRepository;
    }

    public function indexAction($contratId)
    {
        $bulletins = $this->bulletinRepository->findFromContrat($contratId);

        return new Response($this->twig->render('admin/bulletins.html.twig', array(
            'bulletins' => $bulletins,
        )));
    }
}