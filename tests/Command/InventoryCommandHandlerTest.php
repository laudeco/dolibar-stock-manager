<?php
namespace App\Tests\Command;

use App\Command\InventoryCommand;
use App\Command\InventoryCommandHandler;
use App\Domain\Inventory\Inventory;
use App\Domain\Inventory\Quantity;
use App\Domain\Product\Counter;
use App\Domain\Product\ProductId;
use App\Exception\InventoryCheckRequestedException;
use App\Exception\ProductNotFoundException;
use App\Repository\Dolibarr\ProductRepository as DolibarrProductRepository;
use App\Repository\Dolibarr\StockMovementRepository;
use App\Repository\InventoryRepository;
use App\Repository\ProductRepository;
use App\ViewModel\Product;
use Dolibarr\Client\Domain\StockMovement\StockMovement;
use Dolibarr\Client\Domain\StockMovement\StockMovementId;
use Dolibarr\Client\Exception\ResourceNotFoundException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @package App\Tests\Command
 */
final class InventoryCommandHandlerTest extends TestCase
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
     * @var InventoryRepository|MockObject
     */
    private $inventoryRepository;

    /**
     * @var DolibarrProductRepository|MockObject
     */
    private $dolibarrProductRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stockMovementRepository = $this->createMock(StockMovementRepository::class);
        $this->productRepository = $this->createMock(ProductRepository::class);
        $this->inventoryRepository = $this->createMock(InventoryRepository::class);
        $this->dolibarrProductRepository = $this->createMock(DolibarrProductRepository::class);
    }

    private function create(int $min, int $max):InventoryCommandHandler
    {
        return new InventoryCommandHandler(
            $this->stockMovementRepository,
            $this->productRepository,
            $this->inventoryRepository,
            $this->dolibarrProductRepository,
            $min,
            $max
        );
    }

    private function command(): InventoryCommand
    {
        return new InventoryCommand('hello world', new \DateTimeImmutable('2020-01-01'), 1, 2, 3);
    }

    private function product(bool $serialable): Product
    {
        $viewProduct = new \App\ViewModel\Product();

        $viewProduct->setLabel('label for test');
        $viewProduct->setBarcode('87644');
        $viewProduct->setId(2);
        $viewProduct->setStock(1000);
        $viewProduct->setSerialNumberable($serialable);

        return $viewProduct;
    }

    /**
     * @test
     *
     * @throws \Exception
     */
    public function invoke_WithProductNotFound_ProductNotFoundException()
    {
        $this->dolibarrProductRepository
            ->expects($this->once())
            ->method('getById')
            ->with(2)
            ->willThrowException(new ResourceNotFoundException());

        $this->expectException(ProductNotFoundException::class);

        $this->create(1, 3)->__invoke($this->command());
    }

    /**
     * @test
     *
     * @throws \Exception
     */
    public function invoke_WithCommand_Persist()
    {
        $stockMovement = new StockMovement();
        $stockMovement->setProductId(2);
        $stockMovement->setWarehouseId(1);
        $stockMovement->setQuantity(3);
        $stockMovement->setLabel('hello world');
        $stockMovement->setInventoryCode('2020-01-01T00:00:00+00:00');

        $this->dolibarrProductRepository
            ->expects($this->once())
            ->method('getById')
            ->with(2)
            ->willReturn($this->product(false));

        $this->stockMovementRepository
            ->expects($this->once())
            ->method('save')
            ->with($stockMovement)
            ->willReturn(new StockMovementId(1));

        $this->productRepository
            ->expects($this->once())
            ->method('getById')
            ->willThrowException(new \Symfony\Component\Routing\Exception\ResourceNotFoundException());

        $this->inventoryRepository
            ->expects($this->once())
            ->method('getById')
            ->with(2)
            ->willThrowException(new \Symfony\Component\Routing\Exception\ResourceNotFoundException());

        $this->productRepository
            ->expects($this->once())
            ->method('save')
            ->with(new \App\Domain\Product\Product(new ProductId(2), Counter::start()));

        $this->create(3, 3)->__invoke($this->command());
    }

    /**
     * @test
     *
     * @throws \Exception
     */
    public function invoke_WithCommand_InventoryRequested()
    {
        $stockMovement = new StockMovement();
        $stockMovement->setProductId(2);
        $stockMovement->setWarehouseId(1);
        $stockMovement->setQuantity(3);
        $stockMovement->setLabel('hello world');
        $stockMovement->setInventoryCode('2020-01-01T00:00:00+00:00');

        $this->dolibarrProductRepository
            ->expects($this->once())
            ->method('getById')
            ->with(2)
            ->willReturn($this->product(false));

        $this->stockMovementRepository
            ->expects($this->once())
            ->method('save')
            ->with($stockMovement)
            ->willReturn(new StockMovementId(1));

        $this->productRepository
            ->expects($this->once())
            ->method('getById')
            ->willReturn(new \App\Domain\Product\Product(new ProductId(2), new Counter(1)));

        $this->inventoryRepository
            ->expects($this->once())
            ->method('getById')
            ->with(2)
            ->willReturn(Inventory::forProduct(new \App\Domain\Inventory\Product(2), Quantity::create(2)));

        $this->productRepository
            ->expects($this->once())
            ->method('save')
            ->with(new \App\Domain\Product\Product(new ProductId(2), new Counter(2)));

        $this->expectException(InventoryCheckRequestedException::class);
        $this->create(2, 2)->__invoke($this->command());
    }

    /**
     * @test
     *
     * @throws \Exception
     */
    public function invoke_WithBatch_Persist()
    {
        $command = $this->command();
        $command->batch('Serial007', new \DateTimeImmutable('2020-05-01'));

        $stockMovement = new StockMovement();
        $stockMovement->setProductId(2);
        $stockMovement->setWarehouseId(1);
        $stockMovement->setQuantity(3);
        $stockMovement->setLabel('hello world');
        $stockMovement->setInventoryCode('2020-01-01T00:00:00+00:00');
        $stockMovement->setDlc(new \DateTimeImmutable('2020-05-01'));
        $stockMovement->setLot('Serial007');

        $this->dolibarrProductRepository
            ->expects($this->once())
            ->method('getById')
            ->with(2)
            ->willReturn($this->product(true));

        $this->stockMovementRepository
            ->expects($this->once())
            ->method('save')
            ->with($stockMovement)
            ->willReturn(new StockMovementId(1));

        $this->create(1, 3)->__invoke($command);
    }
}
