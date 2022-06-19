<?php declare(strict_types=1);

namespace App\GameOfLife\Helpers;

class Console
{
    public const COLOR_BLACK = 'black';
    public const COLOR_RED = 'red';
    public const COLOR_GREEN = 'green';
    public const COLOR_BLUE = 'blue';
    public const COLOR_WHITE = 'white';
    private const FOREGROUND_COLORS = [
        self::COLOR_BLACK => '0;30',
        self::COLOR_RED => '0;31',
        self::COLOR_GREEN => '0;32',
        self::COLOR_BLUE => '0;34',
        self::COLOR_WHITE => '1;37',
    ];
    private const BACKGROUND_COLORS = [
        self::COLOR_BLACK => '40',
        self::COLOR_RED => '41',
        self::COLOR_GREEN => '42',
        self::COLOR_BLUE => '44',
        self::COLOR_WHITE => '47',
    ];

    public static function output(
        string $text,
        string $foregroundColor = '',
        string $backgroundColor = ''
    ): void {
        $coloredOutput = '';
        if ($foregroundColor && isset(self::FOREGROUND_COLORS[$foregroundColor])) {
            $coloredOutput .= sprintf("\033[%sm", self::FOREGROUND_COLORS[$foregroundColor]);
        }
        if ($backgroundColor && isset(self::BACKGROUND_COLORS[$backgroundColor])) {
            $coloredOutput .= sprintf("\033[%sm", self::BACKGROUND_COLORS[$backgroundColor]);
        }
        echo sprintf("%s%s\033[0m", $coloredOutput, $text);
    }
}
