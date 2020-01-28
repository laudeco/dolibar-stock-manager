<?php


namespace App\Domain\Product;

use Webmozart\Assert\Assert;

final class Serial
{
    /**
     * @var string
     */
    private $value;

    private function __construct(string $value)
    {
        $this->value = $value;
        $this->validate();
    }

    public static function initialize(string $value): Serial
    {
        return new self($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    private function validate()
    {
        Assert::stringNotEmpty($this->value);
    }
}
