<?php


namespace App\Exception;

use App\ViewModel\StockMovement;

final class ActionException extends \Exception
{

    /**
     * @var StockMovement[]|array
     */
    private $feedbacks;

    /**
     * @param StockMovement[]|array $feedbacks
     */
    public function __construct($feedbacks)
    {
        $this->feedbacks = $feedbacks;
    }

    /**
     * @return StockMovement[]|array
     */
    public function getFeedbacks()
    {
        return $this->feedbacks;
    }
}
