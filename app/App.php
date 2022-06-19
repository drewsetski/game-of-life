<?php declare(strict_types=1);

namespace App\GameOfLife;

use App\GameOfLife\Helpers\Arr;

class App
{
    public static function init(): void
    {
        $options = getopt('', [
            'template::',
            'random::',
        ]);
        $template = (string)Arr::get($options, 'template');
        $shouldRunRandom = array_key_exists('random', $options);

        if ($shouldRunRandom) {
            $game = Game::makeWithRandomBoard();
        } elseif ($template) {
            $game = Game::makeFromTemplate($template);
        } else {
            echo 'One of required parameters are missing: --template, --random' . PHP_EOL;
            return;
        }

        $game->start();
    }
}
