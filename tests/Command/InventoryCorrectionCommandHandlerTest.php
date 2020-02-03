<?php

namespace App\Tests\Command;

use App\Command\InventoryCorrectionCommand;
use App\Command\InventoryCorrectionCommandHandler;
use App\Domain\Product\Counter;
use App\Domain\Product\ProductId;
use App\Repository\Dolibarr\ProductRepository as DolibarrProductRepository;
use App\Repository\Dolibarr\StockMovementRepository;
use App\Repository\ProductRepository;
use App\ViewModel\Product;
use Dolibarr\Client\Domain\StockMovement\StockMovement;
use Dolibarr\Client\Domain\StockMovement\StockMovementId;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class InventoryCorrectionCommandHandlerTest extends TestCase
{

    /**
     * @var StockMovementRepository|MockObject
     */
    private $stockMovementRepository;

    /**
     * @var ProductRepository|MockObject
     */
    private $productRepository;

    /**
     * @var DolibarrProductRepository|MockObject
     */
    private $dolibarrProductRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stockMovementRepository = $this->createMock(StockMovementRepository::class);
        $this->dolibarrProductRepository = $this->createMock(DolibarrProductRepository::class);
        $this->productRepository = $this->createMock(ProductRepository::class);
    }

    private function create():InventoryCorrectionCommandHandler
    {
        return new InventoryCorrectionCommandHandler(
            $this->stockMovementRepository,
            $this->dolibarrProductRepository,
            $this->productRepository
        );
    }

    private function command(): InventoryCorrectionCommand
    {
        return new InventoryCorrectionCommand('Correction', new \DateTimeImmutable('2020-01-01'), 1, 2, 300);
    }

    private function product(int $stockQuantity, bool $batchable = false): Product
    {
        $viewProduct = new Product();

        $viewProduct->setLabel('label for test');
        $viewProduct->setBarcode('87644');
        $viewProduct->setId(2);
        $viewProduct->setStock($stockQuantity);
        $viewProduct->setSerialNumberable($batchable);

        return $viewProduct;
    }

    /**
     * @test
     */
    public function invoke_WithEqualThanExpectation_DoNothing()
    {
        $this->dolibarrProductRepository
            ->expects($this->once())
            ->method('getById')
            ->willReturn($this->product(300));

        $this->create()->__invoke($this->command());
    }

    /**
     * @test
     */
    public function invoke_WithBatchProduct_DoNothing()
    {
        $this->dolibarrProductRepository
            ->expects($this->once())
            ->method('getById')
            ->willReturn($this->product(300, true));

        $this->create()->__invoke($this->command());
    }

    /**
     * @test
     */
    public function invoke_WithChanges_Change()
    {
        $this->dolibarrProductRepository
            ->expects($this->once())
            ->method('getById')
            ->willReturn($this->product(1000));

        $dolibarrMovement = new StockMovement();
        $dolibarrMovement->setProductId(2);
        $dolibarrMovement->setWarehouseId(1);
        $dolibarrMovement->setQuantity(-700);
        $dolibarrMovement->setLabel('Correction');
        $dolibarrMovement->setInventoryCode('2020-01-01T00:00:00+00:00');

        $this->stockMovementRepository
            ->expects($this->once())
            ->method('save')
            ->with($dolibarrMovement)
            ->willReturn(new StockMovementId(1));

        $this->productRepository
            ->expects($this->once())
            ->method('save')
            ->with(new \App\Domain\Product\Product(new ProductId(2), Counter::start()));

        $this->create()->__invoke($this->command());
    }
}
