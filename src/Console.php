<?php

namespace Assmat;

use Spear\Silex\Provider\Commands\AsseticDumper;
use Assmat\Commands\CpReferenceComputer;

class Console
{
    private
        $app,
        $configuration;

    public function __construct(Application $dic)
    {
        $this->configuration = $dic['configuration'];

        $dic['security.firewalls'] = array();

        $this->app = new \Symfony\Component\Console\Application('silex-spear-app');

        $this->app->add(new AsseticDumper($this->configuration, $dic['assetic.dumper'], $dic['assetic.path_to_web']));
        $this->app->add(new CpReferenceComputer($dic['db.default'], $dic['repository.cpReference']));
    }

    public function run()
    {
        $this->app->run();
    }
}