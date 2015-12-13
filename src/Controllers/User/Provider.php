<?php

namespace Assmat\Controllers\User;

use Silex\Application;
use Silex\ControllerProviderInterface;


class Provider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $app['user.controller'] = $app->share(function() use($app) {
            return new Controller(
                $app['twig'],
                $app['request'],
                $app['configuration'],
                $app['url_generator'],
                $app['form.factory'],
                $app['form.contact'],
                $app['form.errors'],
                $app['repository.contact'],
                $app['security.encoder.digest'],
                $app['mailer']
            );
        });

        $controllers = $app['controllers_factory'];

        $controllers->get('/lostpass', 'user.controller:lostpassEditAction')
                    ->bind('user_lostpass_edit');

        $controllers->post('/lostpass', 'user.controller:lostpassSendAction')
                    ->bind('user_lostpass_send');

        $controllers->get('/new', 'user.controller:newAction')
                    ->bind('user_new');

        $controllers->post('/new', 'user.controller:createAction')
                    ->bind('user_create');

        return $controllers;
    }
}