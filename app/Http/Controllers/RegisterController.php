<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RegisterController extends Controller
{
    public function registerUser(Request $request)
    {
        $rules = [
            'first_name'=>'required|min:2|max:20',
            'sirname'=>'required|min:2|max:20',
            'last_name'=>'required|min:2|max:20',
            'gender' => 'required|in:male,female',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|confirmed|min:6|max:12',
            'phone' => 'required|min:12|max:12|unique:users,phone',
            'address'=> 'required|min:4|max:50',
            'dob'=>'required|date_format:Y-m-d',
            'remember_token'=>'nullable'
        ];
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            return response()->json(['status_code'=>400, 'message'=>$validator->errors(),'data'=>$request->all()]);
        }

        $user = new User();
        $user->first_name = $request->first_name;
        $user->sirname = $request->sirname;
        $user->last_name = $request->last_name;
        $user->gender = $request->gender;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->dob = $request->dob;
        $user->remember_token = $request->remember_token;
        $res = $user->save();
        if(!$res){
            return response()->json(['status_code'=>400, 'message'=>'User Creation Failled!']);
        }else{
            //send account created email to given email $request->email
            $details = [
                'first_name'=>$request->first_name,
                'sirname'=>$request->sirname,
                'last_name'=>$request->last_name,
                'gender'=>$request->gender,
                'email'=>$request->email,
                'phone'=>$request->phone,
                'address'=>$request->address,
                'dob'=>$request->dob
            ];
            //Mail::to($request->email)->send(new AccountCreatedMailer($details));
            return response()->json(['status_code'=>200, 'message'=>'User Created Successfuly! Notification Message Sent']);
        }
    }
    public function registerStaff(){

    }
}
