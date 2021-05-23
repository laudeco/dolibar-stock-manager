<?php


namespace App\Controller;

use App\Command\InventoryCommand;
use App\Command\InventoryCommandHandler;
use App\Command\InventoryCorrectionCommand;
use App\Command\InventoryCorrectionCommandHandler;
use App\Domain\Inventory\InventoryRequest;
use App\Exception\ActionException;
use App\Exception\InventoryCheckRequestedException;
use App\Exception\ProductNotFoundException;
use App\Query\GetProductByBarcodeQueryHandler;
use App\Query\GetWarehousesQuery;
use App\Query\GetWarehousesQueryHandler;
use App\Util\TransactionBuilder;
use App\ViewModel\Transaction;
use Dolibarr\Client\Exception\ApiException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
     * @var GetWarehousesQueryHandler
     */
    private $warehouseQueryHandler;

    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(
        InventoryCommandHandler $handler,
        InventoryCorrectionCommandHandler $inventoryCorrectionHandler,
        GetProductByBarcodeQueryHandler $productQueryHandler,
        GetWarehousesQueryHandler $warehouseQueryHandler,
        SessionInterface $session
    ) {
        $this->handler = $handler;

        $this->inventoryCorrectionHandler = $inventoryCorrectionHandler;
        $this->productQueryHandler = $productQueryHandler;
        $this->warehouseQueryHandler = $warehouseQueryHandler;

        $this->session = $session;
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
        $warehouses = $this->warehouseQueryHandler->__invoke(new GetWarehousesQuery());

        try {
            $transaction = $this->buildTransaction($request, $this->getParameter('app_mouvement_label'));

            $productRequiresInventoryCheck = $this->handleTransaction($transaction);
            if (empty($productRequiresInventoryCheck)) {
                return $this->success();
            }

            $this->session->set('inventory_request', $productRequiresInventoryCheck);

            return $this->forward(InventoryController::class.'::indexAction');
        } catch (ActionException $e) {
            return $this->render('submission/index.html.twig', ['movements' => $e->getFeedbacks(), "warehouses" => $warehouses]);
        } catch (\InvalidArgumentException $e) {
            $this->addFlash('error', sprintf('Erreur avec la soumission (rien n\'est sauvegardé). (%s)', $e->getMessage()));
        } catch (ProductNotFoundException $e) {
            $this->addFlash('error', 'Un des produit n\'a pas été trouvé (rien n\'est sauvegardé).');
        }

        return $this->render('index/index.html.twig', ['warehouses' => $warehouses]);
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

        foreach ($transaction as $movement) {
            try {
                $command = new InventoryCorrectionCommand($transaction->getLabel(), $transaction->getDueDate(), $movement->getWarehouse(), $movement->getProductId(), $movement->getQuantity());
                $this->inventoryCorrectionHandler->__invoke($command);

                $this->addFlash('success', sprintf('Inventory for : %s - OK', $movement->getProductLabel()));
            } catch (ApiException $e) {
                $this->addFlash('error', sprintf('Inventory for : %s - NOK', $movement->getProductLabel()));
            }
        };

        return $this->success();
    }

    /**
     * @param Transaction $transaction
     *
     * @return array|InventoryRequest[]
     *
     * @throws ActionException
     * @throws \App\Exception\ProductNotFoundException
     */
    private function handleTransaction(Transaction $transaction): array
    {
        $productRequiresInventoryCheck = [];
        $movementFeedback = [];
        $issue = false;

        foreach ($transaction as $movement) {
            try {
                $command = new InventoryCommand($transaction->getLabel(), $transaction->getDueDate(), $movement->getWarehouse(), $movement->getProductId(), $movement->getQuantity());

                if ($movement->isBatch()) {
                    $command->batch($movement->getSerial(), $movement->getDlc());
                }

                $this->handler->__invoke($command);

                $movementFeedback[] = $movement;
                $this->addFlash('success', sprintf('%s : OK', $movement->getProductLabel()));
            } catch (InventoryCheckRequestedException $e) {
                $productRequiresInventoryCheck[] = new InventoryRequest($e->getProductId(), $movement->getWarehouse());
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
