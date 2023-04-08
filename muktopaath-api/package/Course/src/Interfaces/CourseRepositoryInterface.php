<?php

namespace Muktopaath\Course\Interfaces;

interface CourseRepositoryInterface 
{
    public function enrollbyadmin(array $request, int $batch_id);
}