<?php

namespace Assmat\Services\Twig;

class AdminExtension extends \Twig_Extension
{
    public function getName()
    {
        return 'admin_extension';
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('md5', array($this, 'md5')),
            new \Twig_SimpleFunction('formatDecimalToHour', array($this, 'formatDecimalToHour')),
        );
    }

    public function md5($value)
    {
        return md5($value);
    }

    public function formatDecimalToHour($hour)
    {
        return date("H\hi", mktime(0, 0, 0) + ($hour * 3600));
    }
}