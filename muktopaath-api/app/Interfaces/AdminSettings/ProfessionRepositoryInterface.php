<?php

namespace App\Interfaces\AdminSettings;

interface ProfessionRepositoryInterface 
{
    public function allProfession();
    public function addProfession(array $request);
    public function updateProfession(array $request);
    public function getfields(int $profession_id);
    public function deleteProfession(int $id);
}