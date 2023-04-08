<?php

namespace App\Http\Controllers\Assessment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use GuzzleHttp\Client as GuzzleClient;

class ZoomMeeting
{
    protected $API_END_POINT = 'https://api.zoom.us/v2';
    protected $DEFAULT_EMAIL = 'rh_mukul@yahoo.com';
    protected $DEFAULT_API_KEY = 'Gb5NIBp8TbyQGdw8pumJDQ';
    protected $DEFAULT_API_SECRET = 'zjBt1Fzmct53Efftk8QRGBMhQWucgaNoaJND';
    
    protected function getJwtToken($request){
        $key = $request['zoom_api_key']?$request['zoom_api_key']:$this->DEFAULT_API_KEY; //env('ZOOM_API_KEY', '');
        $secret = $request['zoom_api_secret']?$request['zoom_api_secret']:$this->DEFAULT_API_SECRET; //env('ZOOM_API_SECRET', '');
        $payload = [
            'iss' => $key,
            'exp' => strtotime('+1 minute'),
        ];
        return \Firebase\JWT\JWT::encode($payload, $secret, 'HS256');
    }
    
    public function getZoomUserInfo($request){
        $jwt_token = $this->getJwtToken($request);
        $email = $request['email']?$request['email']:$this->DEFAULT_EMAIL;
        
        // return $jwt_token;
        
        $headers = [
            'Content-Type' => 'application/json',
            'User-Agent' => 'Zoom-api-Jwt-Request',
            'Authorization' => 'Bearer ' . $jwt_token,
        ];

        $client = new GuzzleClient([
            'headers' => $headers
        ]);

        /*$body = '{
            "key1" : '.$value1.',
            "key2" : '.$value2.',
        }';*/

        /*$r = $client->request('POST', 'http://example.com/api/postCall', [
            'body' => $body
        ]);*/
        try{
            return $r = $client->request('GET', $this->API_END_POINT . '/users/' . $email);
//            return response()->json(['data' => $r], 200);
        } catch(\Exception $e){
            return response()->json(['id' => ''], 200);
        }
        
//        $response = $r->getBody()->getContents();
//        return json_decode($r, true);
    }
    
    public function getZoomMeetings($request){

        $jwt_token = $this->getJwtToken($request);
        $user_id = $request['user_id']?$request['user_id']:'eRcAtMPATdyk3D_tK2keGQ';
        
        // return $jwt_token;
        
        $headers = [
            'Content-Type' => 'application/json',
            'User-Agent' => 'Zoom-api-Jwt-Request',
            'Authorization' => 'Bearer ' . $jwt_token,
        ];

        $client = new GuzzleClient([
            'headers' => $headers
        ]);        
        
        return $r = $client->request('GET', $this->API_END_POINT . '/users/' . $user_id . '/meetings');
    }
    
    public function createZoomMeeting($request){
        // return $request['start_date_time'];
        $jwt_token = $this->getJwtToken($request);
        $user_id = $request['user_id']?$request['user_id']:'eRcAtMPATdyk3D_tK2keGQ';
        
        // return $jwt_token;
        
        $headers = [
            'Content-Type' => 'application/json',
            'User-Agent' => 'Zoom-api-Jwt-Request',
            'Authorization' => 'Bearer ' . $jwt_token,
        ];

        $client = new GuzzleClient([
            'headers' => $headers
        ]);
        
        /*$body = '{
          "topic": "string",
          "type": "integer",
          "start_time": "string [date-time]",
          "duration": "integer",
          "schedule_for": "string",
          "timezone": "string",
          "password": "string",
          "agenda": "string",
          "recurrence": {
            "type": "integer",
            "repeat_interval": "integer",
            "weekly_days": "string",
            "monthly_day": "integer",
            "monthly_week": "integer",
            "monthly_week_day": "integer",
            "end_times": "integer",
            "end_date_time": "string [date-time]"
          },
          "settings": {
            "host_video": "boolean",
            "participant_video": "boolean",
            "cn_meeting": "boolean",
            "in_meeting": "boolean",
            "join_before_host": "boolean",
            "mute_upon_entry": "boolean",
            "watermark": "boolean",
            "use_pmi": "boolean",
            "approval_type": "integer",
            "registration_type": "integer",
            "audio": "string",
            "auto_recording": "string",
            "enforce_login": "boolean",
            "enforce_login_domains": "string",
            "alternative_hosts": "string",
            "global_dial_in_countries": [
              "string"
            ],
            "registrants_email_notification": "boolean"
          }
        }';*/
        
        $to_time = strtotime($request['end_date_time']);
        $from_time = strtotime($request['start_date_time']);
        $minute = round(abs($to_time - $from_time) / 60,2);
        
        //$start_time = date('Y-m-d H:i:s',strtotime($request['start_date_time']));
        //$start_time = str_replace(' ', 'T', $start_time).'Z';
        
        $body = '{
            "topic": "'. $request['topic'] .'",
            "type": "2",
            "start_time": "' . str_replace(' ','T',gmdate('Y-m-d H:i:s',strtotime($request['start_date_time']) - 60 * 60 * 6)) .'Z",
            "duration": "'. $minute .'", 
            "password": "'. Str::random(8) .'",
            "agenda": "'. $request['topic'] .'",
            "settings": {
                "join_before_host": "true"
            }
        }';    

        
        return $r = $client->request('POST', $this->API_END_POINT . '/users/' . $user_id . '/meetings', [
            'body' => $body
        ]);
    }
    
    public function updateZoomMeeting($request, $meeting_id){
        $jwt_token = $this->getJwtToken($request);
        $user_id = $request['user_id']?$request['user_id']:'eRcAtMPATdyk3D_tK2keGQ';
        
        // return $jwt_token;
        
        $headers = [
            'Content-Type' => 'application/json',
            'User-Agent' => 'Zoom-api-Jwt-Request',
            'Authorization' => 'Bearer ' . $jwt_token,
        ];

        $client = new GuzzleClient([
            'headers' => $headers
        ]);
        
        $to_time = strtotime($request['end_date_time']);
        $from_time = strtotime($request['start_date_time']);
        $minute = round(abs($to_time - $from_time) / 60,2);
        
        $body = '{
            "topic": "'. $request['topic'] .'",
            "type": "2",
            "start_time": "' . $request['start_date_time'] .'",
            "duration": "'. $minute .'",
            "timezone": "Asia/Dhaka",
            "password": "'. $request['password'] .'",
            "agenda": "'. $request['topic'] .'",
            "settings": {
                "join_before_host": "true"
            }
        }';        
        
        return $r = $client->request('PATCH', $this->API_END_POINT . '/meetings/' . $meeting_id, [
            'body' => $body
        ]);
    }
    
    public function lol($email)
    {
        return $email;
    }
}