<?php

namespace Assmat\Services\Twig;

use Symfony\Component\HttpFoundation\Request;

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
		);
	}

	public function md5($value)
	{
		return md5($value);
	}
}