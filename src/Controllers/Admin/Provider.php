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
        $this->initializeSalairesControllers($controllers, $app);

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
        $app['contrat.controller'] = $app->share(function() use($app) {
            return new Contrat($app['twig'], $app['security'], $app['repository.employeur'], $app['repository.contrat']);
        });

        $controllers->get('/contrats', 'contrat.controller:indexAction')
                    ->bind('admin_contrats');

        $controllers->get('/contrats/{id}', 'contrat.controller:readAction')
                    ->bind('admin_contrats_read');

        return $controllers;
    }

    private function initializeSalairesControllers(ControllerCollection $controllers, Application $app)
    {
        $app['bulletin.controller'] = $app->share(function() use($app) {
            return new Bulletin($app['twig'], $app['repository.bulletin']);
        });

        $controllers->get('/contrats/{contratId}/bulletins', 'bulletin.controller:indexAction')
                    ->bind('admin_contrats_bulletins');

        return $controllers;
    }
}