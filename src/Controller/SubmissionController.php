<?php


namespace App\Controller;

use App\ViewModel\StockMovement;
use App\ViewModel\Transaction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @package App\Controller
 */
final class SubmissionController extends AbstractController
{

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
        $label = $request->request->get('label', 'Inventory Movement');
        $barcodes = $request->request->get('barcode', []);
        $qty = $request->request->get('qty', []);

        $transaction = new Transaction($label);

        $i = 0;
        foreach ($barcodes as $currentProduct){
            $transaction->add(StockMovement::move($currentProduct, $qty[$i]));
            $i++;
        }

        return $this->redirectToRoute('logout');
    }
}