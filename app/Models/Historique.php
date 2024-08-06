<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Historique extends Model
{
    use HasFactory;
    protected $fillable = ['description', 'ticket_id'];
    public function tickets(){
        return $this->hasMany(Ticket::class);
    }

    public function ticket(){
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }
    public static function getAllHistory(){
        return Self::all();
    }

}
