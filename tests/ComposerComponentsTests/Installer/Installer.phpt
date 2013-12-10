<?php

require __DIR__ . "/../../bootstrap.php";

$dir =  __DIR__ . "/components";
rrmdir($dir);
mkdir($dir);

$installer = new \ComposerComponents\Installer(__DIR__ . "/source/lockfile.1.json", __DIR__ . "/source/composer.1.json", __DIR__, $dir);

$css = $installer->getCssFiles();
Tester\Assert::equal(array(
	realpath(__DIR__ . "/package/sample/src/css/test1.css") => "css/test1.css",
	realpath(__DIR__ . "/package/sample/src/css/test2.css") => "css/test2.css",
	realpath(__DIR__ . "/package/sample/src/css/test3.css") => "css/test3.css"
), $css);

$js = $installer->getJsFiles();
Tester\Assert::equal(array(
	realpath(__DIR__ . "/package/sample/src/js/test1.js") => "js/test1.js",
	realpath(__DIR__ . "/package/sample/src/js/test2.js") => "js/test2.js",
	realpath(__DIR__ . "/package/sample/src/js/test3.js") => "js/test3.js",
	realpath(__DIR__ . "/source/own/js/foo.js")			  => "js/foo.js"
), $js);

Tester\Assert::true(file_exists(__DIR__ . "/components/css/test1.css"));
Tester\Assert::true(file_exists(__DIR__ . "/components/img/foo.txt"));
Tester\Assert::true(file_exists(__DIR__ . "/components/img/bar.txt"));
Tester\Assert::true(file_exists(__DIR__ . "/components/js/foo.js"));