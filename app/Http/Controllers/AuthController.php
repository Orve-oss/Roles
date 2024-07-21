<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {

        try {
            $creds =  $request->validated();

            if (Auth::guard('web')->attempt($creds)) {
                if (!Auth::attempt($creds)) {
                    return response()->json([
                        'message' => 'Email ou mot de passe invalide'
                    ]);
                }
                $user = Auth::user();
                $token = $user->createToken('auth_token', ['*']);

                return response()->json([
                    'message' => 'Connexion réussie',
                    'access_token' => $token, 'token_type' => 'Bearer',
                    'status' => 'success',
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->roles->pluck('name')
                    ]
                ]);
            }
            if (Auth::guard('client')->attempt($creds)) {
                if (!Auth::attempt($creds)) {
                    return response()->json([
                        'message' => 'Email ou mot de passe invalide'
                    ]);
                }
                $client = Auth::guard('client')->user();
                $token = $client->createToken('auth_token', ['*']);
                return response()->json([
                    'message' => 'Connexion réussie',
                    'access_token' => $token, 'token_type' => 'Bearer',
                    'status' => 'success',
                    'user' => [
                        'id' => $client->id,
                        'name' => $client->nom_clt,
                        'email' => $client->email,
                        'role' => $client->roles->pluck('name')
                    ]
                ]);
            }
            // $user = Auth::attempt($creds);
            // // return response()->json([
            // //     'message' => 'You are logged',
            // //     'status' => 'success'
            // // ]);


        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'Status' => 'Fail'
            ]);
        }
    }
}
