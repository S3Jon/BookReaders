<?php

namespace controllers;
use models\Review;

require_once "../app/models/Review.php";

class ReviewController {

    private $reviewModel;

    public function __construct(Review $review)
    {
        $this->reviewModel = new $review;
    }

    public function createReview($data)
    {
        //si la review ya existe no se puede crear
        if($this->reviewModel->reviewExists($data['id_user'], $data['isbn'])){
            return false;
        }

        $this->reviewModel->id_user = $data['id_user'];
        $this->reviewModel->isbn = $data['isbn'];
        $this->reviewModel->rating = $data['rating'] < 1 ? 1 : $data['rating'];
        $this->reviewModel->comment = $data['comment'];
        $this->reviewModel->visibility = $data['visibility'];

        if($this->reviewModel->create()){
            return true;
        } else {
            return false;
        }
    }

    public function getReviews($isbn)
    {
        return $this->reviewModel->getReviews($isbn);
    }

    
}