<?php

namespace App\Tests\Application\Inventory;

use App\Application\Inventory\Requester\NoInventoryRequester;
use App\ViewModel\Product;
use PHPUnit\Framework\TestCase;

final class NoInventoryRequesterTest extends TestCase
{

    /**
     * @test
     */
    public function shouldInventoryBeTriggered_AlwaysFalse()
    {
        $this->assertFalse((new NoInventoryRequester())->shouldTriggerInventory($this->createMock(Product::class), 1));
    }
}
