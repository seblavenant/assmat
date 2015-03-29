<?php

namespace Assmat;

use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Gaufrette\Filesystem;
use Gaufrette\Adapter\Local;
use Puzzle\Configuration;
use Silex\Provider\UrlGeneratorServiceProvider;

use Assmat\DataSource\Repositories;
use Assmat\DataSource\Domains;
use Herrera\Pdo\PdoServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Spear\Silex\Application\AbstractApplication;
use Silex\Provider\SecurityServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Assmat\Services\Security;

class Application  extends AbstractApplication
{
    protected function initializeServices()
    {
        $this->configureTwig();
        $this->initializeRepositories();
        $this->initializeSecurity();
    }

    protected function registerProviders()
    {
        $this->register(new SessionServiceProvider());
        $this->register(new UrlGeneratorServiceProvider());
        $this->register(new \Spear\Silex\Provider\DBAL());
        $this->register(new \Spear\Silex\Provider\Twig());
        $this->register(new \Spear\Silex\Provider\AsseticServiceProvider());
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
                'users' => $this->share(function () {
    				return new Security\UserProvider($this['repository.contact']);
				}),
            ),
        );

        $this->get('/user/login', function(Request $request){
            return $this['twig']->render('user/login_form.html.twig', array(
                'error'         => $this['security.last_error']($request),
                'last_username' => $this['session']->get('_security.last_username'),
            ));
        })->bind('user_login');
    }


    private function initializeRepositories()
    {
        $this['repository.employeur'] = function($c) {
            return new Repositories\Mysql\Employeur($c['db.default'], $c['repository.contact']);
        };

        $this['repository.employe'] = function($c) {
            return new Repositories\Mysql\Employe($c['db.default'], $c['repository.contact'], $c['repository.contrat']);
        };

        $this['repository.contact'] = function($c) {
            return new Repositories\Mysql\Contact($c['db.default']);
        };

        $this['repository.contrat'] = function($c) {
            return new Repositories\Mysql\Contrat($c['db.default'], $c['repository.bulletin']);
        };

        $this['repository.bulletin'] = function($c) {
            return new Repositories\Mysql\Bulletin($c['db.default']);
        };
    }
}
