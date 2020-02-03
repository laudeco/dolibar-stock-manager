<?php


namespace App\ViewModel;

final class Warehouse
{

    /**
     * @var string
     */
    private $label;

    /**
     * @var int
     */
    private $id;

    /**
     * @param string $label
     * @param int    $id
     */
    public function __construct(string $label, int $id)
    {
        $this->label = $label;
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}
