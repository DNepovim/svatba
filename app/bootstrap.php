<?php

require __DIR__ . '/../vendor/autoload.php';

// Simple debug methods
//\Nette\Diagnostics\Debugger::$maxDepth = 6;
//\Nette\Diagnostics\Debugger::$maxLen= 1000;

function dd($val, $exit = NULL) {
	\Tracy\Debugger::dump($val);

	if ($exit !== NULL) {
		exit;
	}
}

function cdd($val, $exit = NULL) {
	\Tracy\Debugger::barDump($val);

	if ($exit !== NULL) {
		exit;
	}
}


$configurator = new Nette\Configurator;

$configurator->setDebugMode(true); // enable for your remote IP
$configurator->enableDebugger(__DIR__ . '/../log');

$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');
$configurator->addConfig(__DIR__ . '/config/config.local.neon');

$container = $configurator->createContainer();

return $container;
