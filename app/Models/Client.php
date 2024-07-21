<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Client extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;
    protected $fillable = [
        'nom_clt',
        'email',
        'password'
    ];

    protected $table = 'clients';
    protected $guard_name = 'clients';

    public static function getAllclients()
    {
        return Self::all();
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
