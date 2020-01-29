<?php


namespace App\Domain\Warehouse;

use Webmozart\Assert\Assert;

final class WarehouseName
{

    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     */
    private function __construct(string $name)
    {
        $this->validate($name);
        $this->name = $name;
    }

    private function validate(string $name)
    {
        Assert::stringNotEmpty($name, 'Name cannot be empty');
    }

    public static function name(string $name): self
    {
        return new self($name);
    }

    public function getName(): string
    {
        return $this->name;
    }
}
