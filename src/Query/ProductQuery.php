<?php


namespace App\Query;

/**
 * @package App\Query
 */
final class ProductQuery
{

    /**
     * @var int
     */
    private $page;

    /**
     * @var int
     */
    private $limit;

    /**
     * @param int $page
     * @param int $limit
     */
    public function __construct(int $page = 0, int $limit = 100)
    {
        $this->page = $page;
        $this->limit = $limit;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

}