<?php


namespace App\Domain\Product;

use Webmozart\Assert\Assert;

final class Quantity
{
    /**
     * @var int
     */
    private $value;

    private function __construct(int $value)
    {
        $this->value = $value;
        $this->validate();
    }

    public static function create(int $value):Quantity
    {
        return new self($value);
    }

    public function getValue(): int
    {
        return $this->value;
    }

    private function validate()
    {
        Assert::notEq($this->value, 0);
    }
}
