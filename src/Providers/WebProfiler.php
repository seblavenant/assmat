<?php

namespace Assmat\Providers;

use Silex\ServiceProviderInterface;
use Silex\Application;
use Assmat\Services;
use Silex\Provider as SilexProvider;

class WebProfiler implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $this->registerWebProfiler($app);
    }

    private function registerWebProfiler(Application $app)
    {
        if((bool) $app['configuration']->read('app/webProfiler') === false)
        {
            return;
        }

        $app->register(new SilexProvider\WebProfilerServiceProvider(), array(
            'profiler.cache_dir' => __DIR__ . '/../../cache/profiler',
        ));
        $app['mysql.collector'] = $app->share(function() {
            return new Services\WebProfiler\Collectors\Mysql();
        });
        $app['dispatcher']->addSubscriber(new Services\Subscribers\WebProfiler\Collectors\Mysql($app['mysql.collector']));
        $app['data_collector.templates'] = array_merge($app['data_collector.templates'], array(array('mysql', 'toolbar/mysql.html.twig')));
        $app->extend('data_collectors', function($collectors) use($app){
            $collectors['mysql'] = function() use($app) { return $app['mysql.collector']; };
            return $collectors;
        });
    }

    public function boot(Application $app)
    {
    }
}
