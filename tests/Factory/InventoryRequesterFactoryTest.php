<?php

namespace App\Tests\Factory;

use App\Application\Inventory\Requester\InventoryRequesterInterface;
use App\Application\Inventory\Requester\NoInventoryRequester;
use App\Factory\InventoryRequesterFactory;
use App\Tests\data\FakeInventoryRequester;
use PHPUnit\Framework\TestCase;

final class InventoryRequesterFactoryTest extends TestCase
{

    /**
     * @test
     */
    public function create_WithoutInventory_NoInventoryRequester()
    {
        $factory = new InventoryRequesterFactory(false, $this->createMock(InventoryRequesterInterface::class));
        $this->assertInstanceOf(NoInventoryRequester::class, $factory->create());
    }

    /**
     * @test
     */
    public function create_WithInventory_DefaultIsReturned()
    {
        $factory = new InventoryRequesterFactory(true, new FakeInventoryRequester());
        $this->assertInstanceOf(FakeInventoryRequester::class, $factory->create());
    }
}
