<?php

namespace Assmat;

use Silex\Provider as SilexProvider;
use Spear\Silex\Provider as SpearProvider;
use Assmat\DataSource\Repositories;
use Spear\Silex\Application\AbstractApplication;
use Silex\Provider\SecurityServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Assmat\Services;


class Application extends AbstractApplication
{
    protected function initializeServices()
    {
        $this->configureTwig();
        $this->initializeRepositories();
        $this->initializeSecurity();
    }

    protected function registerProviders()
    {
        $this->register(new SilexProvider\SessionServiceProvider());
        $this->register(new SilexProvider\UrlGeneratorServiceProvider());
        $this->register(new SilexProvider\FormServiceProvider());
        $this->register(new SilexProvider\TranslationServiceProvider());
        $this->register(new SpearProvider\DBAL());
        $this->register(new SpearProvider\Twig());
        $this->register(new SpearProvider\AsseticServiceProvider());
    }

    protected function mountControllerProviders()
    {
        $this->mount('/', new Controllers\Home\Provider());
        $this->mount('/admin', new Controllers\Admin\Provider());
    }


    private function configureTwig()
    {
        $this['twig.path.manager']->addPath(array(
            $this['root.path'] . 'views/',
        ));

        $this['twig']->addExtension(new Services\Twig\AdminExtension());
    }

    private function initializeSecurity()
    {
        if(PHP_SAPI === 'cli')
        {
            return;
        }

        $this->register(new SecurityServiceProvider());
        $this['security.firewalls'] = array(
            'admin' => array(
                'pattern' => '^/admin',
                'form' => array('login_path' => '/user/login', 'check_path' => '/admin/login_check'),
                'logout' => array('logout_path' => '/admin/logout'),
                'users' => $this->share(function() {
                    return new Services\Security\UserProvider($this['repository.contact']);
                }),
            ),
        );

        $this->get('/user/login', function(Request $request) {
            return $this['twig']->render('user/login_form.html.twig', array(
                'error'         => $this['security.last_error']($request),
                'last_username' => $this['session']->get('_security.last_username'),
            ));
        })->bind('user_login');
    }


    private function initializeRepositories()
    {
        $this['repository.employeur'] = function($c) {
            return new Repositories\Mysql\Employeur($c['db.default'], $c['repository.contact'], $c['repository.employe']);
        };

        $this['repository.employe'] = function($c) {
            return new Repositories\Mysql\Employe($c['db.default'], $c['repository.contact'], $c['repository.contrat']);
        };

        $this['repository.contact'] = function($c) {
            return new Repositories\Mysql\Contact($c['db.default']);
        };

        $this['repository.contrat'] = function($c) {
            return new Repositories\Mysql\Contrat($c['db.default']);
        };

        $this['repository.bulletin'] = function($c) {
            return new Repositories\Mysql\Bulletin($c['db.default'], $c['repository.evenement']);
        };

        $this['repository.evenement'] = function($c) {
            return new Repositories\Mysql\Evenement($c['db.default']);
        };
    }
}
