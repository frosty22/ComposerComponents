<?php

namespace Composer\Components\Control;

use Composer\Components\Manager;

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
	 * @param Manager $manager
	 */
	public function __construct(Manager $manager)
	{
		parent::__construct();
		$this->manager = $manager;
	}


	/**
	 * Render CSS for HTML
	 */
	public function renderCss()
	{
		$tpl = $this->createTemplate();
		$tpl->setFile(__DIR__ . "/control.latte");
		$tpl->css = $this->manager->getCssFiles();
		$tpl->js = array();
		$tpl->render();
	}


	/**
	 * Render JS for HTML
	 */
	public function renderJs()
	{
		$tpl = $this->createTemplate();
		$tpl->setFile(__DIR__ . "/control.latte");
		$tpl->css = array();
		$tpl->js = $this->manager->getJsFiles();
		$tpl->render();
	}


	/**
	 * Render CSS & JS for HTML
	 */
	public function render()
	{
		$tpl = $this->createTemplate();
		$tpl->setFile(__DIR__ . "/control.latte");
		$tpl->css = $this->manager->getCssFiles();
		$tpl->js = $this->manager->getJsFiles();
		$tpl->render();
	}

}