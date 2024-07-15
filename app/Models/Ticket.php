<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;
    protected $fillable = [
        'sujet',
        'description',
        'user_id',
        'service_id',
        'type_ticket_id',
        'priorite_id'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
    protected $table = 'tickets';
    public static function getAllTickets(){
        return Self::all();
    }
}
