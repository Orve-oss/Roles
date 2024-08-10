<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientResource;
use App\Mail\ClientMail;
use App\Models\Client;
use App\Notifications\AccountActivation;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Nombre d'éléments par page


        try {
            $list = Client::getAllclients();
            if ($list) {
                $resp = ClientResource::collection($list);
                return response()->json($resp);
            } elseif ($list->isEmpty()) {
                return response()->json(['message' => 'Aucun Enregistrement']);
            } else {
                return response()->json([
                    'message' => 'Aucun client n\'existe',
                    'Status' => 'None'
                ]);
            }
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
    // public function send($id){
    //     $client = Client::find($id);
    //     if (!$client) {
    //         return response()->json([
    //             'message' => 'Client non trouve',
    //         ]);
    //     }
    //     $client->notify(new AccountActivation($client->activation_token));
    //     return response()->json([
    //                 'message' => 'Voulez vous l\'envoyer le mail d\'activation?',
    //             ]);
    // }

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
}
