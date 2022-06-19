<?php declare(strict_types=1);

namespace App\GameOfLife;

use App\GameOfLife\Helpers\Arr;
use App\GameOfLife\Helpers\Console;
use SplFileObject;

class Board
{
    private const STATE_DEAD = 0;
    private const STATE_ALIVE = 1;

    /** @var int[][] */
    private array $cells;

    /**
     * @param int[][] $cells
     */
    public function __construct(array $cells)
    {
        $this->cells = $cells;
    }

    public function getWidth(): int
    {
        return count(Arr::first($this->getCells(), []));
    }

    public function getHeight(): int
    {
        return count($this->getCells());
    }

    /**
     * @return int[][]
     */
    public function getCells(): array
    {
        return $this->cells ?? [];
    }

    public function setCellState(int $x, int $y, int $state): void
    {
        if ($x >= 0 && $y >= 0 && $x < $this->getWidth() && $y < $this->getHeight()) {
            $this->cells[$y][$x] = $state;
        }
    }

    public static function generateRandomCells(int $width = 10, int $height = 10, int $maxAliveCells = 10): self
    {
        $cells = [];
        $board = new self($cells);
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $board->setCellState($x, $y, $board->getRandomState($maxAliveCells));
            }
        }
        return $board;
    }

    public static function generateCellsFromTemplate(string $filename): self
    {
        $cells = [];
        if (file_exists($filename)) {
            $file = new SplFileObject($filename);
            $file->setFlags(SplFileObject::READ_CSV);
            foreach ($file as $y => $row) {
                foreach ($row as $x => $cell) {
                    if (is_string($cell)) {
                        $cells[$y][$x] = (int)$cell;
                    }
                }
            }
        }
        return new self($cells);
    }

    private function getRandomState(int $maxAliveCells): int
    {
        if ($this->getAliveCellsCount() < $maxAliveCells) {
            return array_rand([self::STATE_DEAD, self::STATE_ALIVE]);
        }
        return self::STATE_DEAD;
    }

    public function getAliveCellsCount(): int
    {
        $count = 0;
        foreach ($this->getCells() as $row) {
            foreach ($row as $cell) {
                if ($cell === self::STATE_ALIVE) {
                    $count++;
                }
            }
        }
        return $count;
    }

    public function render(): void
    {
        $cells = $this->getCells();
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
                    case self::STATE_DEAD:
                        Console::output($cellBlock, Console::COLOR_BLACK, Console::COLOR_WHITE);
                        break;
                    case self::STATE_ALIVE:
                        Console::output($cellBlock, Console::COLOR_WHITE, Console::COLOR_RED);
                        break;
                    default:
                        // do nothing yet
                }
            }
            echo '|' . $border;
        }
    }

    private function getAliveCellsCountAround(int $x, int $y): int
    {
        $aliveCount = 0;
        for ($nextY = $y - 1; $nextY <= $y + 1; $nextY++) {
            if ($nextY < 0 || $nextY >= $this->getHeight()) {
                continue;
            }

            for ($nextX = $x - 1; $nextX <= $x + 1; $nextX++) {
                if ($nextX < 0 || $nextX >= $this->getWidth()) {
                    continue;
                }
                if ($nextX === $x && $nextY === $y) {
                    continue;
                }

                if ($this->getCellState($nextX, $nextY) === self::STATE_ALIVE) {
                    $aliveCount++;
                }
            }
        }
        return $aliveCount;
    }

    private function getCellState(int $x, int $y): int
    {
        $cellState = 0;
        $row = Arr::get($this->getCells(), $y);
        if ($row) {
            $cellState = (int)Arr::get($row, $x);
        }
        return $cellState;
    }

    public function calculateNextGeneration(): void
    {
        $queueToDie = [];
        $queueToBeBorn = [];
        foreach ($this->getCells() as $y => $row) {
            foreach ($row as $x => $cell) {
                $aliveCellsCountAround = $this->getAliveCellsCountAround($x, $y);
                $currentState = $this->getCellState($x, $y);
                if ($currentState === self::STATE_ALIVE
                    && ($this->isUnderPopulation($aliveCellsCountAround) || $this->isOvercrowd($aliveCellsCountAround))
                ) {
                    $queueToDie[] = [$x, $y];
                } elseif ($currentState === self::STATE_DEAD && $this->isReproducible($aliveCellsCountAround)) {
                    $queueToBeBorn[] = [$x, $y];
                }
            }
        }
        $this->setStateForQueue($queueToDie, self::STATE_DEAD);
        $this->setStateForQueue($queueToBeBorn, self::STATE_ALIVE);
    }

    private function setStateForQueue(array $queue, int $state): void
    {
        foreach ($queue as $coordinates) {
            $this->setCellState(Arr::get($coordinates, 0), Arr::get($coordinates, 1), $state);
        }
    }

    private function isUnderPopulation(int $aliveCellsCountAround): bool
    {
        return $aliveCellsCountAround < 2;
    }

    private function isOvercrowd(int $aliveCellsCountAround): bool
    {
        return $aliveCellsCountAround > 3;
    }

    private function isReproducible(int $aliveCellsCountAround): bool
    {
        return $aliveCellsCountAround === 3;
    }
}
