<?php

namespace ComposerComponents;

/**
 *
 * @copyright Copyright (c) 2013 Ledvinka Vít
 * @author Ledvinka Vít, frosty22 <ledvinka.vit@gmail.com>
 *
 */
class Manager extends \Nette\Object {


	/**
	 * @var Installer
	 */
	private $installer;


	/**
	 * @var \Nette\Caching\Cache
	 */
	private $cache;


	/**
	 * List of JavaScript files
	 * @var array
	 */
	private $js = array();


	/**
	 * List of CSS files
	 * @var array
	 */
	private $css = array();


	/**
	 * URI to the components dir
	 * @var string
	 */
	private $componentsUri;


	/**
	 * Is inited?
	 * @var bool
	 */
	private $inited = FALSE;


	/**
	 * @param Installer $installer
	 * @param \Nette\Caching\Cache $cache
	 * @param string $uri
	 */
	public function __construct(Installer $installer, \Nette\Caching\Cache $cache, $uri)
	{
		$this->installer = $installer;
		$this->cache = $cache;
		$this->componentsUri = $uri;
	}


	/**
	 * Get list of CSS files
	 * @return array
	 */
	public function getCssFiles()
	{
		$this->init();
		return $this->css;
	}


	/**
	 * Get list of JS files
	 * @return array
	 */
	public function getJsFiles()
	{
		$this->init();
		return $this->js;
	}


	/**
	 * Initialize load files
	 */
	protected function init()
	{
		if ($this->inited) return;

		$files = $this->cache->load("files");
		if ($files === NULL) {

			$files = array(
				"css"	=> $this->convertPath($this->installer->getCssFiles()),
				"js"	=> $this->convertPath($this->installer->getJsFiles())
			);


			$this->cache->save("files", $files, array(
				\Nette\Caching\Cache::FILES => $this->installer->getDependencyFiles()
			));
		}

		$this->css = $files["css"];
		$this->js = $files["js"];
		$this->inited = TRUE;
	}


	/**
	 * Convert paths from absolute to the URIs with modified parameter
	 * @param array $files
	 * @return array
	 */
	private function convertPath(array $files)
	{
		$result = array();
		foreach ($files as $path => $uri) {
			$modified = filemtime($path);
			$result[$uri] = $this->componentsUri . "/" . $uri . "?" . $modified;
		}
		return $result;
	}

}