<?php

namespace Assmat\Controllers\Admin;

use Silex\Application;
use Silex\ControllerProviderInterface;
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
        $this->initializeProfileControllers($controllers, $app);
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
                $app['url_generator'],
                $app['form.factory'],
                $app['form.errors'],
                $app['form.contrat'],
                $app['repository.employeur'],
                $app['repository.employe'],
                $app['repository.contrat'],
                $app['repository.ligneTemplate']
            );
        });

        $controllers->get('/contrats', 'contrat.controller:indexAction')
                    ->bind('admin_contrats');

        $controllers->get('/contrats', 'contrat.controller:listAction')
                    ->bind('admin_contrats_list');

        $controllers->get('/contrats/{id}', 'contrat.controller:readAction')
                    ->bind('admin_contrats_read')
                    ->assert('id', '\d+');

        $controllers->get('/contrats/new', 'contrat.controller:newAction')
                    ->bind('admin_contrats_new');

        $controllers->post('/contrats/', 'contrat.controller:createAction')
                    ->bind('admin_contrats_create');

        $controllers->get('/contrats/edit/{id}', 'contrat.controller:editAction')
                    ->bind('admin_contrats_edit')
                    ->assert('id', '\d+');

        $controllers->post('/contrats/{id}', 'contrat.controller:updateAction')
                    ->bind('admin_contrats_update')
                    ->assert('id', '\d+');

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
                $app['bulletin.builder.fromEvenements'],
                $app['bulletin.builder.fromLignes']
            );
        });

        $controllers->get('/contrats/{contratId}/bulletins', 'bulletin.controller:indexAction')
                    ->bind('admin_bulletins');

        $controllers->post('/contrats/{contratId}/bulletins', 'bulletin.controller:createAction')
                    ->bind('admin_bulletins_create');

        $controllers->get('/bulletins/{id}', 'bulletin.controller:readAction')
                    ->bind('admin_bulletins_read');
        
        $controllers->put('/bulletins/{id}', 'bulletin.controller:updateAction')
                    ->bind('admin_bulletins_update');

        $controllers->get('/bulletins/{id}/print', 'bulletin.controller:printAction')
                    ->bind('admin_bulletins_print');

        $controllers->get('/bulletins/{id}/lignes', 'bulletin.controller:lignesAction')
                    ->bind('admin_bulletins_lignes');

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

        $controllers->post('/contrats/{contratId}/evenements/', 'evenement.controller:setAction')
                    ->bind('admin_evenements_set');

        $controllers->delete('/contrats/{contratId}/evenements/', 'evenement.controller:deleteAction')
                    ->bind('admin_evenements_delete');

        $controllers->get('/contrats/{contratId}/evenements/', 'evenement.controller:listAction')
                    ->bind('admin_evenements_contrat_list');

        $controllers->get('/evenements/', 'evenement.controller:contactListAction')
                    ->bind('admin_evenements_contact_list');
    }

    private function initializeProfileControllers(ControllerCollection $controllers, Application $app)
    {
        $app['profile.controller'] = $app->share(function() use($app) {
            return new Profile(
                $app['twig'],
                $app['request'],
                $app['security'],
                $app['security.encoder_factory'],
                $app['form.factory'],
                $app['form.profile'],
                $app['form.password'],
                $app['form.errors'],
                $app['repository.employe'],
                $app['repository.employeur'],
                $app['repository.contact']
            );
        });

        $controllers->get('/profiles/', 'profile.controller:editAction')
                    ->bind('admin_profiles_edit');

        $controllers->post('/profiles/', 'profile.controller:updateAction')
                    ->bind('admin_profiles_update');

        $controllers->get('/profiles/password', 'profile.controller:passwordEditAction')
                    ->bind('admin_profiles_password_edit');

        $controllers->post('/profiles/password', 'profile.controller:passwordUpdateAction')
                    ->bind('admin_profiles_password_update');
    }
}