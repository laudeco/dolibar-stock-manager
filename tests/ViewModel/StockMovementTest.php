<?php

namespace App\Tests\ViewModel;

use App\ViewModel\StockMovement;
use PHPUnit\Framework\TestCase;

final class StockMovementTest extends TestCase
{
    private $labelValid = 'Label';
    private $labelinvalid = [''];

    private $warehouseValid = 1;
    private $warehouseInvalid = [0, -1];

    private $productValid = 1;
    private $productInvalid = [0, -1];

    private $quantityValid = [10, -10];
    private $quantityInvalid = [0];

    private $barcodeValid = 'ba9c22c8-f780-474f-8855-0f017dd6565d';
    private $barcodeInvalid = [''];

    private $serialValid = '69032f4a-b833-46c6-aac8-e9fa361b56ed';

    /**
     * @param array $propertyValue
     *
     * @return array
     */
    private function fromInvalidProperty($propertyValue)
    {
        return array_map(function ($val) {
            return [$val];
        }, $propertyValue);
    }

    /**
     * @return array|int[]
     */
    public function invalidWarehouseIds(): array
    {
        return $this->fromInvalidProperty($this->warehouseInvalid);
    }

    /**
     * @return array
     */
    public function invalidLabels(): array
    {
        return $this->fromInvalidProperty($this->labelinvalid);
    }

    /**
     * @return array
     */
    public function invalidBarcodes(): array
    {
        return $this->fromInvalidProperty($this->barcodeInvalid);
    }

    /**
     * @return array
     */
    public function invalidProducts(): array
    {
        return $this->fromInvalidProperty($this->productInvalid);
    }

    /**
     * @return array
     */
    public function invalidQuantities(): array
    {
        return $this->fromInvalidProperty($this->quantityInvalid);
    }

    /**
     * @test
     * @dataProvider invalidWarehouseIds
     *
     * @param $warehouseInvalid
     */
    public function move_InvalidWarehouse_Exception($warehouseInvalid)
    {
        $this->expectException(\InvalidArgumentException::class);
        StockMovement::move($warehouseInvalid, $this->labelValid, $this->barcodeValid, $this->productValid, $this->quantityValid[0]);
    }

    /**
     * @test
     * @dataProvider invalidWarehouseIds
     *
     * @param $warehouseInvalid
     */
    public function batch_InvalidWarehouse_Exception($warehouseInvalid)
    {
        $this->expectException(\InvalidArgumentException::class);
        StockMovement::batch($warehouseInvalid, $this->labelValid, $this->barcodeValid, $this->productValid, $this->quantityValid[0], $this->serialValid);
    }

    /**
     * @test
     * @dataProvider invalidLabels
     *
     * @param $labelInvalid
     */
    public function move_InvalidLabel_Exception($labelInvalid)
    {
        $this->expectException(\InvalidArgumentException::class);
        StockMovement::move($this->warehouseValid, $labelInvalid, $this->barcodeValid, $this->productValid, $this->quantityValid[0]);
    }

    /**
     * @test
     * @dataProvider invalidLabels
     *
     * @param $labelInvalid
     */
    public function batch_InvalidLabel_Exception($labelInvalid)
    {
        $this->expectException(\InvalidArgumentException::class);
        StockMovement::batch($this->warehouseValid, $labelInvalid, $this->barcodeValid, $this->productValid, $this->quantityValid[0], $this->serialValid);
    }

    /**
     * @test
     * @dataProvider invalidBarcodes
     *
     * @param $invalidValue
     */
    public function move_InvalidBarcode_Exception($invalidValue)
    {
        $this->expectException(\InvalidArgumentException::class);
        StockMovement::move($this->warehouseValid, $this->labelValid, $invalidValue, $this->productValid, $this->quantityValid[0]);
    }

    /**
     * @test
     * @dataProvider invalidBarcodes
     *
     * @param $invalidValue
     */
    public function batch_InvalidBarcode_Exception($invalidValue)
    {
        $this->expectException(\InvalidArgumentException::class);
        StockMovement::batch($this->warehouseValid, $this->labelValid, $invalidValue, $this->productValid, $this->quantityValid[0], $this->serialValid);
    }

    /**
     * @test
     * @dataProvider invalidProducts
     *
     * @param $invalidValue
     */
    public function move_InvalidProduct_Exception($invalidValue)
    {
        $this->expectException(\InvalidArgumentException::class);
        StockMovement::move($this->warehouseValid, $this->labelValid, $this->barcodeValid, $invalidValue, $this->quantityValid[0]);
    }

    /**
     * @test
     * @dataProvider invalidProducts
     *
     * @param $invalidValue
     */
    public function batch_InvalidProduct_Exception($invalidValue)
    {
        $this->expectException(\InvalidArgumentException::class);
        StockMovement::batch($this->warehouseValid, $this->labelValid, $this->barcodeValid, $invalidValue, $this->quantityValid[0], $this->serialValid);
    }

    /**
     * @test
     * @dataProvider invalidQuantities
     *
     * @param $invalidValue
     */
    public function move_InvalidPQty_Exception($invalidValue)
    {
        $this->expectException(\InvalidArgumentException::class);
        StockMovement::move($this->warehouseValid, $this->labelValid, $this->barcodeValid, $this->productValid, $invalidValue);
    }

    /**
     * @test
     * @dataProvider invalidQuantities
     *
     * @param $invalidValue
     */
    public function batch_InvalidQty_Exception($invalidValue)
    {
        $this->expectException(\InvalidArgumentException::class);
        StockMovement::batch($this->warehouseValid, $this->labelValid, $this->barcodeValid, $this->productValid, $invalidValue, $this->serialValid);
    }
}
