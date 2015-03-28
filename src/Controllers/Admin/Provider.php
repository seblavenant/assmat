<?php

namespace Assmat\Controllers\Admin;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class Provider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $app['admin.controller'] = $app->share(function() use($app) {
            return new Controller($app['twig']);
        });

        $controllers = $app['controllers_factory'];

        $controllers->get('/', 'admin.controller:indexAction')
                    ->bind('admin_index');

        return $controllers;
    }
}