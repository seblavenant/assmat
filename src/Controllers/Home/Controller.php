<?php

namespace Assmat\Controllers\Home;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Twig_Environment;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Controller
{
    private
        $twig,
        $urlGenerator;

    public function __construct(Twig_Environment $twig, UrlGeneratorInterface $urlGenerator)
    {
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
    }

    public function indexAction()
    {
        return new RedirectResponse($this->urlGenerator->generate('admin_index'));
    }

    public function errorAction()
    {
        return $this->twig->render('Error/index.html.twig');
    }
}