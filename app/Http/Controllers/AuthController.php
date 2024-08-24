<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\Client;
use App\Models\User;
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
                // Récupérer l'utilisateur authentifié
                $user = Auth::guard('web')->user();
                if ($user) {
                    return $this->handleLoginAttempt($user, $creds);
                }
            }

            else if ($client = Client::where('email', $creds['email'])->first()) {
                if ($client && Hash::check($creds['password'], $client->password)) {
                    Auth::login($client);
                    if ($client) {
                        return $this->handleLoginAttemptClient($client, $creds, 'Client');
                    }

                }
            } else {
                return $this->handleFailedAttempt($creds);
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
        if ($user->account_locked_at) {
            return response()->json([
                'message' => 'Votre compte est bloqué. Veuillez contacter l\'administrateur.'
            ], 403);
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
    private function handleLoginAttemptClient($client, $creds, $type = 'User')
    {
        if ($client->account_locked_at) {
            return response()->json([
                'message' => 'Votre compte est bloqué. Veuillez contacter l\'administrateur.'
            ], 403);
        }

        // Réinitialiser les tentatives de connexion après une connexion réussie
        $client->login_attempts = 0;
        $client->last_login_attempt = null;
        $client->account_locked_at = null;
        $client->save();

        $token = $client->createToken('auth_token', ['*'])->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'status' => 'success',
            'user' => [
                'id' => $client->id,
                'name' => $type === 'Client' ? $client->name : $client->nom_clt,
                'email' => $client->email,
                'role' => $client->roles->pluck('name')
            ]
        ]);
    }
    private function handleFailedAttempt($creds)
    {
        $user = User::where('email', $creds['email'])->first() ?? Client::where('email', $creds['email'])->first();

        if ($user) {
            $user->login_attempts++;
            $user->last_login_attempt = now();

            // Bloquer le compte après 5 tentatives échouées
            if ($user->login_attempts >= 2) {
                $user->account_locked_at = now();

                // Envoyer un email à l'administrateur
                $adminEmail = 'sikamagnou@gmail.com';
                Mail::raw("Le compte de l'utilisateur {$user->email} a été bloqué après 3 tentatives de connexion échouées.", function ($message) use ($adminEmail) {
                    $message->to($adminEmail)
                        ->subject('Compte utilisateur bloqué');
                });
            }

            $user->save();
        }

        return response()->json([
            'message' => 'Email ou mot de passe invalide',
        ], 401);
    }

}
