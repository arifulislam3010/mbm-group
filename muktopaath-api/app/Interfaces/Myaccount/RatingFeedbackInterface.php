<?php

namespace App\Interfaces\Myaccount;

interface RatingFeedbackInterface 
{
    public function index();
    public function view();
    public function view_all();
    public function store($request);
    public function update($request);
    public function approve($id);
    // public function delete(int $id);
}