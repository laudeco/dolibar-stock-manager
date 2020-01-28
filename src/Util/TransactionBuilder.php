<?php


namespace App\Util;

use App\Query\GetProductByBarcodeQuery;
use App\Query\GetProductByBarcodeQueryHandler;
use App\Query\QueryHandlerInterface;
use App\ViewModel\Product;
use App\ViewModel\StockMovement;
use App\ViewModel\Transaction;
use Dolibarr\Client\Exception\ApiException;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
        Assert::allCount([$qty, $serials, $dlc, $warehouses], count($barcodes));

        $transaction = new Transaction($label);
        $i = 0;
        foreach ($barcodes as $currentBarcode) {
            $product = $this->searchProduct($currentBarcode);

            if (!$this->productSupportBatch($product)) {
                $transaction->add(StockMovement::move($warehouses[$i], $product->getLabel(), $currentBarcode, $product->getId(), $qty[$i]));
                $i++;

                continue;
            }

            //batch product
            $dlcDate = null;
            if (!empty($dlc[$i])) {
                $dlcDate = new \DateTimeImmutable($dlc[$i]);
            }

            $transaction->add(StockMovement::batch($warehouses[$i], $product->getLabel(), $currentBarcode, $product->getId(), $qty[$i], $serials[$i], $dlcDate));
            $i++;
        }

        return $transaction;
    }

    private function cleanLabel(string $label):string
    {
        if (!$this->isEmpty($label)) {
            return $label;
        }

        return $this->defaultTransactionLabel;
    }

    private function isEmpty(string $label): bool
    {
        return empty($label);
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
}
