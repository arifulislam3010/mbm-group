<?php
namespace Muktopaath\Course\Lib;


use App\Models\Course\CourseEnrollment;
use App\Models\Course\CourseBatch;
use App\Jobs\SendEmailJob;
use App\User;
use Illuminate\Support\Facades\Hash;
use DB;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use App\Models\Account\Order;
trait EnrollMentPartner
{

	public function userCreateOrUpdate($users)
	{
	    
		$userscheck = array();
		$sqlInsert = array();
		$emailList = array();
		$phonelList = array();
        $password = bcrypt('12345678');
        $verify_status = 1;
        try {
            
            
            foreach($users as $key=>$user){
                  $username_u = str_replace(' ','',$user['name']);
                    
                  $username_u_f = $username_u.str_random(5);
                    
                 $str = '("'.$user['email'].'","'.$user['name'].'","'.config('global.partner').'","'.$username_u_f.'","'.$password.'","'.$verify_status.'")';
                 array_push($sqlInsert, $str);
                 array_push($emailList,'"'.$user['email'].'"');
                 // array_push($phonelList,'"'.$user['phone'].'"');
            }
            
            
            $qryStr = 'INSERT INTO `users`(email,name,partner_id,username,password,verify_status) VALUES'.implode(',',$sqlInsert).' ON DUPLICATE KEY UPDATE email=email';
            
            $social = '[{"type":0,"link":null}]';
            $area_of_experiences = '[{"title":null}]';
            
            DB::select($qryStr);
            $insertUserInfoByEmailQry = 'INSERT INTO `user_infos`(user_id)
            SELECT id FROM users where email IN ('.implode(',',$emailList).') ON DUPLICATE KEY UPDATE user_id=VALUES(user_id),social="'.addslashes($social).'"'.',area_of_experiences="'.addslashes($area_of_experiences).'"';
            // $insertUserInfoByEmailQry = 'INSERT INTO `user_infos`(user_id)
            // SELECT id FROM users where email IN ('.implode(',',$emailList).')  or phone IN ('.implode(',',$phonelList).')
            // ON DUPLICATE KEY UPDATE user_id=VALUES(user_id),social="'.addslashes($social).'"'.',area_of_experiences="'.addslashes($area_of_experiences).'"';
            
            $InsertUserInfoData = DB::select($insertUserInfoByEmailQry);
            
            $getUserByEmailQry = 'SELECT id FROM users where email IN ('.implode(',',$emailList).')';
            return $getUserData = DB::select($getUserByEmailQry);


            $this->userInfoCreateOrUpdate($userscheck);
            return $userscheck;

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json([
                'code'    => 406,
                'message' => "forbidden",
                'errors'  => $e->getMessage(),
            ]);
        }

    }

    public function userInfoCreateOrUpdate($userscheck)
    {
        try {
            foreach($userscheck as $user)
            {
                $arr = array();
                $arr['user_id'] = $user->id;
                $arr['social'] = '[{"type":0,"link":null}]';
                $arr['area_of_experiences'] = '[{"title":null}]';
                
                $userInfo = DB::table('user_infos')->where('user_id',$user->id)->first();
                
                if(!empty($userInfo))
                {
                    DB::table('user_infos')->where('id', $userInfo->id)->update($arr);
                }else{
                    DB::table('user_infos')->insert($arr);
                }
            }
        }catch (ValidationException $e){
            return response()->json([
                'code'    => 406,
                'message' => "forbidden",
                'errors'  => $e->getMessage(),
            ]);
        }

    }

    public function restrictedCourseRecord($users,$batch_id)
    {
    	$arr = array();
    	$userscheck = array();
		$sqlInsert = array();
		$emailList = array();
		$phonelList = array();
		// return $users;
        try {

            $course_batch = CourseBatch::where('id',$batch_id)->where('owner_id',config('global.partner'))->first();
            if($course_batch){

                $arr['course_batch_id'] = $batch_id;
                $arr['access_code'] = $course_batch->restricted_access_code;
                $arr['job_status'] = 0;

                foreach($users as $user)
                {
                    $str = '("'.$user->id.'","'.$arr['course_batch_id'].'","'.$arr['access_code'].'")';
                    array_push($sqlInsert, $str);
                } 

               $qryStr = 'INSERT INTO `restricted_course_records`(user_id,course_batch_id,access_code) VALUES'.implode(',',$sqlInsert).' ON DUPLICATE KEY UPDATE updated_at=VALUES(updated_at)';
               DB::select($qryStr);
            }
            

        }catch (ValidationException $e){
            return response()->json([
                'code'    => 406,
                'message' => "forbidden",
                'errors'  => $e->getMessage(),
            ]);
        }


    }

    public function MailSent($data)
    {
        try{
            $url = 'http://103.48.16.6:8080/imlma/api/training/user-training-applicant-status-update';
           return $this->CurlFunction2($url,$data);
           return true;
        }
        catch (ValidationException $e){
            return true;
            return response()->json([
                'code'    => 406,
                'message' => "forbidden",
                'errors'  => $e->getMessage(),
            ]);
        }

    }

    public function CurlFunction2($url,$data)
    {
        $headers = array('Accept: application/json', 'Content-Type: application/json');
        try {
            //return $data;
            $data = json_encode($data);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $res = curl_exec($ch);
            return $res;
            if($e=curl_error($ch)){
                return $e;
            }else{
                $decoded = json_decode($res);
            }
            curl_close($ch);
        } catch(\Exception $e) {
            return $e;
        }

    }

}
