<?php declare(strict_types=1);

require_once('Autoloader.php');

$autoloader = new Autoloader();
$autoloader->register();
$autoloader->addNamespace('App\GameOfLife', 'app');
