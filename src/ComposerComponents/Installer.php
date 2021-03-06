<?php

namespace ComposerComponents;

/**
 *
 * @copyright Copyright (c) 2013 Ledvinka Vít
 * @author Ledvinka Vít, frosty22 <ledvinka.vit@gmail.com>
 *
 */
class Installer extends \Nette\Object {


	/**
	 * Path to the composer lock file
	 * @var string
	 */
	private $composerLockFile;


	/**
	 * Path to the composer json file
	 * @var string
	 */
	private $composerFile;


	/**
	 * Path to the components dir
	 * @var string
	 */
	private $componentsDir;


	/**
	 * Path to the vendor dir
	 * @var string
	 */
	private $vendorDir;


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
	 * @param string $lockFile
	 * @param string $composerFile
	 * @param string $vendorDir
	 * @param string $dir
	 */
	public function __construct($lockFile, $composerFile, $vendorDir, $dir)
	{
		$this->composerLockFile = $lockFile;
		$this->composerFile = $composerFile;
		$this->componentsDir = $dir;
		$this->vendorDir = $vendorDir;
	}


	/**
	 * Get array of depended files
	 * @return string
	 */
	public function getDependencyFiles()
	{
		return array($this->composerLockFile, $this->composerFile);
	}


	/**
	 * Get list of CSS files (absolute path => relative path)
	 * @return array
	 */
	public function getCssFiles()
	{
		$this->run();
		return $this->css;
	}


	/**
	 * Get list of JS files (absolute path => relative path)
	 * @return array
	 */
	public function getJsFiles()
	{
		$this->run();
		return $this->js;
	}


	/**
	 * @throws \Nette\FileNotFoundException
	 * @throws \Nette\DirectoryNotFoundException
	 */
	protected function run()
	{
		if (!file_exists($this->composerLockFile))
			throw new \Nette\FileNotFoundException("Composer lock file '{$this->composerLockFile}' not found.");

		if (!file_exists($this->composerFile))
			throw new \Nette\FileNotFoundException("Composer file '{$this->composerFile}' not found.");

		if (!is_dir($this->componentsDir))
			throw new \Nette\DirectoryNotFoundException("Components dir '{$this->componentsDir}' not found.");

		if (!is_dir($this->vendorDir))
			throw new \Nette\DirectoryNotFoundException("Vendor dir '{$this->vendorDir}' not found.");

		$composer = json_decode(file_get_contents($this->composerLockFile));
		foreach ($composer->packages as $package) {
			$this->parsePackage($this->vendorDir . "/" . $package->name, $package);
		}

		$composer = json_decode(file_get_contents($this->composerFile));
		$this->parsePackage(dirname($this->composerFile), $composer);

		$this->css = $this->removeDuplicates($this->css);
		$this->js = $this->removeDuplicates($this->js);
	}


	/**
	 * @param string $path
	 * @param string $package
	 * @throws \Nette\DirectoryNotFoundException
	 */
	protected function parsePackage($path, $package)
	{
		if (isset($package->extra) && isset($package->extra->component)) {

			if (!is_dir($path))
				throw new \Nette\DirectoryNotFoundException("Package dir '{$path}' not found.");

			$path = isset($package->extra->component->src) ? $path . "/" . $package->extra->component->src : $path . "/";
			if (!is_dir($path))
				throw new \Nette\DirectoryNotFoundException("Package component's dir '{$path}' not found.");

			if (isset($package->extra->component->styles))
				$this->css += $this->processFiles($path, $package->extra->component->styles);

			if (isset($package->extra->component->scripts))
				$this->js += $this->processFiles($path, $package->extra->component->scripts);

			if (isset($package->extra->component->files))
				$this->processFiles($path, $package->extra->component->files);

		}
	}


	/**
	 * Process files
	 * @param string $path
	 * @param array $files
	 * @return array
	 * @throws \Nette\FileNotFoundException
	 */
	protected function processFiles($path, array $files)
	{
		$output = array();

		foreach ($files as $localPath) {
			$absolutePath = $path . $localPath;

			if (strpos($absolutePath, "*")) {
				foreach (glob($absolutePath) as $filename) {

					$localParts = Explode("/", $localPath);
					$absoluteParts = Explode("/", $filename);
					$absolutePath = Implode("/", array_slice($absoluteParts, count($localParts) * -1));
					$localFilename = $absolutePath;

					$output[$filename] = $localFilename;
				}
			} else {
				if (!file_exists($absolutePath))
					throw new \Nette\FileNotFoundException();

				$output[$absolutePath] = $localPath;
			}
		}

		$this->copyFiles($output);
		return $output;
	}


	/**
	 * Remove duplicated files via MD5 hash
	 * @param array $files
	 * @return array
	 */
	protected function removeDuplicates(array $files)
	{
		$clear = array();

		$hashes = array();
		foreach ($files as $absolute => $local) {
			$hash = md5_file($absolute);
			if (!isset($hashes[$hash])) {
				$clear[$absolute] = $local;
				$hashes[$hash] = TRUE;
			}
		}

		return $clear;
	}


	/**
	 * Copy files
	 * @param array $files
	 */
	protected function copyFiles(array $files)
	{
		foreach ($files as $absolute => $local) {
			$dir = mb_substr($local, 0, mb_strrpos($local, "/"));
			@mkdir($this->componentsDir . "/" . $dir, 0777, TRUE);
			copy($absolute, $this->componentsDir . "/" . $local);
		}
	}

}