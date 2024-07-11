<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use Exception;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function register(UserRequest $request){

        try {
            $creds =  $request->validated();
            // Auth::attempt($creds);
            // return response()->json([
            //     'message' => 'You are logged',
            //     'Status'=> 201
            // ]);

           if (!Auth::attempt($creds)) {
            return response()->json([
                'message' => 'Email ou mot de passe invalide',
                'Status'=> 401
            ]);
           }
           $user = Auth::user();
           $token = $user->createToken('auth_token', ['*']);

            return response()->json(['access_token' => $token, 'token_type' => 'Bearer']);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'Status'=> 'Fail'
            ]);
        }

    }
}
