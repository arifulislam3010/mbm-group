<?php
namespace App\Models\Myaccount;
use Laravel\Passport\HasApiTokens; 
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use App\Models\Myaccount\InstitutionInfo;
use App\Models\Filemanager\ContentBank;
use Muktopaath\Course\Models\Course\Order;
use App\Models\Myaccount\SystemRole;
use App\Models\Myaccount\UserInfo;
use myGov\Logtracker\Traits\Logtrackerable;


class User extends Model implements AuthenticatableContract,AuthorizableContract
{

    protected $connection = 'my-account';

    use HasApiTokens, Authenticatable, Authorizable;

    protected $guarded = ['created_at','updated_at'];
    // protected $guarded = ['created_at','updated_at'];


    public function institutions()
    {
        return $this->hasMany(InstitutionInfo::class,'user_id');
    }

    public function photo(){
        return $this->hasOne(ContentBank::class,'id','photo_id')->select(['id','type','file_encode_path','file_main_path','is_url']);
    }

    public function user_info(){
        return $this->hasOne(UserInfo::class);
    }

    public function user_other(){
        return $this->hasOne(UserInfo::class)->select(['id','user_id','designation']);
    }
    
    public function roles()
    {
        return $this->hasMany(SystemRole::class,'user_id')->with('owner');
    }

    // public function total_enrolled(){

    //     return $this->hasMany(Order::class,'user_id','id')->join('course_enrollments','course_enrollments.order_id','orders.id')->join('course_batches','course_batches.id','course_enrollments.course_batch_id')->where('course_batches.owner_id',config()->get('global.owner_id'))->select(['orders.id','orders.user_id','course_enrollments.course_batch_id','course_batches.owner_id']);
    // }
    public function total_enrolled(){

        return $this->hasMany(Order::class,'user_id','id')->join('course_enrollments','course_enrollments.order_id','orders.id')->join('course_batches', function($join){
            $join->on('course_enrollments.course_batch_id', '=', 'course_batches.id')
                    ->where('course_batches.owner_id',config()->get('global.owner_id'));
        })->select(['orders.id','orders.user_id','course_enrollments.course_batch_id','course_batches.owner_id']);
    }

    public function total_certificate(){

        return $this->hasMany(Order::class,'user_id','id')->join('course_enrollments','course_enrollments.order_id','orders.id')->join('course_batches', function($join){
            $join->on('course_enrollments.course_batch_id', '=', 'course_batches.id')
                    ->where('course_batches.owner_id',config()->get('global.owner_id'));
        })->join('certificate_submit', function($join){
            $join->on('certificate_submit.course_enrollment_id', '=', 'course_enrollments.id');
        })->select(['orders.id']);
    }

    // public function total_certificate(){

    //     return $this->hasMany(Order::class,'user_id','id')->join('course_enrollments','course_enrollments.order_id','orders.id')->join('course_batches','course_batches.id','course_enrollments.course_batch_id')
    //         ->join('certificate_submit','certificate_submit.course_enrollment_id','course_enrollments.id')->where('course_batches.owner_id',config()->get('global.owner_id'))->select(['orders.id','orders.user_id','course_enrollments.course_batch_id','course_batches.owner_id']);
    // }
    public function course_progress(){
        return $this->hasMany('Muktopaath\Course\Models\Course\Completeness');
    }
    public function currentrole()
    {
        return $this->belongsTo(SystemRole::class,'role_id')->with('owner');
    }
    public function getUserInfo()
    {
        return $this->hasOne(UserInfo::class,'user_id');
    }
    
}
