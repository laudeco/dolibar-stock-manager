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
     */
    public function __construct(string $label)
    {
        $this->label = $label;
        $this->movements = [];

        try {
            $this->dueDate = new \DateTimeImmutable();
        } catch (\Exception $e) {
        }
    }

    /**
     * @param StockMovement $movement
     */
    public function add(StockMovement $movement)
    {
        $this->movements[] = $movement;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDueDate(): \DateTimeImmutable
    {
        return $this->dueDate;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return StockMovement[]|array
     */
    public function getMovements()
    {
        return $this->movements;
    }
}
