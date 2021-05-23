<?php

namespace App\Tests\Application\Inventory;

use App\Application\Inventory\Requester\InventoryRequester;
use App\Domain\Inventory\Inventory;
use App\Repository\InventoryRepository;
use App\ViewModel\Product;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

final class InventoryRequesterTest extends TestCase
{
    /**
     * @var InventoryRepository|MockObject
     */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->createMock(InventoryRepository::class);
    }

    private function createRequester(int $min, int $max): InventoryRequester
    {
        return new InventoryRequester(
            $this->repository,
            $min,
            $max
        );
    }

    /**
     * @test
     */
    public function shouldTriggerInventory_WithExistingLimitNotReached_False()
    {
        $this->repository
            ->expects($this->once())
            ->method('getById')
            ->with(42)
            ->willReturn($this->createInventory(false));

        $requester = $this->createRequester(1, 2);

        $this->assertFalse($requester->shouldTriggerInventory($this->createProduct(), 1));
    }

    /**
     * @test
     */
    public function shouldTriggerInventory_WithExistingLimitReached_True()
    {
        $this->repository
            ->expects($this->once())
            ->method('getById')
            ->with(42)
            ->willReturn($this->createInventory(true));

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Inventory::class))
            ->willReturn($this->createInventory(true));

        $requester = $this->createRequester(1, 2);

        $this->assertTrue($requester->shouldTriggerInventory($this->createProduct(), 1));
    }

    /**
     * @test
     */
    public function shouldTriggerInventory_WithNonExisting_createOne()
    {
        $this->repository
            ->expects($this->once())
            ->method('getById')
            ->with(42)
            ->willThrowException(new ResourceNotFoundException());

        $this->repository
            ->expects($this->any())
            ->method('save')
            ->with($this->isInstanceOf(Inventory::class))
            ->willReturn($this->createInventory(false));

        $requester = $this->createRequester(2, 2);

        $this->assertFalse($requester->shouldTriggerInventory($this->createProduct(), 1));
    }

    /**
     * @test
     */
    public function shouldTriggerInventory_WithNonExisting_True()
    {
        $this->repository
            ->expects($this->once())
            ->method('getById')
            ->with(42)
            ->willThrowException(new ResourceNotFoundException());

        $this->repository
            ->expects($this->any())
            ->method('save')
            ->with($this->isInstanceOf(Inventory::class))
            ->willReturn($this->createInventory(true));

        $requester = $this->createRequester(1, 1);

        $this->assertTrue($requester->shouldTriggerInventory($this->createProduct(), 1));
    }


    private function createInventory(bool $reached): Inventory
    {
        $mock = $this->createMock(Inventory::class);

        $mock->expects($this->any())
            ->method('isLimitReached')
            ->willReturn($reached);

        return $mock;
    }

    private function createProduct(): Product
    {
        $mock = $this->createMock(Product::class);

        $mock->expects($this->any())
            ->method('getId')
            ->willReturn(42);

        return $mock;
    }
}
