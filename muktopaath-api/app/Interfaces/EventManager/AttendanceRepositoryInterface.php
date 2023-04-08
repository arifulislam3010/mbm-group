<?php

namespace App\Interfaces\EventManager;

interface AttendanceRepositoryInterface 
{
    public function allAttendances();
    public function createAttendance(array $request);
    public function updateAttendance(array $request);
    public function deleteAttendance(int $id);
}