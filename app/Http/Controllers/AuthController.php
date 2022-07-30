<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Passport\HasApiTokens;
use Laravel\Passport\RefreshToken;
use Laravel\Passport\Token;
use illuminate\support\Facades\Auth;
// use illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Hashing\Hasher;
use Carbon\Carbon;
use App\User;

class AuthController extends Controller

{
    public function __construct(Hasher $hasher)
    {
        $this->hasher=$hasher;
    }

    public function register(Request $request){
        $request->validate([
            'username'=> 'required|string',
            'email' => 'required|string|unique:users',
            'password' => 'required|string|min:6'
        ]);
        $user=new User([
            'username'=>$request->username,
            'email'=>$request->email,
            'password' => $this->hasher->make($request->password)
        ]);
        $user->save();
        return response()->json(['message'=>'user has been registered'],200);
    }

    public function login(Request $request){
        $request->validate([
            // 'username'=> 'required|string',
            'username' => 'required|string',
            'password' => 'required|string'
        ]);
        $credentials= request(['username','password']);

        if(!Auth::attempt($credentials))
        {
            return response()->json(['message'=>'unauthorized'],401);
            
        }
        // return $credentials;
        $user=$request->user();
        // return $user;
    //     $access_token= Auth::user()->createToken('authToken')->accessToken;
    //     return response(['user' => Auth::user(),
    //                      'access_token' => $access_token
    // ]);
        $tokenResult=$user->createToken('personal_access_token');
        // return $tokenResult;
        $token=$tokenResult->token;
        $token->expires_at=Carbon::now()->addDays(1);
        $token->save();
        // return $token;
        return response()->json(['data'=>[
            'user'=>Auth::user(),
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'bearer',
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString() 
        ]]);
    }

    public function logout(Request $request){
        $bus_id=Auth::user()->business_id;
        // if(Auth::user()->hasrole("Admin#".$bus_id) == true)
        // {
        //     $rep="true";
        // }
        // else{
        //     $rep="false";
        // }

        // if (auth()->user()->can('product.view') || auth()->user()->can('product.create') ){
        //     $rep="connect";
        // }
        // else{
        //     $rep='not connected';
        // }
        // return $rep;
        // $user = Auth::user()->token();
        // $user->revoke();
        // return 'logged out';
        //  auth()->user()->token()->revoke();
        $request->user()->token()->delete();
        return response()->json(['message' => 'user successfully loged out',200]);
    }
}
