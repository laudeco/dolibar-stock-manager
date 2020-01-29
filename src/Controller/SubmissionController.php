<?php


namespace App\Controller;

use App\Command\InventoryCommand;
use App\Command\InventoryCommandHandler;
use App\Command\InventoryCorrectionCommand;
use App\Command\InventoryCorrectionCommandHandler;
use App\Exception\ActionException;
use App\Exception\InventoryCheckRequestedException;
use App\Exception\ProductNotFoundException;
use App\Query\GetProductByBarcodeQuery;
use App\Query\GetProductByBarcodeQueryHandler;
use App\Util\TransactionBuilder;
use App\ViewModel\StockMovement;
use App\ViewModel\Transaction;
use Dolibarr\Client\Exception\ApiException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
     *
     * @throws \Exception
     */
    public function index(Request $request)
    {
        try {
            $transaction = $this->buildTransaction($request, $this->getParameter('app_mouvement_label'));

            $productRequiresInventoryCheck = $this->handleTransaction($transaction);
            if (empty($productRequiresInventoryCheck)) {
                return $this->success();
            }

            return $this->forward(InventoryController::class.'::indexAction', [], ['products' => $productRequiresInventoryCheck]);
        } catch (ActionException $e) {
            return $this->render('submission/index.html.twig', ['movements' => $e->getFeedbacks()]);
        } catch (\InvalidArgumentException $e) {
            $this->addFlash('error', 'Erreur avec la soumission');
        } catch (ProductNotFoundException $e) {
            $this->addFlash('error', 'Un des produit n\'a pas été trouvé');
        }

        return $this->render('index/index.html.twig');
    }

    /**
     * @Route("/inventory-check", name="inventory-check", methods={"POST"})
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function handleInventoryAction(Request $request)
    {
        $transaction = $this->buildTransaction($request, $this->getParameter('app_inventory_label'));

        foreach ($transaction->getMovements() as $movement) {
            try {
                $command = new InventoryCorrectionCommand($transaction->getLabel(), $transaction->getDueDate(), $this->getParameter('app_stock_id'), $movement->getProductId(), $movement->getQuantity());
                $this->inventoryCorrectionHandler->__invoke($command);

                $this->addFlash('success', sprintf('Inventory for : %s - OK', $movement->getProductLabel()));
            } catch (ApiException $e) {
                throw new HttpException(500);
            }
        }

        return $this->success();
    }

    /**
     * @param Transaction $transaction
     *
     * @return array
     *
     * @throws ActionException
     * @throws \App\Exception\ProductNotFoundException
     */
    private function handleTransaction(Transaction $transaction): array
    {
        $productRequiresInventoryCheck = [];
        $movementFeedback = [];
        $issue = false;

        foreach ($transaction->getMovements() as $movement) {
            try {
                $command = new InventoryCommand($transaction->getLabel(), $transaction->getDueDate(), $this->getParameter('app_stock_id'), $movement->getProductId(), $movement->getQuantity());

                if ($movement->isBatch()) {
                    $command->batch($movement->getSerial(), $movement->getDlc());
                }

                $this->handler->__invoke($command);

                $movementFeedback[] = $movement;

                $this->addFlash('success', sprintf('%s : OK', $movement->getProductLabel()));
            } catch (InventoryCheckRequestedException $e) {
                $productRequiresInventoryCheck[] = $e->getProductId();
                $movementFeedback[] = $movement;
            } catch (ApiException $e) {
                $issue = true;
                $movement->fail();
                $movementFeedback[] = $movement;
            }
        }

        if ($issue) {
            throw new ActionException($movementFeedback);
        }

        return $productRequiresInventoryCheck;
    }

    /**
     * @param Request $request
     * @param string  $defaultLabel
     *
     * @return Transaction
     *
     * @throws \Exception
     */
    private function buildTransaction(Request $request, string $defaultLabel): Transaction
    {
        return (new TransactionBuilder($this->productQueryHandler, $defaultLabel))->fromRequest($request->request);
    }

    /**
     * @return Response
     */
    private function success(): Response
    {
        return $this->render('submission/success.html.twig');
    }
}
