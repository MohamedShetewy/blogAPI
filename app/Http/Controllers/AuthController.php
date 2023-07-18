<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends BaseController
{
    public function login(Request $request){
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            $success['token'] = $user->createToken('mosh')->accessToken;
            $success['name'] = $user->name;
            return $this->sendResponse($success,"User Login successfully");
        }else{
            return $this->sendError("please check Auth",['error'=>"Unauthorized"]);
        }

    }

    public function register(Request $request){
        $validate = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if($validate->fails()){
                return $this->sendError("please validate error",$validate->errors());
        }

        $input = $request->all();
        $input['password']= Hash::make($input['password']);
        
        
        try{
            $user = User::create($input);
        } catch (Exception $e) {
            // Log the error message
            //error_log('Error creating user: ' . $e->getMessage());
            return $this->sendError("please validate error",$e->getMessage());
        }
        $success['token'] = $user->createToken('mosh')->accessToken;
        $success['name'] = $user->name;

        return $this->sendResponse($success,"User registered successfully");
    }


}
