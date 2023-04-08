<?php

namespace App\Interfaces\EventManager;

interface EventRepositoryInterface 
{
    public function allEvents(array $request);
    public function createEvent(array $request);
    public function updateEvent(array $request);
    public function deleteEvent(int $id);
}