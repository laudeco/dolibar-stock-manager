<?php


namespace App\Controller;

use App\Domain\Inventory\InventoryRequest;
use App\Query\GetProductById;
use App\Query\GetProductByIdHandler;
use App\ViewModel\ProductInventory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @package App\Controller
 */
final class InventoryController extends AbstractController
{
    /**
     * @var GetProductByIdHandler
     */
    private $productHandler;

    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(GetProductByIdHandler $productHandler, SessionInterface $session)
    {
        $this->productHandler = $productHandler;
        $this->session = $session;
    }

    public function indexAction():Response
    {
        $inventoryRequests = $this->session->get('inventory_request', []);

        $products = [];
        /** @var InventoryRequest $inventoryRequest */
        foreach ($inventoryRequests as $inventoryRequest) {
            try {
                $prd = $this->productHandler->__invoke(new GetProductById($inventoryRequest->getProductId()));

                if ($prd->serialNumberable()) {
                    continue;
                }

                $products[] = ProductInventory::create($prd->getId(), $prd->getCodebar(), $prd->getLabel(), $inventoryRequest->getWarehouseId());
            } catch (NotFoundHttpException $e) {
                $products[] = ProductInventory::notFound($inventoryRequest->getProductId());
            }
        }

        if (empty($products)) {
            return $this->redirectToRoute('logout');
        }

        return $this->render('inventory/index.html.twig', [
            'products' => $products
        ]);
    }
}
