<?php

namespace Assmat;

use Silex\Provider as SilexProvider;
use Silex\Provider\SecurityServiceProvider;
use Spear\Silex\Provider as SpearProvider;
use Spear\Silex\Application\AbstractApplication;
use Symfony\Component\HttpFoundation\Request;
use Assmat\DataSource\Repositories;
use Assmat\Services;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Assmat\Services\Form;
use Assmat\DataSource\Forms;
use Symfony\Component\Translation\TranslatorInterface;

class Application extends AbstractApplication
{
    protected function initializeServices()
    {
        $this->configureTwig();
        $this->configureMailer();
        $this->initializeRepositories();
        $this->initializeSecurity();
        $this->initializeForms();

        $this['bulletin.builder.fromEvenements'] = function() {
            return new Services\Bulletin\Builders\FromEvenements($this['repository.ligneTemplate'], $this['repository.congePaye'], $this['provider.ligneBuilder']);
        };

        $this['bulletin.builder.fromLignes'] = function() {
            return new Services\Bulletin\Builders\FromLignes($this['repository.ligne'], $this['provider.ligneBuilder']);
        };

        $this['provider.ligneBuilder'] = $this->share(function() {
            return new Services\Providers\LigneBuilder();
        });
    }

    protected function registerProviders()
    {
        $this['documentRoot.path'] = $this['root.path'] . 'web' . DIRECTORY_SEPARATOR;

        $this->register(new SilexProvider\SessionServiceProvider());
        $this->register(new SilexProvider\UrlGeneratorServiceProvider());
        $this->register(new SilexProvider\FormServiceProvider());
        $this->register(new SilexProvider\ValidatorServiceProvider());
        $this->register(new SilexProvider\TranslationServiceProvider(), array('locale' => 'fr'));
        $this->register(new SilexProvider\HttpFragmentServiceProvider());
        $this->register(new SilexProvider\SwiftmailerServiceProvider());
        $this->register(new SpearProvider\Twig());
        $this->register(new SpearProvider\AsseticServiceProvider());
        $this->register(new \Assmat\Providers\MysqlDBAL());
        $this->register(new \Assmat\Providers\WebProfiler());
    }

    protected function mountControllerProviders()
    {
        $this->mount('/', new Controllers\Home\Provider());
        $this->mount('/admin', new Controllers\Admin\Provider());
        $this->mount('/user', new Controllers\User\Provider());
    }

    private function configureTwig()
    {
        $this['twig.path.manager']->addPath(array(
            $this['root.path'] . 'views/',
        ));

        $this['twig.form.templates'] = array(
            'form_div_layout.html.twig',
            'common/form.html.twig',
        );

        $this['translator'] = $this->share($this->extend('translator', function(TranslatorInterface $translator, \Silex\Application $app) {
            $translator->addLoader('yaml', new YamlFileLoader());
            $translator->addResource('yaml', $app['root.path'] . 'src/DataSource/Forms/Labels.yml', 'fr');

            return $translator;
        }));

        $this['twig']->addExtension(new Services\Twig\AdminExtension());
    }

    private function configureMailer()
    {
        $this['swiftmailer.options'] = array(
            'host' => $this['configuration']->read('mailer/host'),
            'port' => $this['configuration']->read('mailer/port'),
        );
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
                    return new Services\Security\UserProvider($this['repository.contact'], $this['configuration']);
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
            return new Repositories\Mysql\Employeur($this['db.default'], $this['repository.contact'], $this['repository.employe.proxy'], $this['repository.contrat.proxy']);
        };

        $this['repository.employe.closure'] = $this->protect(function() {
            return new Repositories\Mysql\Employe($this['db.default'], $this['repository.contact'], $this['repository.contrat.proxy'], $this['repository.employeur']);
        });

        $this['repository.employe'] = function() {
            return $this['repository.employe.closure']();
        };

        $this['repository.employe.proxy'] = function() {
            return new Repositories\Proxy\Employe($this['repository.employe.closure']);
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
            return new Repositories\Mysql\Bulletin($this['db.default'], $this['repository.evenement'], $this['repository.contrat'], $this['repository.ligne'], $this['repository.congePaye']);
        };

        $this['repository.evenement'] = function() {
            return new Repositories\Mysql\Evenement($this['db.default'], $this['repository.evenementType'], $this['repository.contrat']);
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
            return new Repositories\Mysql\Ligne($this['db.default'], $this['repository.ligneTemplate']);
        };

        $this['repository.cpReference'] = function() {
            return new Repositories\Mysql\CpReference($this['db.default'], $this['repository.ligne']);
        };

        $this['repository.congePaye'] = function() {
            return new Repositories\Mysql\CongePaye($this['repository.ligne'], $this['repository.cpReference']);
        };
    }

    private function initializeForms()
    {
        $this['form.contrat'] = function() {
            return new Forms\Contrat($this['repository.employe']);
        };

        $this['form.indemnite'] = function() {
            return new Forms\Indemnite();
        };

        $this['form.profile'] = function() {
            return new Forms\Profile();
        };

        $this['form.contact'] = function() {
            return new Forms\Contact();
        };

        $this['form.password'] = function() {
            return new Forms\Password();
        };

        $this['form.errors'] = function() {
            return new Form\Errors($this['translator']);
        };
    }
}