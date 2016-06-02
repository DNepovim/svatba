<?php

$container = require __DIR__ . '/../app/bootstrap.php';
$container->getByType('Nette\Application\Application')->run();
$router[] = new Route('dist/thumbnails/<path .+>', 'Palette:Palette:image');
