<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;

class AuthController extends Controller
{
   public function register(Request $request){
        $name = $request->input('name');
        $email = $request->input('email');
        $password = Hash::make($request->input('password'));

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' =>'required|min:6'
        ]);

        $register = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'api_token' =>''
        ]);
        
        if($register) {
            return response()->json([
                'success' => true,
                'message' => 'Register Success!',
                'data' => $register
            ],201);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Register Fail! Try Again',
                'data' => ''
            ],400);
        }
   }

   public function login(Request $request){
       $email = $request->input('email');
       $password = $request->input('password');

       //Validate Request
       $this->validate($request, [
            'email' => 'required',
            'password' => 'required'
        ]);
       
       $user = User::where('email', $email) ->first();
       if ($user){
            //Cek Password
            if(Hash::check($password, $user->password)){
                    $apiToken = base64_encode(str_random(40));

                    $user->update([
                        'api_token' =>$apiToken
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Login Success!',
                        'data' => [
                            'user' => $user,
                            'api_token' => $apiToken
                        ]
                    ],201);
            }else{
                return response()->json([
                        'success' => false,
                        'message' => 'Login Fail! Password not Match',
                        'data' => ''
                ]);
            }
       }else{
            return response()->json([
                    'success' => false,
                    'message' => 'Email does not Exist!'
            ]);
       }
   }   
}
