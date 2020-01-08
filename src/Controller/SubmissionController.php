<?php


namespace App\Controller;

use App\Command\InventoryCommand;
use App\Command\InventoryCommandHandler;
use App\Command\InventoryCorrectionCommand;
use App\Command\InventoryCorrectionCommandHandler;
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
     * @var InventoryCorrectionCommandHandler
     */
    private $inventoryCorrectionHandler;

    /**
     * @var GetProductByBarcodeQueryHandler
     */
    private $productQueryHandler;

    /**
     * @param InventoryCommandHandler           $handler
     * @param InventoryCorrectionCommandHandler $inventoryCorrectionHandler
     * @param GetProductByBarcodeQueryHandler   $productQueryHandler
     */
    public function __construct(
        InventoryCommandHandler $handler,
        InventoryCorrectionCommandHandler $inventoryCorrectionHandler,
        GetProductByBarcodeQueryHandler $productQueryHandler
    ) {
        $this->handler = $handler;
        $this->inventoryCorrectionHandler = $inventoryCorrectionHandler;
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
        $transaction = $this->buildTransaction($request, $this->getParameter('app_mouvement_label'));
        $productRequiresInventoryCheck = $this->handleTransaction($transaction);

        if (empty($productRequiresInventoryCheck)) {
            return $this->redirectToRoute('logout');
        }

        return $this->forward(InventoryController::class.'::indexAction', [], ['products' => $productRequiresInventoryCheck]);
    }

    /**
     * @Route("/inventory-check", name="inventory-check", methods={"POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function handleInventoryAction(Request $request)
    {
        $transaction = $this->buildTransaction($request, $this->getParameter('app_inventory_label'));

        foreach ($transaction->getMovements() as $movement) {
            try {
                $command = new InventoryCorrectionCommand($transaction->getLabel(), $transaction->getDueDate(), $this->getParameter('app_stock_id'), $movement->getProductId(), $movement->getQuantity());
                $this->inventoryCorrectionHandler->__invoke($command);
            } catch (ApiException $e) {
                throw new HttpException(500);
            }
        }

        return $this->redirectToRoute('logout');
    }

    /**
     * @param Request $request
     * @param string  $defaultLabel
     *
     * @return Transaction
     */
    private function buildTransaction(Request $request, string $defaultLabel): Transaction
    {
        $barcodes = $request->request->get('barcode', []);
        $qty = $request->request->get('qty', []);
        $label = $request->request->get('label', '');
        if (empty($label)) {
            $label = $defaultLabel;
        }

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

    /**
     * @param Transaction $transaction
     *
     * @return array
     */
    private function handleTransaction(Transaction $transaction): array
    {
        $productRequiresInventoryCheck = [];

        foreach ($transaction->getMovements() as $movement) {
            try {
                $command = new InventoryCommand($transaction->getLabel(), $transaction->getDueDate(), $this->getParameter('app_stock_id'), $movement->getProductId(), $movement->getQuantity());
                $this->handler->__invoke($command);
            } catch (InventoryCheckRequestedException $e) {
                $productRequiresInventoryCheck[] = $e->getProductId();
            } catch (ApiException $e) {
                throw new HttpException(500);
            }
        }

        return $productRequiresInventoryCheck;
    }
}
