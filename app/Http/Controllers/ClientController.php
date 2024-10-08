<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientResource;
use App\Mail\ClientMail;
use App\Mail\VerificationCodeMail;
use App\Models\Client;
use App\Notifications\AccountActivation;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index( Request $request)
    {
        // Nombre d'éléments par page


        try {
            $perPage = $request->get('perPage', 5);
            $list = Client::getAllclients($perPage);
            if ($list->isEmpty()) {
                return response()->json(['message' => 'Aucun Enregistrement']);
            }
            return response()->json($list);
            // if ($list) {
            //     $resp = ClientResource::collection($list);
            //     return response()->json($resp);
            // } elseif ($list->isEmpty()) {
            //     return response()->json(['message' => 'Aucun Enregistrement']);
            // } else {
            //     return response()->json([
            //         'message' => 'Aucun client n\'existe',
            //         'Status' => 'None'
            //     ]);
            // }
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'Status' => 'Fail'
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom_clt' => 'required|string',
            'email' => 'required|string|email|unique:clients',
            'password' => 'nullable|min:4'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $clientdata = [
            'nom_clt' => $request->nom_clt,
            'email' => $request->email,
            'activation_token' => Str::random(60),

        ];
        if ($request->has('password') && !empty($request->password)) {
            $clientdata['password'] = Hash::make($request->password); // Hachage du mot de passe
        } else {
            $clientdata['password'] = Hash::make(Str::random(8));
        }

        $client = Client::create($clientdata);
        $client->assignRole('Client');
        Mail::to($client->email)->send(new ClientMail($client));

        return response()->json([
            'message' => 'Client Crée',
            'status' => 200,
            'client_id' => $client->id
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([

            'email' => 'required|string|email',
            'password' => 'required|min:4|string|confirmed'
        ]);
        $client = Client::where('email', $request->email)->first();
        if (!$client) {
            return response()->json(['message' => 'Client non trouvé']);
        }

        $client->password = Hash::make($request->password);
        // $client->activation_token = null; //Invalide le token après la réinitialisation du mot de passe
        $client->save();

        return response()->json(['message' => 'Compte activé avec succès', 'status' => 200]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        if (Client::where('id', $id)->exists()) {
            $client = Client::getOneClient($id);
            return response()->json([
                'message' => 'Voici le client',
                'client' => $client,
                'status' => 201
            ]);
        } else {
            return response()->json([
                'message' => 'Le client n\'existe pas',
                'status' => 401
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            if (Client::where('id', $id)->exists()) {
                Client::updateClient($request, $id);
                return response()->json([
                    'message' => 'Client modifié'
                ]);
            } else {
                return response()->json([
                    'message' => 'Le client n\'existe pas',
                    'status' => 401
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                "statut" => "fail",
                "Error" => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        if (Client::deleteClient($id)) {
            return response()->json([
                'message' => 'Client supprimé',
                'status' => 200
            ]);
        } else {
            return response()->json([
                'message' => 'Le Client n\'existe pas',
                'status' => 401
            ]);
        }
    }
    public function getProfile()
    {
        $client = Auth::user();
        if (!$client) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }
        $client->roles->pluck('name');
        return response()->json([
            'client' => $client,
            'image' => $client->image ? url('storage/' . $client->image) : null,
        ]);
    }
    public function updateProfile(Request $request)
    {
        $client = Auth::user();
        if (!$client instanceof Client) {
            return response()->json(['error' => 'Invalid client'], 400);
        }
        $client->nom_clt = $request->nom_clt;
        $client->email = $request->email;
        $client->role = $request->role;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('image', 'pulic');
            $client->image = $path;
        }
        $client->save();
        return response()->json(['message' => 'Profil modifié avec succès']);
    }

    public function getEmailFromToken($token)
    {

        $client = Client::where('activation_token', $token)->first();

        if ($client) {
            return response()->json([
                'email' => $client->email
            ]);
        } else {
            return response()->json([
                'message' => 'Token invalide ou expiré'
            ], 404);
        }
    }
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'password' => 'required|string|confirmed',
        ]);

        $client =Client::where('activation_token', $request->token)->first();

        if (!$client) {
            return response()->json([
                'message' => 'Token invalide',
                'status' => 'error'
            ], 400);
        }

        $client->password = Hash::make($request->password);
        $client->activation_token= null;
        $client->save();

        return response()->json([
            'message' => 'Mot de passe changé avec succès',
            'status' => 'success'
        ]);
    }
    public function sendVerificationCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $email = $request->input('email');
        $client = Client::where('email', $email)->first();

        if (!$client) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        // Générer un code de vérification aléatoire de 6 chiffres
        $code = rand(100000, 999999);

        // Enregistrer le code et l'expiration dans la base de données
        DB::table('verificationclient_codes')->updateOrInsert(
            ['client_id' => $client->id],
            [
                'code' => $code,
                'expire_at' => Carbon::now()->addHour(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        // Envoyer l'email avec le code de vérification
        Mail::to($email)->send(new VerificationCodeMail($code));

        return response()->json(['message' => 'Code de vérification envoyé.', 'email'=>$email]);
    }

    // Méthode pour vérifier le code de vérification
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|numeric|digits:6'
        ]);

        $email = $request->input('email');
        $code = $request->input('code');

        $client = Client::where('email', $email)->first();

        if (!$client) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        $verification = DB::table('verificationclient_codes')
            ->where('client_id', $client->id)
            ->where('code', $code)
            ->first();

        if (!$verification) {
            return response()->json(['message' => 'Code invalide'], 400);
        }

        if (Carbon::now()->greaterThan($verification->expire_at)) {
            return response()->json(['message' => 'Code expiré'], 400);
        }
        $token = Str::random(60);
        DB::table('password_resets')->insert([
            'email' => $client->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        return response()->json(['message' => 'Code vérifié avec succès', 'token' => $token]);
    }
    public function generateResetLink($id){
        $client = Client::find($id);
        if (!$client) {
            return response()->json(['message'=> 'Utilisateur non trouvé'], 404);
        }
        $token = Str::random(60);
        DB::table('password_resets')->insert([
            'email' => $client->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);
        $link = 'http://127.0.0.1:8080/changePassword/'. $token. '?email=' .urlencode($client->email);
        Mail::send('emails.reset', ['link' => $link], function ($message) use ($client) {

            $message->to($client->email);

            $message->subject('Réinitialisation du mot de passe');

        });
        return response()->json(['message' => 'Lien de réinitialisation']);
    }
    public function changePassword(Request $request){
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|confirmed',
        ]);
        if ($validator->fails()) {
           return response()->json(['errors' => $validator->errors()], 422);
        }
        $passwordReset = DB::table('password_resets')
        ->where('email', $request->email)
        ->first();
        if (!$passwordReset) {
            return response()->json(['message' => 'Token ou email invalide.'], 404);
        }
        $client = Client::where('email', $request->email)->first();
        if (!$client) {
            return response()->json(['message' => 'Utilisateur non trouvé.'], 404);
        }
        $client->password = Hash::make($request->password);
        $client->login_attempts = 0;
        $client->last_login_attempt = null;
        $client->account_locked_at = null;
        $client->save();
        DB::table('password_resets')->where('email', $request->email)->delete();
        return response()->json(['message' => 'Mot de passe réinitialisé avec succès']);
    }
}
