<?php

namespace App\Http\Middleware;
// use illuminate\support\Facades\Auth;
use Illuminate\Http\Request ;
// use Illuminate\Auth\Middleware\AuthenticateApi as Middlewar;
use Closure;



use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Factory as Auth;

class AuthenticateApi
{
    
    /**
     * The authentication factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        try{
        $this->authenticate($request, $guards);

        return $next($request);
        }
        catch(\Exception $e){
            return response()->json(['message'=>'unauthenticated'],401);
        }
    }

    /**
     * Determine if the user is logged in to any of the given guards.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $guards
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function authenticate($request, array $guards)
    {
        if (empty($guards)) {
            $guards = [null];
        }

        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                // return response()->json(['message'=>'authorized'],401);
                return $this->auth->shouldUse($guard);
                
            }
            // return response()->json(['message'=>'unauthorized'],401);
        }

        throw new AuthenticationException(
            'Unauthenticated.', $guards, $this->redirectTo($request)
        );
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        // return response()->json(['message'=>'unauthorized'],401);
        if ( !$request->expectsJson()) {
            // return route('login');
            // return $next($request);
            return response()->json(['message'=>'unauthorized'],401);
        }
    
    }
}










// class AuthenticateApi extends Middlewar
// {
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    // protected function redirectTo($request)
    // {
    //       return response()->json(['message'=>'unauthorized'],401);
    //     if ( !$request->expectsJson()) {
    //         // return route('login');
    //         // return $next($request);
    //         return response()->json(['message'=>'unauthorized'],401);
    //     }
    //     else{
    //         return response()->json(['message'=>'authorized'],401);

    //     }
    // }

        /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    // public function handle($request, Closure $next)
    // {
    //     if (!$request->expectsJson()) {
    //         return response()->json(['message'=>'unauthorized'],401);
    //         // return $next($request);
    //     }
    //     else{
    //         // return response()->json(['message'=>'uauthorized'],401);
    //         return $next($request);
    //     }   
    // }
// }
