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
}
