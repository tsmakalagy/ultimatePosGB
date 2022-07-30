<?php

namespace App\Http\Middleware;

use Closure;

class Role
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
        $administrator_list = config('constants.administrator_usernames');

        $bus_id=auth()->user()->business_id;
        if(auth()->user()->hasrole("Admin#".$bus_id) == true)
        {
            return $next($request);
        }
        else{
            return response()->json(['message'=>'authorized Action.'],403);
        }
            
      
    }
}
