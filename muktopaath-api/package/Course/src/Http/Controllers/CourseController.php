<?php

namespace Muktopaath\Course\Http\Controllers;

use App\Http\Controllers\Controller;
use Muktopaath\Course\Models\Course;

class CourseController extends Controller
{
    public function index()
    {
        $owner_id = config()->get('global.owner_id');
        return $owner_id;
        $Courses = Course::where('owner_id',$owner_id)->with('contantBank')->get();
        return $Courses;
    }
}