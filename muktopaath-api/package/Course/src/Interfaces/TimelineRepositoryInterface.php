<?php

namespace Muktopaath\Course\Interfaces;

interface TimelineRepositoryInterface 
{
    public function store(array $request);
    public function comment();
    public function updateComment($id);
    public function update(int $id);
    public function delete(int $id);
    public function deleteComment(int $id);
    public function view(int $id);
    public function viewall();
}