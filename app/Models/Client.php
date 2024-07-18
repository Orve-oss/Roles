<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $fillable = [
        'nom_clt',
        'email_clt'
    ];

    protected $table = 'clients';

    public static function getAllclients()
    {
        return Self::all();
    }

    public static function addClient($request)
    {
        $clt = new Self();
        $clt->nom_clt = $request->nom_clt;
        $clt->email_clt = $request->email_clt;
        $clt->save();
    }

    public static function getOneClient($id)
    {
        return Self::where('id', $id)->get();
    }
    public static function updateClient($request, $id)
    {
        $clt = Client::findOrFail($id);
        $clt->nom_clt = $request->nom_clt;
        $clt->email_clt = $request->email_clt;
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
