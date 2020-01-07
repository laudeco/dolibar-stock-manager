<?php


namespace App\ViewModel;


final class Transaction
{

    /**
     * @var \DateTimeImmutable
     */
    private $dueDate;

    /**
     * @var string
     */
    private $label;

    /**
     * @var array|StockMovement
     */
    private $movements;

    /**
     * @param string $label
     *
     * @throws \Exception
     */
    public function __construct(string $label){
        $this->label = $label;
        $this->dueDate = new \DateTimeImmutable();
        $this->movements = [];
    }

    /**
     * @param StockMovement $movement
     */
    public function add(StockMovement $movement){
        $this->movements[] = $movement;
    }

}