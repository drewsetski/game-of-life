<?php declare(strict_types=1);

namespace App\GameOfLife;

use App\GameOfLife\Helpers\Console;

class Game
{
    private Board $board;
    private int $steps = 1;

    public function __construct(Board $board)
    {
        $this->board = $board;
    }

    private function getBoard(): Board
    {
        return $this->board;
    }

    private function getSteps(): int
    {
        return $this->steps;
    }

    public static function makeWithRandomBoard(): self
    {
        return new self(Board::generateRandomCells());
    }

    public static function makeFromTemplate(string $filename): self
    {
        return new self(Board::generateCellsFromTemplate($filename));
    }

    public function start(): void
    {
        $this->clearConsole();
        while ($this->hasAnyAliveCells()) {
            $this->renderBoard();
            $this->clearConsoleForRender();
            usleep(500000);
            $this->nextStep();
            $this->renderBoard();
            $this->renderStats();
            $this->incrementSteps();
        }
    }

    private function incrementSteps(): void
    {
        $this->steps++;
    }

    private function renderStats(): void
    {
        echo PHP_EOL . PHP_EOL . str_repeat('-', 50);
        echo PHP_EOL . 'Steps: ' . $this->getSteps() . PHP_EOL;
    }

    private function hasAnyAliveCells(): bool
    {
        return $this->getBoard()->getAliveCellsCount() > 0;
    }

    private function renderBoard(): void
    {
        $cells = $this->getBoard()->getCells();
        $border = '';
        echo $border;
        foreach ($cells as $row) {
            if (!$border) {
                $border = PHP_EOL . str_repeat('+---', count($row)) . '+' . PHP_EOL;
                echo $border;
            }
            foreach ($row as $cell) {
                echo '|';
                $cellBlock = '   ';
                switch ($cell) {
                    case Board::STATE_DEAD:
                        Console::output($cellBlock, Console::COLOR_BLACK, Console::COLOR_WHITE);
                        break;
                    case Board::STATE_ALIVE:
                        Console::output($cellBlock, Console::COLOR_WHITE, Console::COLOR_RED);
                        break;
                    default:
                        // do nothing yet
                }
            }
            echo '|' . $border;
        }
    }

    private function nextStep(): void
    {
        $this->getBoard()->calculateNextGeneration();
    }

    private function clearConsole(): void
    {
        echo chr(27) . chr(91) . 'H' . chr(27) . chr(91) . 'J' . PHP_EOL;
    }

    private function clearConsoleForRender(): void
    {
        echo "\033[0;0H";
    }
}
