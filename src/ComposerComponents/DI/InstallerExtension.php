<?php

namespace ComposerComponents\DI;

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
		"composerFile"	=> "%appDir%/../composer.json",
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
			->setClass('ComposerComponents\Installer', array($config["lockFile"], $config["composerFile"], $config["vendor"], $config["dir"]));

		$builder->addDefinition($this->prefix('manager'))
			->setClass('ComposerComponents\Manager', array($installer, $cache, $config["uri"]));

		$builder->addDefinition($this->prefix('control'))
			->setClass('ComposerComponents\Control\Control');

	}

}