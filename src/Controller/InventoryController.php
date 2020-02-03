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

    public function __construct(SessionInterface $session, GetInventoryCheckProductsQueryHandler $getInventoryCheckProductsQueryHandler)
    {
        $this->session = $session;
        $this->getInventoryCheckProductsQueryHandler = $getInventoryCheckProductsQueryHandler;
    }

    public function indexAction(): Response
    {
        $inventoryRequests = $this->session->get('inventory_request', []);
        $products = new ArrayCollection();

        /** @var InventoryRequest $request */
        foreach ($inventoryRequests as $request) {
            $inventoryProduct = $this->getInventoryCheckProductsQueryHandler->handle(new GetInventoryCheckProductsQuery($request->getWarehouseId(), $request->getProductId()));

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
