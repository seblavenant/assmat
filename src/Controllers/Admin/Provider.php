<?php

namespace Assmat\Controllers\Admin;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Silex\ControllerCollection;

class Provider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $this->initializeAdminControllers($controllers, $app);
        $this->initializeContratsControllers($controllers, $app);

        return $controllers;
    }

    private function initializeAdminControllers(ControllerCollection $controllers, Application $app)
    {
        $app['admin.controller'] = $app->share(function() use($app) {
            return new Admin($app['twig']);
        });

        $controllers->get('/', 'admin.controller:indexAction')
                    ->bind('admin_index');

        return $controllers;
    }

    private function initializeContratsControllers(ControllerCollection $controllers, Application $app)
    {
        $app['contrats.controller'] = $app->share(function() use($app) {
            return new Contrat($app['twig'], $app['security'], $app['repository.employeur'], $app['repository.contrat']);
        });

        $controllers->get('/contrats', 'contrats.controller:indexAction')
                    ->bind('admin_contrats');

        $controllers->get('/contrats/{id}', 'contrats.controller:readAction')
                    ->bind('admin_contrats_read');

        return $controllers;
    }
}