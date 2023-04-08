<?php

namespace App\Http\Controllers\Assessment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassMetaController extends Controller
{
    public function reapitingClass()
    {
        $now = strtotime("yesterday");

        $pushToFirst = -11;
        for($i = $pushToFirst; $i < $pushToFirst+30; $i++)
        {
            $now = strtotime("+".$i." day");
            $year = date("Y", $now);
            $month = date("m", $now);
            $day = date("d", $now);
            $nowString = $year . "-" . $month . "-" . $day;
            $week = (int) ((date('d', $now) - 1) / 7) + 1;
            $weekday = date("N", $now);

            // echo $nowString . "<br />";
            // echo $week . " " . $weekday . "<br />";

            //return $now;

            $sql = DB::SELECT("SELECT EV.*
                    FROM `course_batches` EV
                    RIGHT JOIN `class_metas` EM1 ON EM1.`batch_id` = EV.`id`
                    WHERE ( DATEDIFF( '$nowString', repeat_start ) % repeat_interval = 0 )
                    OR ( 
                        (repeat_year = $year OR repeat_year = '*' )
                        AND
                        (repeat_month = $month OR repeat_month = '*' )
                        AND
                        (repeat_day = $day OR repeat_day = '*' )
                        AND
                        (repeat_week = $week OR repeat_week = '*' )
                        AND
                        (repeat_weekday = $weekday OR repeat_weekday = '*' )
                        AND repeat_start <= DATE('$nowString')
                    )");

            return $sql;
        }
    }
}
