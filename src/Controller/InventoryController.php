<?php


namespace App\Controller;

use App\Domain\Inventory\InventoryRequest;
use App\Query\GetInventoryCheckProductsQuery;
use App\Query\GetInventoryCheckProductsQueryHandler;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @package App\Controller
 */
final class InventoryController extends AbstractController
{

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var GetInventoryCheckProductsQueryHandler
     */
    private $getInventoryCheckProductsQueryHandler;

    /**
     * @var bool
     */
    private $inventoryRequest = true;

    /**
     * @var bool
     */
    private $logoutRequest = true;

    public function __construct(
        SessionInterface $session,
        GetInventoryCheckProductsQueryHandler $getInventoryCheckProductsQueryHandler,
        bool $inventoryRequest,
        bool $logoutRequest
    ) {
        $this->session = $session;
        $this->getInventoryCheckProductsQueryHandler = $getInventoryCheckProductsQueryHandler;
        $this->inventoryRequest = $inventoryRequest;
        $this->logoutRequest = $logoutRequest;
    }

    public function indexAction(): Response
    {
        if (!$this->inventoryRequest) {
            if ($this->logoutRequest) {
                return $this->redirectToRoute('logout');
            }

            return $this->redirectToRoute('index');
        }

        $inventoryRequests = $this->session->get('inventory_request', []);
        $products = new ArrayCollection();

        /** @var InventoryRequest $request */
        foreach ($inventoryRequests as $request) {
            $inventoryProduct = $this->getInventoryCheckProductsQueryHandler->handle(
                new GetInventoryCheckProductsQuery(
                    $request->getWarehouseId(),
                    $request->getProductId()
                )
            );

            if (null === $inventoryProduct) {
                continue;
            }

            $products[] = $inventoryProduct;
        }

        if (empty($inventoryRequests)) {
            return $this->redirectToRoute('logout');
        }

        return $this->render('inventory/index.html.twig', [
            'products' => $products
        ]);
    }
}
