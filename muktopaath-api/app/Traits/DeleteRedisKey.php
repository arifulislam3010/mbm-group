<?php
  
namespace App\Traits;
  
use Illuminate\Http\Request;
  
trait DeleteRedisKey {
  
    /**
     * @param Request $request
     * @return $this|false|string
     */
    public function delete_key(Request $request, $redis_key){

        
    }
  
}