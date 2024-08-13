<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class Client extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasApiTokens, SoftDeletes;
    protected $fillable = [
        'nom_clt',
        'email',
        'password',
        'activation_token'
    ];

    protected $table = 'clients';
    protected $guard_name = 'web';

    public function role(){
        return $this->belongsTo(Role::class);
    }

    public static function getAllclients($perPage = 5)
    {
        return Self::query()->orderBy('created_at', 'desc')->paginate($perPage);
    }
    public static function getOneClient($id)
    {
        return Self::where('id', $id)->get();
    }
    public static function updateClient($request, $id)
    {
        $clt = Client::findOrFail($id);
        $clt->nom_clt = $request->nom_clt;
        $clt->email = $request->email;
        if ($request->has('password')) {
            $clt->password = bcrypt($request->password);
        }
        $clt->update();
    }
    public static function deleteClient($id)
    {
        if (Client::where('id', $id)->exists()) {
            $clt = Client::find($id);
            $clt->delete();

        }
    }
}
