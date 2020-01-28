<?php


namespace App\Domain\Product;

use Webmozart\Assert\Assert;

final class ProductId
{

    /**
     * @var int
     */
    private $id;

    public function __construct(int $id)
    {
        $this->validate($id);
        $this->id = $id;
    }

    private function validate(int $id)
    {
        Assert::greaterThan($id, 0);
    }

    public function getId(): int
    {
        return $this->id;
    }
}
