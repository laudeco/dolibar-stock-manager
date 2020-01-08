<?php


namespace App\ViewModel;


final class ProductInventory
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string|null
     */
    private $barcode;

    /**
     * @var string|null
     */
    private $label;

    /**
     * @var bool
     */
    private $find;

    /**
     * @param int $id
     * @param string|null $barcode
     * @param string|null $label
     */
    private function __construct(int $id, ?string $barcode = null, ?string $label = null)
    {
        $this->id = $id;
        $this->barcode = $barcode;
        $this->label = $label;
        $this->find = null === $label && null === $barcode;
    }

    /**
     * @param int $id
     *
     * @return ProductInventory
     */
    public static function notFound(int $id){
        return new self($id);
    }

    /**
     * @param int $id
     * @param string $barcode
     * @param string $label
     *
     * @return ProductInventory
     */
    public static function create(int $id, string $barcode, string $label){
        return new self($id, $barcode, $label);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getBarcode(): string
    {
        return $this->barcode;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return bool
     */
    public function isFound(): bool
    {
        return $this->find;
    }

}