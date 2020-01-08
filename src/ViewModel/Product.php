<?php


namespace App\ViewModel;

/**
 * @package App\ViewModel
 */
final class Product
{

    /**
     * @var string|null
     */
    private $codebar;

    /**
     * @var string
     */
    private $label;

    /**
     * @var int
     */
    private $id;

    /**
     * @return string
     */
    public function getCodebar(): string
    {
        return $this->codebar;
    }

    /**
     * @param string $codebar
     */
    public function setCodebar(string $codebar): void
    {
        $this->codebar = $codebar;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

}