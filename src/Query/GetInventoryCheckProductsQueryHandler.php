<?php


namespace App\Query;

use App\Repository\Dolibarr\ProductRepository;
use App\ViewModel\ProductInventory;
use Dolibarr\Client\Exception\ApiException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class GetInventoryCheckProductsQueryHandler implements QueryHandlerInterface
{
    /**
     * @var ProductRepository
     */
    private $doliProductRepository;

    public function __construct(ProductRepository $doliProductRepository)
    {
        $this->doliProductRepository = $doliProductRepository;
    }

    public function __invoke(GetInventoryCheckProductsQuery $query): ?ProductInventory
    {
        try {
            $prd = $this->doliProductRepository->getById($query->getProductId());

            if ($prd->serialNumberable()) {
                return null;
            }

            return ProductInventory::create(
                $prd->getId(),
                $prd->getCodebar(),
                $prd->getLabel(),
                $query->getWarehouseId()
            );
        } catch (NotFoundHttpException $e) {
            return ProductInventory::notFound($query->getProductId());
        } catch (ApiException $e) {
            return ProductInventory::notFound($query->getProductId());
        }
    }

    public function handle($query):?ProductInventory
    {
        return $this->__invoke($query);
    }
}
