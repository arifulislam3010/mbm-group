<?php

namespace Muktopaath\Course\Http\Controllers\Course;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Muktopaath\Course\Interfaces\DiscussionRepositoryInterface;
use Illuminate\Support\Str;
use Auth;
 
class DiscussionController extends Controller
{

    private  $discussionRepository;

    public function __construct(DiscussionRepositoryInterface $discussionRepository) 
    {
        $this->discussionRepository = $discussionRepository;
    }

    public function index(){
        
        return $this->discussionRepository->index();
    }

    public function store(){
       return  $this->discussionRepository->store();
    }
}