<?php

namespace App\Interfaces\AdminSettings;

interface DegreeRepositoryInterface 
{
    public function degreeByLevel(int $level_id);
    public function addDegree(array $request);
    public function updateDegree(array $request);
    public function deleteDegree(int $id);
}