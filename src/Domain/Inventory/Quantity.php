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

    /**
     * @param int $value
     */
    private function __construct(int $value)
    {
        $this->value = $value;
    }

    /**
     * @param int $min
     * @param int $max
     *
     * @return Quantity
     */
    public static function random($min, $max)
    {
        try {
            return new self(random_int($min, $max));
        } catch (\Exception $e) {
        }
    }

    /**
     * @param int $quantity
     *
     * @return Quantity
     */
    public static function create(int $quantity)
    {
        return new self($quantity);
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @param int $counter
     *
     * @return bool
     */
    public function isLimitReached(int $counter): bool
    {
        return $counter >= $this->value;
    }
}
