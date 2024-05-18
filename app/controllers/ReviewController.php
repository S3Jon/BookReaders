<?php

namespace controllers;
use models\Review;

class ReviewController
{
    private $reviewModel;

    public function __construct(Review $review)
    {
        $this->reviewModel = new $review;
    }

    
}