<?php

namespace App\Tests\Util;

use App\Query\GetProductByBarcodeQuery;
use App\Query\GetProductByBarcodeQueryHandler;
use App\Query\QueryHandlerInterface;
use App\Util\TransactionBuilder;
use App\ViewModel\Product;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;

final class TransactionBuilderTest extends TestCase
{

    /**
     * @var GetProductByBarcodeQueryHandler|MockObject
     */
    private $productQueryHandler;

    public function setUp(): void
    {
        parent::setUp();
        $this->productQueryHandler = $this->createMock(QueryHandlerInterface::class);
    }

    private function create(): TransactionBuilder
    {
        return new TransactionBuilder($this->productQueryHandler, 'Default label for test');
    }

    private function barcodes(): array
    {
        return [
            'f5a9faa5-c779-4a84-ab49-3f8b0d99699e',
            'acc05ab9-c1eb-4bd3-9f2a-cb5b7c5e6a8c',
            'f5a9faa5-c779-4a84-ab49-3f8b0d99699e', // same as first
        ];
    }

    /**
     * @test
     *
     * @throws \Exception
     */
    public function fromRequest_WithEmptyBarcodes_Exception()
    {
        $request = $this->createMock(ParameterBag::class);
        $request
            ->expects($this->exactly(6))
            ->method('get')
            ->withConsecutive(
                ['barcode', []],
                ['qty', []],
                ['serial', []],
                ['dlc', []],
                ['warehouses', []],
                ['label', '']
            )
        ->willReturnOnConsecutiveCalls(
            [],
            [],
            [],
            [],
            [],
            'Label'
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->create()->fromRequest($request);
    }

    /**
     * @test
     *
     * @throws \Exception
     */
    public function fromRequest_WithNotEqualsQuantity_Exception()
    {
        $request = $this->createMock(ParameterBag::class);
        $request
            ->expects($this->exactly(6))
            ->method('get')
            ->withConsecutive(
                ['barcode', []],
                ['qty', []],
                ['serial', []],
                ['dlc', []],
                ['warehouses', []],
                ['label', '']
            )
        ->willReturnOnConsecutiveCalls(
            $this->barcodes(),
            [-100, 100],
            [],
            [],
            [],
            'My label'
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->create()->fromRequest($request);
    }

    /**
     * @test
     *
     * @throws \Exception
     */
    public function fromRequest_WithNotEqualsSerials_Exception()
    {
        $request = $this->createMock(ParameterBag::class);
        $request
            ->expects($this->exactly(6))
            ->method('get')
            ->withConsecutive(
                ['barcode', []],
                ['qty', []],
                ['serial', []],
                ['dlc', []],
                ['warehouses', []],
                ['label', '']
            )
        ->willReturnOnConsecutiveCalls(
            $this->barcodes(),
            [-100, 100, 0],
            ['Serial1', 'Serial2'],
            [],
            [],
            'My label'
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->create()->fromRequest($request);
    }

    /**
     * @test
     *
     * @throws \Exception
     */
    public function fromRequest_WithNotEqualsDlc_Exception()
    {
        $request = $this->createMock(ParameterBag::class);
        $request
            ->expects($this->exactly(6))
            ->method('get')
            ->withConsecutive(
                ['barcode', []],
                ['qty', []],
                ['serial', []],
                ['dlc', []],
                ['warehouses', []],
                ['label', '']
            )
        ->willReturnOnConsecutiveCalls(
            $this->barcodes(),
            [-100, 100, 0],
            ['Serial1', 'Serial2', 'Serial3'],
            [(new \DateTimeImmutable())->add(new \DateInterval('P1Y'))->format('Y-m-d'), (new \DateTimeImmutable())->add(new \DateInterval('P1Y'))->format('Y-m-d')],
            [],
            'My label'
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->create()->fromRequest($request);
    }

    /**
     * @test
     *
     * @throws \Exception
     */
    public function fromRequest_WithNotEqualsWarehouse_Exception()
    {
        $request = $this->createMock(ParameterBag::class);
        $request
            ->expects($this->exactly(6))
            ->method('get')
            ->withConsecutive(
                ['barcode', []],
                ['qty', []],
                ['serial', []],
                ['dlc', []],
                ['warehouses', []],
                ['label', '']
            )
        ->willReturnOnConsecutiveCalls(
            $this->barcodes(),
            [-100, 100, 0],
            ['Serial1', 'Serial2', 'Serial3'],
            [(new \DateTimeImmutable())->add(new \DateInterval('P1Y'))->format('Y-m-d'), (new \DateTimeImmutable())->add(new \DateInterval('P1Y'))->format('Y-m-d'), (new \DateTimeImmutable())->add(new \DateInterval('P1Y'))->format('Y-m-d')],
            [1, 2],
            'My label'
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->create()->fromRequest($request);
    }

    /**
     * @test
     *
     * @throws \Exception
     */
    public function fromRequest_Success()
    {
        $request = $this->createMock(ParameterBag::class);
        $request
            ->expects($this->exactly(6))
            ->method('get')
            ->withConsecutive(
                ['barcode', []],
                ['qty', []],
                ['serial', []],
                ['dlc', []],
                ['warehouses', []],
                ['label', '']
            )
        ->willReturnOnConsecutiveCalls(
            $this->barcodes(),
            [-100, 100, 50],
            ['Serial1', 'Serial2', 'Serial3'],
            ['', (new \DateTimeImmutable())->add(new \DateInterval('P1Y'))->format('Y-m-d'), (new \DateTimeImmutable())->add(new \DateInterval('P1Y'))->format('Y-m-d')],
            [1, 2, 1],
            'My label'
        );

        $this->productQueryHandler
            ->expects($this->exactly(2))
            ->method('handle')
            ->withConsecutive(
                [new GetProductByBarcodeQuery('f5a9faa5-c779-4a84-ab49-3f8b0d99699e')],
                [new GetProductByBarcodeQuery('acc05ab9-c1eb-4bd3-9f2a-cb5b7c5e6a8c')]
            )
            ->willReturnOnConsecutiveCalls(
                $this->product('f5a9faa5-c779-4a84-ab49-3f8b0d99699e', true),
                $this->product('acc05ab9-c1eb-4bd3-9f2a-cb5b7c5e6a8c', false)
            );

        $transaction = $this->create()->fromRequest($request);

        $this->assertEquals('My label', $transaction->getLabel());
        $this->assertCount(3, $transaction);
    }

    private function product(string $barcode, bool $batchSupport): Product
    {
        $prod = new Product();
        $prod->setSerialNumberable($batchSupport);
        $prod->setId(1);
        $prod->setBarcode($barcode);
        $prod->setLabel('Product label');
        $prod->setStock(1000);

        return $prod;
    }
}
