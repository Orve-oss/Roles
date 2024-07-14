<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use \Illuminate\Support\Facades\Validator;
use Exception;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

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

    public function index(){
        $list = User::getAllUsers();
        if ($list->isEmpty()) {
            return response()->json(['message'=>'Aucun Enregistrement']);
        }
        return response()->json([
            'message'=>'Liste des utilisateurs',
            'users'=>$list,
            'status'=> 200,
        ]);
    }
    public function store(Request $request){
        try {
            $validator = $request->validate([
                'name'=>'required|string|unique:users,name',
                'email'=>'required|email|unique:users,email',
                'password'=>'required|string',
                'role'=>'required|string|exists:roles,name'
            ]);
            if (!$validator) {
                return response()->json([
                    "message" => "La validation a échoué",
                    "status" => "Error"
                ]);
            }
            $user = User::create([
                'name'=>$validator['name'],
                'email'=>$validator['email'],
                'password'=>$validator['password'],
            ]);
            $role = Role::findByName($validator['role']);
            $user->assignRole($role);
            return response()->json([
                'message'=> 'Utilisateur crée',
                'user'=>$user,
                'status'=>201
            ]);
        } catch (Exception $e) {
            return response()->json([
                "statut" => "fail",
                "Error" => $e->getMessage()
            ]);
        }


    }
    public function show(){}
    public function update(){}
    public function destroy(){}
}
