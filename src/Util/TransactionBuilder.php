<?php


namespace App\Util;

use App\Query\GetProductByBarcodeQuery;
use App\Query\QueryHandlerInterface;
use App\ViewModel\Product;
use App\ViewModel\StockMovement;
use App\ViewModel\Transaction;
use Symfony\Component\HttpFoundation\ParameterBag;
use Webmozart\Assert\Assert;

final class TransactionBuilder
{

    /**
     * @var QueryHandlerInterface
     */
    private $productQueryHandler;

    /**
     * @var string
     */
    private $defaultTransactionLabel;

    /**
     * @var Product[]|array
     */
    private $products;

    public function __construct(QueryHandlerInterface $productQueryHandler, string $defaultTransactionLabel)
    {
        $this->productQueryHandler = $productQueryHandler;
        $this->defaultTransactionLabel = $defaultTransactionLabel;
        $this->products = [];
    }

    /**
     * @param ParameterBag $request
     *
     * @return Transaction
     *
     * @throws \Exception
     */
    public function fromRequest(ParameterBag $request): Transaction
    {
        $barcodes = $request->get('barcode', []);
        $qty = $request->get('qty', []);
        $serials = $request->get('serial', []);
        $dlc = $request->get('dlc', []);
        $warehouses = $request->get('warehouses', []);
        $label = $this->cleanLabel($request->get('label', ''));

        Assert::notEmpty($barcodes);
        Assert::allCount([$qty, $serials, $dlc, $warehouses], count($barcodes), 'All count are not equals.');

        $transaction = Transaction::create($label);

        $i = 0;
        foreach ($barcodes as $currentBarcode) {
            $product = $this->searchProduct($currentBarcode);
            $warehouseId = (int)$warehouses[$i];
            $quantity = (int)$qty[$i];
            $serial = $serials[$i];
            $dlcDate = !empty($dlc[$i]) ? new \DateTimeImmutable($dlc[$i]) : null;

            $movement = $this->createMovement($product, $warehouseId, $currentBarcode, $quantity, $serial, $dlcDate);

            $transaction = $transaction->addMovement($movement);
            $i++;
        }

        return $transaction;
    }

    private function cleanLabel(string $label):string
    {
        if (!empty($label)) {
            return $label;
        }

        return $this->defaultTransactionLabel;
    }

    private function searchProduct(string $barcode):Product
    {
        if (isset($this->products[$barcode])) {
            return $this->products[$barcode];
        }

        $this->products[$barcode] = $this->productQueryHandler->handle(new GetProductByBarcodeQuery($barcode));

        return $this->products[$barcode];
    }

    private function productSupportBatch(Product $product):bool
    {
        return $product->serialNumberable();
    }

    private function createMovement(Product $product, int $warehouseId, $currentBarcode, int $qty, string $serialNumber, \DateTimeImmutable $dlc = null): StockMovement
    {
        if (!$this->productSupportBatch($product)) {
            return StockMovement::move($warehouseId, $product->getLabel(), $currentBarcode, $product->getId(), $qty);
        }

        return StockMovement::batch($warehouseId, $product->getLabel(), $currentBarcode, $product->getId(), $qty, $serialNumber, $dlc);
    }
}
