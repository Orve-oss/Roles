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
        return $this->belongsTo(User::class, 'user_id');
    }
    public function type(){
        return $this->belongsTo(TypeTicket::class, 'type_ticket_id');
    }
    public function service(){
        return $this->belongsTo(Service::class, 'service_id');
    }
    public function priorite(){
        return $this->belongsTo(Priorite::class, 'priorite_id');
    }
    protected $table = 'tickets';
    public static function getAllTickets(){
        return Self::with(['type','service', 'priorite'])->get();
    }

    public static function getOneTicket($id){
        return Self::where('id', $id)
                ->with('priorite')
                ->with('type')
                ->with('service')
                ->get();
    }
}
