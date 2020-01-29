<?php


namespace App\Domain\Warehouse;

use Webmozart\Assert\Assert;

final class WarehouseId
{

    /**
     * @var int
     */
    private $id;

    private function __construct(int $id)
    {
        $this->id = $id;
        $this->validate();
    }

    public static function create(int $id):WarehouseId
    {
        return new self($id);
    }

    public function getId(): int
    {
        return $this->id;
    }

    private function validate()
    {
        Assert::greaterThan($this->id, 0, 'The ID must be greater than 0.');
    }
}
