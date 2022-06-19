<?php declare(strict_types=1);

namespace App\GameOfLife;

class Game
{
    private Board $board;

    public function __construct(Board $board)
    {
        $this->board = $board;
    }

    public function getBoard(): Board
    {
        return $this->board;
    }

    public function setBoard(Board $board): void
    {
        $this->board = $board;
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
        }
    }

    private function hasAnyAliveCells(): bool
    {
        return $this->getBoard()->getAliveCellsCount() > 0;
    }

    private function renderBoard(): void
    {
        $this->getBoard()->render();
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

    public static function makeWithRandomBoard(): self
    {
        return new self(Board::generateRandomCells());
    }

    public static function makeFromTemplate(string $filename): self
    {
        return new self(Board::generateCellsFromTemplate($filename));
    }
}
