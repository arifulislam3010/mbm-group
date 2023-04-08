<?php

namespace Muktopaath\Course\Interfaces;

interface SAttendanceRepositoryInterface 
{
    public function attend(int $id);
    public function list(int $id);
    public function verify(int $id);
}