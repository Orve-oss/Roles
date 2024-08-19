<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Mail\UserMail;
use App\Models\User;
use \Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Http\Request;
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
        $link = url(`http://localhost:8080`. $token. '?email='.urlencode($user->email));
        Mail::send('Html.view', ['link' => $link], function ($message) use ($user) {

            $message->to($user->email);

            $message->subject('Réinitialisation du mot de passe');

        });
        return response()->json(['message' => 'Lien de réinitialisation']);
    }

}
