<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    public function loginUser(Request $request){
        $rules = [
            'email'=>'required|email',
            'password'=>'required|min:6|max:12'
        ];
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            return response()->json(['status_code'=>400, 'message'=>$validator->errors()]);
        }
        //check if email exists, then check if passwords match
        $credentials = request(['email','password']);
        if(!Auth::attempt($credentials))
        {
            return response()->json(['status_code'=>500, 'message'=>'Email Password Combination Incorrect']);
        }
        $user = User::where('email',$request->email)->first();
        $tokenResult = $user->createToken('authToken')->plainTextToken;

        return response()->json(['status_code'=>200, 'token'=> $tokenResult,'user'=>$user]);
    }
    public function logoutUser(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status_code'=>200, 'message'=> 'Logged Out Successfuly']);
    }
}
