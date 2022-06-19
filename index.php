<?php declare(strict_types=1);

use App\GameOfLife\Game;

require_once('autoload.php');

$game = Game::makeFromTemplate('templates/version1.csv');
$game->start();
