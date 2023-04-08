<?php

namespace App\Interfaces\AdminSettings;

interface EduLevelRepositoryInterface 
{
    public function allLevel();
    public function addEduLevel(array $request);
    public function updateEduLevel(array $request);
    public function deleteEduLevel(int $id);
}