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

    public static function getAllclients(){
        return Self::all();
    }

    public static function addClient($request){
        $clt = new Self();
        $clt->nom_clt = $request->nom_clt;
        $clt->email_clt = $request->email_clt;
        $clt->save();
    }
}
