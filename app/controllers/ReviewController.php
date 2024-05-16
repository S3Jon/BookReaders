<?php

namespace controllers;
use models\User;

class ReviewController
{
    private $reviewModel;

    public function __construct(User $user)
    {
        $this->reviewModel = new $user;
    }

    
}