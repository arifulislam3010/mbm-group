<?php

namespace App\Interfaces\AdminSettings;

interface CategoryRepositoryInterface 
{
    public function allCategories();
    public function disabilities();
    public function addCategory(array $request);
    public function updateCategory(array $request);
    public function deleteCategory(int $id);
}