<?php


namespace App\Controller;

use App\Command\InventoryCommand;
use App\Command\InventoryCommandHandler;
use App\Exception\InventoryCheckRequestedException;
use App\Query\GetProductByBarcodeQuery;
use App\Query\GetProductByBarcodeQueryHandler;
use App\ViewModel\StockMovement;
use App\ViewModel\Transaction;
use Dolibarr\Client\Exception\ApiException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @package App\Controller
 */
final class SubmissionController extends AbstractController
{

    /**
     * @var InventoryCommandHandler
     */
    private $handler;

    /**
     * @var GetProductByBarcodeQueryHandler
     */
    private $productQueryHandler;

    /**
     * @param InventoryCommandHandler         $handler
     * @param GetProductByBarcodeQueryHandler $productQueryHandler
     */
    public function __construct(InventoryCommandHandler $handler, GetProductByBarcodeQueryHandler $productQueryHandler)
    {
        $this->handler = $handler;
        $this->productQueryHandler = $productQueryHandler;
    }

    /**
     * @Route("/process", name="process", methods={"POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $transaction = $this->buildTransaction($request);

        $productRequiresInventoryCheck = [];

        foreach ($transaction->getMovements() as $movement) {
            try {
                $label = $transaction->getLabel();
                if (empty($label)) {
                    $label = 'movement';
                }

                $command = new InventoryCommand($label, $transaction->getDueDate(), 1, $movement->getProductId(), $movement->getQuantity());
                $this->handler->__invoke($command);
            } catch (InventoryCheckRequestedException $e) {
                $productRequiresInventoryCheck[] = $e->getProductId();
            } catch (ApiException $e) {
                throw new HttpException(500);
            }
        }

        if (empty($productRequiresInventoryCheck)) {
            return $this->redirectToRoute('logout');
        }

        return $this->forward(InventoryController::class.'::indexAction', [], ['products' => $productRequiresInventoryCheck]);
    }

    /**
     * @param Request $request
     *
     * @return Transaction
     */
    private function buildTransaction(Request $request): Transaction
    {
        $label = $request->request->get('label', 'Inventory Movement');
        $barcodes = $request->request->get('barcode', []);
        $qty = $request->request->get('qty', []);

        $transaction = new Transaction($label);
        $products = [];
        $i = 0;
        foreach ($barcodes as $currentBarcode) {
            if (!isset($products[$currentBarcode])) {
                try {
                    $product = $this->productQueryHandler->__invoke(new GetProductByBarcodeQuery($currentBarcode));
                    $products[$currentBarcode] = $product->getId();
                } catch (ApiException $e) {
                    throw new HttpException(500);
                }
            }

            $transaction->add(StockMovement::move($currentBarcode, $products[$currentBarcode], $qty[$i]));
            $i++;
        }

        return $transaction;
    }
}
