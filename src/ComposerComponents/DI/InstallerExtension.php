<?php

namespace Composer\Components\DI;

use Nette\Config\CompilerExtension;

/**
 *
 * @copyright Copyright (c) 2013 Ledvinka Vít
 * @author Ledvinka Vít, frosty22 <ledvinka.vit@gmail.com>
 *
 */
class InstallerExtension extends CompilerExtension
{


	/**
	 * @var array
	 */
	private $defaults = array(
		"lockFile" 		=> "%appDir%/../composer.lock",
		"dir"			=> "%wwwDir%/components",
		"uri"			=> "/components",
		"vendor"		=> "%appDir%/../vendor"
	);


	/**
	 * Base configuration
	 */
	public function loadConfiguration()
	{
		$config = $this->getConfig($this->defaults);
		$builder = $this->getContainerBuilder();

		$cache = new \Nette\DI\Statement('Nette\Caching\Cache', array(1 => "composer-compiler"));

		$installer = $builder->addDefinition($this->prefix('installer'))
			->setClass('Composer\Components\Installer', array($config["lockFile"], $config["vendor"], $config["dir"]));

		$builder->addDefinition($this->prefix('manager'))
			->setClass('Composer\Components\Manager', array($installer, $cache, $config["uri"]));

		$builder->addDefinition($this->prefix('control'))
			->setClass('Composer\Components\Control\Control');

	}

}