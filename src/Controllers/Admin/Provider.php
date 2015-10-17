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
        $this->initializeBulletinsControllers($controllers, $app);
        $this->initializeEvenementControllers($controllers, $app);

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
            return new Contrat(
                $app['twig'],
                $app['request'],
                $app['security'],
                $app['form.factory'],
                $app['form.errors'],
                $app['repository.employeur'],
                $app['repository.employe'],
                $app['repository.contrat']
            );
        });

        $controllers->get('/contrats', 'contrat.controller:indexAction')
                    ->bind('admin_contrats');

        $controllers->get('/contrats/new', 'contrat.controller:newAction')
                    ->bind('admin_contrats_new');

        $controllers->post('/contrats/', 'contrat.controller:createAction')
                    ->bind('admin_contrats_create');

        $controllers->get('/contrats/{id}', 'contrat.controller:readAction')
                    ->bind('admin_contrats_read');

        return $controllers;
    }

    private function initializeBulletinsControllers(ControllerCollection $controllers, Application $app)
    {
        $app['bulletin.controller'] = $app->share(function() use($app) {
            return new Bulletin(
                $app['twig'],
                $app['request'],
                $app['security'],
                $app['url_generator'],
                $app['repository.bulletin'],
                $app['repository.evenement'],
                $app['repository.contrat'],
                $app['repository.ligne'],
                $app['bulletin.builder']
            );
        });

        $controllers->get('/contrats/{contratId}/bulletins', 'bulletin.controller:indexAction')
                    ->bind('admin_bulletins');

        $controllers->post('/contrats/{contratId}/bulletins', 'bulletin.controller:createAction')
                    ->bind('admin_bulletins_create');

        $controllers->get('/bulletins/{id}', 'bulletin.controller:readAction')
                    ->bind('admin_bulletins_read');

        $controllers->get('/bulletins/{id}/print', 'bulletin.controller:printAction')
                    ->bind('admin_bulletins_print');

        $controllers->get('/contrats/{contratId}/bulletins/new', 'bulletin.controller:newAction')
                    ->bind('admin_bulletins_new');

        return $controllers;
    }

    private function initializeEvenementControllers(ControllerCollection $controllers, Application $app)
    {
        $app['evenement.controller'] = $app->share(function() use($app) {
            return new Evenement(
                $app['twig'],
                $app['request'],
                $app['security'],
                $app['form.factory'],
                $app['repository.evenement'],
                $app['repository.evenementType'],
                $app['repository.bulletin'],
                $app['repository.contrat']
            );
        });

        $controllers->get('/contrats/{contratId}/evenements/', 'evenement.controller:setAction')
                    ->method('POST')
                    ->bind('admin_evenements_set');

        $controllers->get('/contrats/{contratId}/evenements/', 'evenement.controller:deleteAction')
                    ->method('DELETE')
                    ->bind('admin_evenements_delete');

        $controllers->get('/contrats/{contratId}/evenements/', 'evenement.controller:listAction')
                    ->bind('admin_evenements_list');
    }
}