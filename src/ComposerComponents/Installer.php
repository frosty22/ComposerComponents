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
	 * @param string $vendorDir
	 * @param string $dir
	 */
	public function __construct($lockFile, $vendorDir, $dir)
	{
		$this->composerLockFile = $lockFile;
		$this->componentsDir = $dir;
		$this->vendorDir = $vendorDir;
	}


	/**
	 * Get composer lock filename
	 * @return string
	 */
	public function getComposerLockFile()
	{
		return $this->composerLockFile;
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
			throw new \Nette\FileNotFoundException("Composer file '{$this->composerLockFile}' not found.");

		if (!is_dir($this->componentsDir))
			throw new \Nette\DirectoryNotFoundException("Components dir '{$this->componentsDir}' not found.");

		if (!is_dir($this->vendorDir))
			throw new \Nette\DirectoryNotFoundException("Vendor dir '{$this->vendorDir}' not found.");

		$composer = json_decode(file_get_contents($this->composerLockFile));
		foreach ($composer->packages as $package) {
			if (isset($package->extra) && isset($package->extra->component)) {

				$path = $this->vendorDir . "/" . $package->name;
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

		$this->css = $this->removeDuplicates($this->css);
		$this->js = $this->removeDuplicates($this->js);
	}


	/**
	 * Process files
	 * @param string $path
	 * @param array $files
	 * @return array
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