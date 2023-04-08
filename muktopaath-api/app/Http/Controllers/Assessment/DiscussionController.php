<?php

namespace App\Http\Controllers\Assessment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Interfaces\Assessment\DiscussionRepositoryInterface;
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