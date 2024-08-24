<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Mail\UserMail;
use App\Mail\VerificationCodeMail;
use App\Models\User;
use \Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    // public function login(UserRequest $request)
    // {

    //     try {
    //         $creds =  $request->validated();
    //         if (!Auth::attempt($creds)) {
    //             return response()->json([
    //                 'message' => 'Email ou mot de passe invalide'
    //             ]);
    //         }
    //         $user = Auth::attempt($creds);
    //         // return response()->json([
    //         //     'message' => 'You are logged',
    //         //     'status' => 'success'
    //         // ]);

    //         $user = Auth::user();
    //         $token = $user->createToken('auth_token', ['*']);

    //         return response()->json([
    //             'message'=>'Connexion réussie',
    //             'access_token' => $token, 'token_type' => 'Bearer',
    //             'status'=> 'success',
    //             'user' => [
    //                 'id' => $user->id,
    //                 'name' => $user->name,
    //                 'email' => $user->email,
    //                 'role' => $user->roles->pluck('name')
    //             ]
    //         ]);
    //     } catch (Exception $e) {
    //         return response()->json([
    //             'message' => $e->getMessage(),
    //             'Status' => 'Fail'
    //         ]);
    //     }
    // }

    public function index()
    {
        $list = User::getAllUsers();
        if ($list->isEmpty()) {
            return response()->json(['message' => 'Aucun Enregistrement']);
        }
        return response()->json($list);
    }
    public function store(Request $request)
    {
        try {
            $validator = $request->validate([
                'name' => 'required|string|unique:users,name',
                'email' => 'required|email|unique:users,email',
                'password' => 'nullable|string',
                'role' => 'required|string|exists:roles,name'
            ]);
            if (!$validator) {
                return response()->json([
                    "message" => "La validation a échoué",
                    "status" => "Error"
                ]);
            }
            $token = Str::random(60);
            $userdata = [
                'name' => $request->name,
                'email' => $request->email,
                'reset_token' => $token,

            ];
            if($request->has('password') && !empty($request->password)) {
                $userdata['password'] = Hash::make($request->password);
            } else {
                $userdata['password'] = Hash::make(Str::random(8));
            }


            $user = User::create($userdata);
            $role = Role::where('name', $request->role)->get();
            // $role = Role::findByName($validator['role']);
            $user->assignRole($role);
            Mail::to($user->email)->send(new UserMail($user));
            // $details = [
            //     'name'=>$user->name,
            //     'email'=>$user->email,

            // ];
            return response()->json([
                'message' => 'Utilisateur crée. un mail d\'activation sera envoyé',
                'status' => 'success'
            ]);
        } catch (Exception $e) {
            return response()->json([
                "statut" => "fail",
                "Error" => $e->getMessage()
            ]);
        }
    }

    public function getAgents()
    {
        $agents = User::whereHas('roles', function ($query) {
            $query->where('name', 'Agent');
        })->get();
        return response()->json($agents);
    }

    public function searchUsers(Request $request)
    {
        try {
            $search = $request->all();
            $searchResult = User::query();

            if (isset($search['name'])) {
                $searchResult->where('name', 'like', "%{$search['name']}%");
            }

            if (isset($search['role'])) {
                $searchResult->whereHas('role', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search['role']}%");
                });
            }

            $searchResult = $searchResult->get();

            return response()->json([
                'message' => 'Résultats de la recherche',
                'users' => $searchResult,
                'status' => 'success'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'fail',
                'error' => $e->getMessage()
            ]);
        }
    }
    public function show()
    {
    }
    public function update()
    {
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'Utilisateur archivé']);
    }
    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();
        return response()->json(['message' => 'Utilisateur restoré']);

    }
    public function getArchived()
    {
        $archivedUsers = User::onlyTrashed()->with('roles')->get();
        return response()->json($archivedUsers);
    }
    public function forceDelete($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->forceDelete();
        return response()->json(['message' => 'Utilisateur supprimé définitivement']);
    }
    public function getUserprofile()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }
        $user->roles->pluck('name');
        return response()->json([
            'user' => $user,
            'image' => $user->image ? url('storage/' . $user->image) : null,
        ]);
    }
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        if (!$user instanceof User) {
            return response()->json(['error' => 'Invalid user'], 400);
        }
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('image', 'pulic');
            $user->image = $path;
        }
        $user->save();
        return response()->json(['message' => 'Profil modifié avec succès']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'password' => 'required|string|confirmed',
        ]);

        $user = User::where('reset_token', $request->token)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Token invalide',
                'status' => 'error'
            ], 400);
        }

        $user->password = Hash::make($request->password);
        $user->reset_token = null;
        $user->save();

        return response()->json([
            'message' => 'Mot de passe changé avec succès',
            'status' => 'success'
        ]);
    }
    public function getEmailFromToken($token)
    {

        $user = User::where('reset_token', $token)->first();

        if ($user) {
            return response()->json([
                'email' => $user->email
            ]);
        } else {
            return response()->json([
                'message' => 'Token invalide ou expiré'
            ], 404);
        }
    }
    public function getProfile()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }
        $user->roles->pluck('name');
        return response()->json([
            'user' => $user,
            'image' => $user->image ? url('storage/' . $user->image) : null,
        ]);
    }
    public function generateResetLink($id){
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message'=> 'Utilisateur non trouvé'], 404);
        }
        $token = Str::random(60);
        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);
        $link = 'http://127.0.0.1:8080/change/'. $token. '?email=' .urlencode($user->email);
        Mail::send('emails.reset', ['link' => $link], function ($message) use ($user) {

            $message->to($user->email);

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
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé.'], 404);
        }
        $user->password = Hash::make($request->password);
        $user->login_attempts = 0;
        $user->last_login_attempt = null;
        $user->account_locked_at = null;
        $user->save();
        DB::table('password_resets')->where('email', $request->email)->delete();
        return response()->json(['message' => 'Mot de passe réinitialisé avec succès']);
    }
    public function sendVerificationCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $email = $request->input('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        // Générer un code de vérification aléatoire de 6 chiffres
        $code = rand(100000, 999999);

        // Enregistrer le code et l'expiration dans la base de données
        DB::table('verification_codes')->updateOrInsert(
            ['user_id' => $user->id],
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

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        $verification = DB::table('verification_codes')
            ->where('user_id', $user->id)
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
            'email' => $user->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        return response()->json(['message' => 'Code vérifié avec succès', 'token' => $token]);
    }
    public function resetPasswordUser(Request $request)
{
    // Valider les entrées
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'token' => 'required|string',
        'password' => 'required|string|min:8|confirmed', // 'confirmed' s'attend à un champ 'password_confirmation'
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Récupérer les données de la requête
    $email = $request->input('email');
    $token = $request->input('token');
    $password = $request->input('password');

    // Vérifier si le token de réinitialisation est valide
    $passwordReset = DB::table('password_resets')->where([
        ['email', $email],
        ['token', $token],
    ])->first();

    if (!$passwordReset) {
        return response()->json(['message' => 'Le lien de réinitialisation est invalide ou expiré.'], 400);
    }

    // Trouver le client par email
    $user = User::where('email', $email)->first();

    if (!$user) {
        return response()->json(['message' => 'Utilisateur non trouvé.'], 404);
    }

    // Mettre à jour le mot de passe du client
    $user->password = Hash::make($password);
    $user->save();

    // Supprimer l'enregistrement de réinitialisation pour éviter l'usage multiple
    DB::table('password_resets')->where('email', $email)->delete();

    return response()->json(['message' => 'Votre mot de passe a été réinitialisé avec succès.'], 200);
}


}
