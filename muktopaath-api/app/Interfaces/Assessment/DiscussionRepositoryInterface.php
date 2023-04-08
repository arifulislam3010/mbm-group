<?php

namespace App\Interfaces\Assessment;

interface DiscussionRepositoryInterface 
{
    public function index();
    public function store();
    public function verify(int $id);
}