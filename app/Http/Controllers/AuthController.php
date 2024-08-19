<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {

        try {
            $creds = $request->validated();

            if (Auth::guard('web')->attempt($creds)) {
                if (!Auth::attempt($creds)) {
                    return response()->json([
                        'message' => 'Email ou mot de passe invalide'
                    ]);
                }
                $user = Auth::user();
                if ($user) {
                    return $this->handleLoginAttempt($user, $creds);
                }
                // $token = $user->createToken('auth_token', ['*'])->plainTextToken;

                // return response()->json([
                //     'message' => 'Connexion réussie',
                //     'access_token' => $token, 'token_type' => 'Bearer',
                //     'status' => 'success',
                //     'user' => [
                //         'id' => $user->id,
                //         'name' => $user->name,
                //         'email' => $user->email,
                //         'role' => $user->roles->pluck('name')
                //     ]
                // ]);
            } else if ($client = Client::where('email', $creds['email'])->first()) {
                if ($client && Hash::check($creds['password'], $client->password)) {
                    Auth::login($client);
                    if ($client) {
                        return $this->handleLoginAttempt($client, $creds, 'Client');
                    }


                    // $token = $client->createToken('auth_token', ['*'])->plainTextToken;
                    // return response()->json([
                    //     'message' => 'Client connecté',
                    //     'access_token' => $token, 'token_type' => 'Bearer',
                    //     'status' => 'success',
                    //     'user'=>[
                    //         'id' => $client->id,
                    //         'name' => $client->nom_clt,
                    //         'email'=>$client->email,
                    //         'role'=>$client->roles->pluck('name')
                    //     ]
                    // ]);
                }
            } else {
                return response()->json([
                    'message' => 'Email ou mot de passe invalide'
                ], 401);
            }

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'Status' => 'Fail'
            ]);
        }
    }

    private function handleLoginAttempt($user, $creds, $type = 'User')
    {
        // Vérifiez si le compte est verrouillé
        if ($user->account_locked_at) {
            return response()->json([
                'message' => 'Votre compte est bloqué. Veuillez contacter l\'administrateur.'
            ], 403);
        }

        // Incrémenter les tentatives de connexion si l'authentification échoue
        if (!Auth::attempt($creds)) {
            $user->login_attempts++;
            $user->last_login_attempt = now();

            // Bloquer le compte après 5 tentatives échouées
            if ($user->login_attempts >= 5) {
                $user->account_locked_at = now();

                // Envoyer un email à l'administrateur
                $adminEmail = 'sikamagnou@gmail.com'; // Remplacez par l'email de l'admin
                Mail::raw("Le compte {$type} de l'utilisateur {$user->email} a été bloqué après 5 tentatives de connexion échouées.", function ($message) use ($adminEmail) {
                    $message->to($adminEmail)
                        ->subject('Compte utilisateur bloqué');
                });
            }

            $user->save();

            return response()->json([
                'message' => 'Email ou mot de passe invalide'
            ], 401);
        }

        // Réinitialiser les tentatives de connexion après une connexion réussie
        $user->login_attempts = 0;
        $user->last_login_attempt = null;
        $user->account_locked_at = null;
        $user->save();

        $token = $user->createToken('auth_token', ['*'])->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'status' => 'success',
            'user' => [
                'id' => $user->id,
                'name' => $type === 'User' ? $user->name : $user->nom_clt,
                'email' => $user->email,
                'role' => $user->roles->pluck('name')
            ]
        ]);
    }
}
