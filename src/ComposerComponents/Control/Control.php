<?php

namespace ComposerComponents\Control;

use ComposerComponents\Manager;

/**
 *
 * @copyright Copyright (c) 2013 Ledvinka Vít
 * @author Ledvinka Vít, frosty22 <ledvinka.vit@gmail.com>
 *
 */
class Control extends \Nette\Application\UI\Control {


	/**
	 * @var Manager
	 */
	private $manager;


	/**
	 * @var array
	 */
	private $css;


	/**
	 * @var array
	 */
	private $js;


	/**
	 * @param Manager $manager
	 */
	public function __construct(Manager $manager)
	{
		parent::__construct();
		$this->manager = $manager;
	}


	/**
	 * Render CSS for HTML
	 * @param string|int $file
	 */
	public function renderCss($file = NULL)
	{
		$tpl = $this->createTemplate();
		$tpl->setFile(__DIR__ . "/control.latte");
		$tpl->css = $this->getCss($file);
		$tpl->js = array();
		$tpl->render();
	}


	/**
	 * Render JS for HTML
	 * @param string|int $file
	 */
	public function renderJs($file = NULL)
	{
		$tpl = $this->createTemplate();
		$tpl->setFile(__DIR__ . "/control.latte");
		$tpl->css = array();
		$tpl->js = $this->getJs($file);
		$tpl->render();
	}


	/**
	 * Render CSS & JS for HTML
	 */
	public function render()
	{
		$tpl = $this->createTemplate();
		$tpl->setFile(__DIR__ . "/control.latte");
		$tpl->css = $this->getCss();
		$tpl->js = $this->getJs();
		$tpl->render();
	}


	/**
	 * @param string $file
	 * @return array
	 */
	protected function getCss($file = NULL)
	{
		if (!isset($this->css))
			$this->css = $this->manager->getCssFiles();

		if ($file === NULL) {
			$this->css = array();
			return $this->css;
		}

		if (isset($this->css[$file])) {
			$return = array($file => $this->css[$file]);
			unset($this->css[$file]);
			return $return;
		}

		return array();
	}


	/**
	 * @param string $file
	 * @return array
	 */
	protected function getJs($file = NULL)
	{
		if (!isset($this->js))
			$this->js = $this->manager->getJsFiles();

		if ($file === NULL) {
			$this->js = array();
			return $this->js;
		}

		if (isset($this->js[$file])) {
			$return = array($file => $this->js[$file]);
			unset($this->js[$file]);
			return $return;
		}

		return array();
	}

}