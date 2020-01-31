<?php


namespace App\ViewModel;

use Traversable;

final class Transaction implements \IteratorAggregate, \Countable
{

    /**
     * @var \DateTimeImmutable
     */
    private $dueDate;

    /**
     * @var string
     */
    private $label;

    /**
     * @var array|StockMovement[]
     */
    private $movements;

    private function __construct(string $label, \DateTimeImmutable $dueDate, array $movements = [])
    {
        $this->label = $label;
        $this->movements = $movements;
        $this->dueDate = $dueDate;
    }

    public static function create(string $label):self
    {
        return new self($label, new \DateTimeImmutable());
    }

    public function addMovement(StockMovement $movement):Transaction
    {
        return new $this($this->label, $this->dueDate, array_merge($this->movements, [$movement]));
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDueDate(): \DateTimeImmutable
    {
        return $this->dueDate;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return StockMovement[]|array
     */
    public function getMovements()
    {
        return $this->movements;
    }

    /**
     * @return Traversable|StockMovement[]
     */
    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->movements);
    }

    public function count():int
    {
        return count($this->movements);
    }
}
