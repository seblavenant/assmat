<?php

namespace Assmat\Controllers\Home;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class Provider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $app['home.controller'] = $app->share(function() use($app) {
            return new Controller($app['twig'], $app['url_generator'], $app['repository.employeur'], $app['repository.employe']);
        });

        $controllers = $app['controllers_factory'];

        $controllers->get('/', 'home.controller:indexAction')
                    ->bind('homepage');

        $controllers->get('/error', 'home.controller:errorAction')
                    ->bind('error');

        return $controllers;
    }
}