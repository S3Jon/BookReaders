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

	public function createReview($id_user, $isbn, $rating, $comment, $visibility)
	{
		$this->reviewModel->id_user = $id_user;
		$this->reviewModel->isbn = $isbn;
		$this->reviewModel->rating = $rating;
		$this->reviewModel->comment = $comment;
		$this->reviewModel->visibility = $visibility;

		if ($this->reviewModel->createReview()) {
			return true;
		}

		return false;
	}

	public function getReviewsByUser($id_user)
	{
		return $this->reviewModel->getReviewsByUser($id_user);
	}

	public function getBookAverageRating($isbn)
	{
		return $this->reviewModel->getBookAverageRating($isbn);
	}

	public function getReview($id_review)
	{
		return $this->reviewModel->getReview($$id_review);
	}

	public function getReviewsByBook($isbn)
	{
		return $this->reviewModel->getReviewsByBook($isbn);
	}

    
}