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

class Application extends \Silex\Application
{
    private
        $configuration;

    public function __construct()
    {
        parent::__construct();

        $this->loadConfiguration();
        $this->registerServiceProviders();
        $this->initializeDebugMode();
        $this->initializeServices();
        $this->initializeRepositories();
        $this->mountProviders();
        $this->initializeErrorHandler();
    }

    private function loadConfiguration()
    {
        $fileSystem = new Filesystem(
            new Local(__DIR__ . '/../config')
        );

        $this->configuration = new Configuration\Yaml($fileSystem);
        $this['configuration'] = $this->share(function(){
            return $this->configuration;
        });
    }

    private function initializeDebugMode()
    {
        $this['debug'] = $this->configuration->readRequired('app/debug');
    }

    private function registerServiceProviders()
    {
        $this->register(new ServiceControllerServiceProvider());
        $this->register(new UrlGeneratorServiceProvider());
        $this->register(new \Spear\Silex\Provider\DBAL());

        $this->register(new TwigServiceProvider(), array(
            'twig.path' => __DIR__ . '/../views',
        ));
    }

    private function initializeServices()
    {

    }

    private function initializeRepositories()
    {
        $this['repository.employeur'] = function($c) {
            return new Repositories\Mysql\Employeur($c['db.default'], $c['repository.contact']);
        };

        $this['repository.employe'] = function($c) {
            return new Repositories\Mysql\Employe($c['db.default'], $c['repository.contact']);
        };

        $this['repository.contact'] = function($c) {
            return new Repositories\Mysql\Contact($c['db.default']);
        };
    }

    private function mountProviders()
    {
        $this->mount('/', new Controllers\Home\Provider());
    }

    private function initializeErrorHandler()
    {
        $this->error(function () {
            if(! $this['debug']) {
                return $this->redirect($this['url_generator']->generate('error'));
            }
        });
    }
}
