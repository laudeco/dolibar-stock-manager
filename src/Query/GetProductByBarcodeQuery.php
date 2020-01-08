<?php


namespace App\Query;

/**
 * @package App\Query
 */
final class GetProductByBarcodeQuery
{

    /**
     * @var string
     */
    private $barcode;

    /**
     * @param string $barcode
     */
    public function __construct(string $barcode)
    {
        $this->barcode = $barcode;
    }

    /**
     * @return string
     */
    public function barcode()
    {
        return $this->barcode;
    }
}
