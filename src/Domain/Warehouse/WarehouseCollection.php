<?php

namespace App\Domain\Warehouse;

use IteratorAggregate;

final class WarehouseCollection implements IteratorAggregate
{

    /**
     * @var Warehouse[]|array
     */
    private $collection;

    private function __construct($collection)
    {
        $this->collection = $collection;
    }

    public static function instanciate(): self
    {
        return new self([]);
    }

    public function add(Warehouse $warehouse): self
    {
        $collection = clone $this->collection;
        $collection[] = $warehouse;

        return new $this($collection);
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->collection);
    }

    public function empty():bool
    {
        return empty($this->collection);
    }
}
