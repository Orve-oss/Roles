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
        'service_id',
        'type_ticket_id',
        'priorite_id'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function type(){
        return $this->belongsTo(TypeTicket::class);
    }
    public function service(){
        return $this->belongsTo(Service::class);
    }
    public function priorite(){
        return $this->belongsTo(Priorite::class);
    }
    protected $table = 'tickets';
    public static function getAllTickets(){
        return Self::all();
    }
}
