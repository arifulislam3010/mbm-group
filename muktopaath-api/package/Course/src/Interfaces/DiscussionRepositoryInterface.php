<?php

namespace Muktopaath\Course\Interfaces;

interface DiscussionRepositoryInterface 
{
    public function index();
    public function store();
    public function verify(int $id);
}