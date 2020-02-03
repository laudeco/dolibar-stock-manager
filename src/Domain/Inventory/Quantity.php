<?php


namespace App\Domain\Inventory;

/**
 * The quantity before the next inventory.
 *
 * @package App\Domain\Inventory
 */
final class Quantity
{

    /**
     * @var int
     */
    private $value;

    private function __construct(int $value)
    {
        $this->value = $value;
    }

    public static function random(int $min, int $max): self
    {
        try {
            return new self(random_int($min, $max));
        } catch (\Exception $e) {
            return new self(1);
        }
    }

    public static function create(int $quantity): self
    {
        return new self($quantity);
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function isLimitReached(int $counter): bool
    {
        return $counter >= $this->value;
    }
}
