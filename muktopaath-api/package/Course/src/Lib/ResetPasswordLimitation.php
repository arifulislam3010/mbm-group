<?php
namespace App\Lib;
use Carbon\Carbon;
use DB;

trait ResetPasswordLimitation
{
    public function ResetPasswordTokenCheck($emailOrPhone,$type,$status)
    {
//        return $emailOrPhone;

        $arr = array();
        $objectArr = array();
        $token = rand(1000,9999);
        $arr['reset_token'] = $token;
        $arr['email_phone'] = $emailOrPhone;
        $arr['type'] = $status;

        $arr['created_at'] = date('Y-m-d H:i:s');
        $time = Carbon::now()->format('Y-m-d H:i:s');
        $reset_pass = DB::table('reset_password')->where('email_phone',$emailOrPhone)->where('type',$status)->get();
       if(sizeof($reset_pass)>0)
       {
        $old_time = date('Y m d H i',strtotime($reset_pass[0]->created_at));
        list($year,$mon,$day,$hour,$minute) = explode(' ',$old_time);
        $new_time = date('Y m d H i',strtotime($time));
        list($y,$m,$d,$h,$min) = explode(' ',$new_time);
        $num1 = $year.$mon.$day.$hour.$minute;
        $num2 = $y.$m.$d.$h.$min;
        if($d-$day>0 && $h-$hour==0 && ($minute*60-$min*60)==0  || $d-$day>0 && $h-$hour==0 || $d-$day>1 ||$d-$day>0 && $h-$hour>1|| $d-$day>0 && $h-$hour==1 && ($minute*60-$min*60)>=600||$h-$hour>0 && ($minute*60-$min*60)>=600 ||$num2-$num1>=10 && $reset_pass[0]->total_count < 3 || $d-$day>0 && $reset_pass[0]->total_count==3){
             $count_sum = $reset_pass[0]->total_count;
             if($d-$day>0){
                 $count_sum=0;
             }
             $count_sum = $count_sum+1;
             DB::table('reset_password')->where('email_phone',$emailOrPhone)->where('type',$status)->update([
                'reset_token'=> $token,
                'created_at' => $time,
                'total_count' => $count_sum
            ]);
            $objectArr['status'] = true;
            $objectArr['token'] = $token;
            $objectArr['messages'] = "phone_verification";
            return $objectArr;
        }
        elseif($d-$day==0 &&  $reset_pass[0]->total_count==3 ){
            $objectArr['status'] = false;
            $objectArr['messages'] = "wait_1_day";
            return $objectArr;
        }
        else{
            $objectArr['status'] = false;
            $objectArr['messages'] = "wait_10_minute";
            return $objectArr;
        }


       }else{
            $arr['total_count'] = 1;
            DB::table('reset_password')->insert($arr);
            $objectArr['status'] = true;
            $objectArr['token'] = $token;
            $objectArr['messages'] = "phone_verification";
            return $objectArr;
       }

    }
}