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

        $this['bulletin.builder'] = function($c) {
            return new Services\Bulletin\Builder($c['repository.ligneTemplate']);
        };
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
        $this['repository.employeur'] = function() {
            return new Repositories\Mysql\Employeur($this['db.default'], $this['repository.contact'], $this['repository.employe']);
        };

        $this['repository.employe'] = function() {
            return new Repositories\Mysql\Employe($this['db.default'], $this['repository.contact'], $this['repository.contrat.proxy']);
        };

        $this['repository.contact'] = function() {
            return new Repositories\Mysql\Contact($this['db.default']);
        };

        $this['repository.contrat.closure'] = $this->protect(function() {
            return new Repositories\Mysql\Contrat($this['db.default'], $this['repository.indemnite'], $this['repository.employe'], $this['repository.employeur']);
        });

        $this['repository.contrat'] = function() {
            return $this['repository.contrat.closure']();
        };

        $this['repository.contrat.proxy'] = function() {
            return new Repositories\Proxy\Contrat($this['repository.contrat.closure']);
        };

        $this['repository.bulletin'] = function() {
            return new Repositories\Mysql\Bulletin($this['db.default'], $this['repository.evenement'], $this['repository.contrat'], $this['repository.ligne']);
        };

        $this['repository.evenement'] = function() {
            return new Repositories\Mysql\Evenement($this['db.default'], $this['repository.evenementType']);
        };

        $this['repository.indemnite'] = function() {
            return new Repositories\Mysql\Indemnite($this['db.default']);
        };

        $this['repository.evenementType'] = function() {
            return new Repositories\Memory\Evenement\Type();
        };

        $this['repository.ligneTemplate'] = function() {
            return new Repositories\Memory\Ligne\Template();
        };

        $this['repository.ligne'] = function() {
            return new Repositories\Mysql\Ligne($this['db.default']);
        };
    }
}