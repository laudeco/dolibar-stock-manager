<?php

namespace App\Tests\Query;

use App\Query\GetInventoryCheckProductsQuery;
use App\Query\GetInventoryCheckProductsQueryHandler;
use App\Repository\Dolibarr\ProductRepository;
use App\ViewModel\Product;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class GetInventoryCheckProductsQueryHandlerTest extends TestCase
{

    /**
     * @var ProductRepository|MockObject
     */
    private $doliProductRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->doliProductRepository = $this->createMock(ProductRepository::class);
    }

    private function handler():GetInventoryCheckProductsQueryHandler
    {
        return new GetInventoryCheckProductsQueryHandler($this->doliProductRepository);
    }

    private function query()
    {
        return new GetInventoryCheckProductsQuery(1, 20);
    }

    /**
     * @test
     */
    public function invoke_WithProductNotFound_InventoryProductNotFound()
    {
        $this->doliProductRepository
            ->expects($this->once())
            ->method('getById')
            ->willThrowException(new NotFoundHttpException());

        $inventoryProduct = $this->handler()->handle($this->query());

        $this->assertFalse($inventoryProduct->isFound());
        $this->assertEquals(20, $inventoryProduct->getId());
        $this->assertNull($inventoryProduct->getLabel());
        $this->assertNull($inventoryProduct->getBarcode());
    }

    /**
     * @test
     */
    public function invoke_WithProduct_InventoryProduct()
    {
        $product = new Product();
        $product->setStock(100);
        $product->setLabel('Test');
        $product->setId(20);
        $product->setSerialNumberable(false);
        $product->setBarcode('123');

        $this->doliProductRepository
            ->expects($this->once())
            ->method('getById')
            ->with(20)
            ->willReturn($product);

        $inventoryProduct = $this->handler()->handle($this->query());

        $this->assertTrue($inventoryProduct->isFound());
        $this->assertEquals(20, $inventoryProduct->getId());
        $this->assertEquals('Test', $inventoryProduct->getLabel());
        $this->assertEquals('123', $inventoryProduct->getBarcode());
    }

    /**
     * @test
     */
    public function invoke_WithProductNotSerialNumberable_Null()
    {
        $product = new Product();
        $product->setStock(100);
        $product->setLabel('Test');
        $product->setId(20);
        $product->setSerialNumberable(true);
        $product->setBarcode('123');

        $this->doliProductRepository
            ->expects($this->once())
            ->method('getById')
            ->with(20)
            ->willReturn($product);

        $inventoryProduct = $this->handler()->handle($this->query());

        $this->assertNull($inventoryProduct);
    }
}
