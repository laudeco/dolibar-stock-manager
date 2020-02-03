<?php


namespace App\Domain\Product;

use DateTimeImmutable;
use Webmozart\Assert\Assert;

final class LimitDate
{

    /**
     * @var DateTimeImmutable
     */
    private $value;

    private function __construct(DateTimeImmutable $value)
    {
        $this->value = $value;
        $this->validate($value);
    }

    public static function fromDate(DateTimeImmutable $value):LimitDate
    {
        return new self($value);
    }

    public function getValue(): DateTimeImmutable
    {
        return $this->value;
    }

    private function validate(DateTimeImmutable $value)
    {
        Assert::true($value > new DateTimeImmutable(''), 'DLC must be greater than now.');
    }
}
