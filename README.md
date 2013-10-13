# Nette composer components installer

Podpora instalace komponent - JS, CSS a obrázků pomocí composeru. Princip je triviální - komponenta je závislá na generovaném souboru **composer.lock**, který vždy projde při změně a podívá se po definici **extra.component** viz příklad níže (syntaxe je inspirovaná komplexnějším systémem Bower, pouze ho velice zjednodušuje a funguje jako triviální náhrada).

Všechny soubory komponent zkopíruje do definované složky (defaultně www/components). Dále definované soubory CSS, JS lze přes nette komponentu ComposerComponents/Control/Control vygenerovat v HTML šabloně.


## Příklad rozšíření v composer

Podstatná je pouze ta část **extra.component**, zde je možné definovat základní složku **src**. Dále je zde možné definovat JavaScript soubory v **scripts**, poté CSS soubory vně **styles**, a dále související soubory, například obrázkyve **files**.

```json
{
	"name": "frosty22/xxx",
	"type": "library",
	"require": {
		"php": ">= 5.3.0",
		"nette/nette": "2.*"
	},
	"autoload": {
		"psr-0": {
			"XXX" : "src/"
		}
	},
	"extra": {
		"component" : {
			"src" : "src/",
			"scripts" : [
					"js/test1.js",
					"js/test2.js"
			],
			"styles" : [
					"css/test1.css",
					"css/test2.css"
			],
			"files" : [
					"img/*"
			]
		}
	}
}

```


## Příklad definice komponenty

```php
class BasePresenter extends Nette\UI\Presenter {

	/** @var \Composer\Components\Control\Control $componentsControl */
	protected $componentsControl;

	/** @param \Composer\Components\Control\Control $componentsControl */
	public function injectComponentsControl(\Composer\Components\Control\Control $componentsControl)
	{
		$this->componentsControl = $componentsControl;
	}

	/** @return \Composer\Components\Control\Control */
	protected function createComponentComponents($name, \Composer\Components\Control\Control $control)
	{
		return $control;
	}

}
```


## Příklad v šabloně

```html
<html>
	<head>
		{control components}
	</head>
	<body>
		...
	</body>
</html>
```


**Vykreslí podle příkladu výše:**

```html
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="/components/test1.css?1380013683" />
		<link rel="stylesheet" type="text/css" href="/components/test2.css?1380013683" />

		<script type="text/javascript" src="/components/test1.js?1380013683"></script>
		<script type="text/javascript" src="/components/test2.js?1380013683"></script>
	</head>
	<body>
		...
	</body>
</html>
```


**Vykreslení odděleně**

```html
<html>
	<head>
		{control components:css}
	</head>
	<body>
		...
		{control components:js}
	</body>
</html>
```
