<?php

namespace App\Interfaces\Assessment;

interface SAttendanceRepositoryInterface 
{
    public function attend(int $id);
    public function list(int $id);
    public function verify(int $id);
}