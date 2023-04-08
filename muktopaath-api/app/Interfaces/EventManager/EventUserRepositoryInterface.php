<?php

namespace App\Interfaces\EventManager;

interface EventUserRepositoryInterface 
{
    public function allUserEvents();
    public function createUserEvent(array $request);
    public function updateUserEvent(array $request);
    public function deleteUserEvent(int $id);
}