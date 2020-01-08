<?php

namespace App\Controller;

use App\Query\GetProductByBarcodeQuery;
use App\Query\GetProductByBarcodeQueryHandler;
use Dolibarr\Client\Exception\ApiException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/api/products", name="products_")
 *
 * @package App\Controller
 */
final class ProductController extends AbstractController
{

    /**
     * @var GetProductByBarcodeQueryHandler
     */
    private $getProductByBarcodeQueryHandler;

    /**
     * @var Security
     */
    private $security;

    /**
     * @param GetProductByBarcodeQueryHandler $getProductByBarcodeQueryHandler
     * @param Security                        $security
     */
    public function __construct(GetProductByBarcodeQueryHandler $getProductByBarcodeQueryHandler, Security $security)
    {
        $this->getProductByBarcodeQueryHandler = $getProductByBarcodeQueryHandler;
        $this->security = $security;
    }


    /**
     * @Route("/{barcode}", name="get_by_barcode", methods={"GET"})
     *
     * @param string $barcode
     *
     * @return JsonResponse
     */
    public function byBarcode(string $barcode)
    {
        try {
            $product = $this->getProductByBarcodeQueryHandler->__invoke(new GetProductByBarcodeQuery($barcode));

            return new JsonResponse([
                'label'   => $product->getLabel(),
                'barcode' => $product->getCodebar(),
            ]);
        } catch (ApiException $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }
}
