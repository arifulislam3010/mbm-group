<?php

namespace App\Interfaces\EventManager;

interface ReviewRepositoryInterface 
{
    public function allReview();
    public function addReview(array $request);
    public function updateReview(array $request);
    public function deleteReview(int $id);
}