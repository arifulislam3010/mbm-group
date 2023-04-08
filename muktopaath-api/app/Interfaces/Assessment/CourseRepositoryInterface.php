<?php

namespace App\Interfaces\Assessment;

interface CourseRepositoryInterface 
{
    public function enrollbyadmin(array $request, int $batch_id);
}