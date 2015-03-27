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

class Application  extends AbstractApplication
{
    protected function initializeServices()
    {
        $this->configureTwig();
        $this->initializeRepositories();
    }

    private function configureTwig()
    {
        $this['twig.path.manager']->addPath(array(
            $this['root.path'] . 'views/',
        ));
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

    private function mountProviders()
    {
        $this->mount('/', new Controllers\Home\Provider());
    }
}
