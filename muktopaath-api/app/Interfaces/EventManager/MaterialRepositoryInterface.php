<?php

namespace App\Interfaces\EventManager;

interface MaterialRepositoryInterface 
{
    public function allMaterials();
    public function createMaterial(array $request);
    public function updateMaterial(array $request);
    public function deleteMaterial(int $id);
}