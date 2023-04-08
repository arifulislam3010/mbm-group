<?php
namespace Muktopaath\Dashboard\Models\Myaccount;
use Laravel\Passport\HasApiTokens; 
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use App\Models\Myaccount\InstitutionInfo;
use App\Models\Myaccount\SystemRole;
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
    
    public function roles()
    {
        return $this->hasMany(SystemRole::class,'user_id')->with('owner');
    }

    public function currentrole()
    {
        return $this->belongsTo(SystemRole::class,'role_id')->with('owner');
    }
}
