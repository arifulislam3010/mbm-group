<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\Models\Myaccount\ActivityLog;

class AuthUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Pre-Middleware Action


        // Post-Middleware Action
        if(Auth::user()){
             if(Auth::user()->id==1){
                 config()->set('global.type', 1);
             }
            config()->set('global.user_id', Auth::user()->id);

            if ($request->hasHeader('ownerid')) {

            config()->set('global.owner_id', $request->header('ownerid'));

        }
            $response = $next($request);

        }
        else{
            return response()->json("No user found",401);
        }

        return $response;
    }
}
