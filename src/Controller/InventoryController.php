<?php


namespace App\Controller;

use App\Query\GetProductById;
use App\Query\GetProductByIdHandler;
use App\ViewModel\ProductInventory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @param GetProductByIdHandler $productHandler
     */
    public function __construct(GetProductByIdHandler $productHandler)
    {
        $this->productHandler = $productHandler;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $productIds = $request->get('products', []);

        if (empty($productIds)) {
            return $this->redirectToRoute('logout');
        }

        $products = [];
        foreach ($productIds as $id) {
            try {
                $prd = $this->productHandler->__invoke(new GetProductById($id));
                $products[] = ProductInventory::create($prd->getId(), $prd->getCodebar(), $prd->getLabel());
            } catch (NotFoundHttpException $e) {
                $products[] = ProductInventory::notFound($id);
            }
        }

        return $this->render('inventory/index.html.twig', [
            'products' => $products
        ]);
    }
}
