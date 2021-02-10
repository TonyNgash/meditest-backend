<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    public function forgotPassword(Request $request){
        $rules = [
            'email'=>'required|email|exists:users,email'
        ];
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            return response()->json(['status_code'=>400, 'message'=>$validator->errors(),'data'=>$request->all()]);
        }
        Password::sendResetLink($request->all());
        return response()->json(['status_code'=>200,'message'=>'A Password reset code has been sent to '.$request->email]);
    }
    public function resetPassword(Request $request){


    }
    public function setNewPassword(Request $request){


    }
}
