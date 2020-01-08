<?php


namespace App\Domain\Product;

final class Counter
{

    /**
     * @var int
     */
    private $value;

    /**
     * @param int $value
     */
    public function __construct(int $value)
    {
        $this->value = $value;
    }

    /**
     * @return Counter
     */
    public static function start()
    {
        return new self(0);
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * Applies the number of modifications.
     *
     * @param int $numberOfModifications
     *
     * @return Counter
     */
    public function apply(int $numberOfModifications)
    {
        return new $this($this->value + $numberOfModifications);
    }
}
