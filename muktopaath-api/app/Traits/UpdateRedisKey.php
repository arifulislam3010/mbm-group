<?php
  
namespace App\Traits;
  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
  
trait UpdateRedisKey {
  
    /**
     * @param Request $request
     * @return $this|false|string
     */

    public function get_data(Request $request, $redisKey, $data){

      // $redis = Redis::connection();
      // // $redis = Redis::get('key1');
      // $redis = Redis::set('body', $data);

      // print_r($redis);

      // return redirect()->back();
   }

   public function add_data(Request $request, $data){

      // print_r($data);
      // return 1;
      // var_dump($data);

      $redis_key = config()->get('global.redis_key');
      // var_dump($redis_key);
      $redis = Redis::connection();
      $redis = Redis::set('cache_key', $redis_key);
      $redis = Redis::set('body', $data);

      // print_r($redis);

      // return redirect()->back();
   }
    
     public function delete_key(Request $request){

      $redis_key = config()->get('global.redis_key');
      $array = explode('_', $redis_key);

      for ($i = 1; $i < 4; $i++) {

         if ($array[$i] != " ") { 
   
           $my_array[] = $array[$i]; 
   
         }        
       } 
       $short_key = implode("_",$my_array);

      $arrayFailed        = Redis::connection()->keys('global.redis_key:*');
      $arrayFailedJobs    = Redis::connection()->keys('body');
      $arrayToRemove      = array_merge($arrayFailed, $arrayFailedJobs);
      
      if(!empty($arrayFailedJobs)){
          $arrayMap = array_map(function ($k) {
            return str_replace(config('global.redis_key'), '', $k);
        }, $arrayToRemove);
      
        Redis::del($arrayMap);

      }
      // else{
      //   // Redis::del($short_key.':*');

      // }
       

    //  var_dump($arrayToRemove);

    //  


    //  exit();

    //  Redis::del(Redis::keys($short_key.'*'));

    // var_dump($short_key);


      //  var_dump($short_key);

         // $redis = Redis::connection();
         // $prefix = $redis->getOption(Redis::$short_key);

         // Redis::del($redis_key);
         // $keys = Redis::keys($short_key.'*');      
         //  var_dump($prefix);
        // return redirect()->back();
     }

    
  
}