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

}