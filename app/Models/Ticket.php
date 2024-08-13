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
        'image',
        // 'user_id',
        'client_id',
        'service_id',
        'type_ticket_id',
        'priorite_id'
    ];
    public function comments(){
        return $this->hasMany(Commentaire::class);
    }
    public function historiques(){
        return $this->hasMany(Historique::class);
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
    public function assignedBy(){
        return $this->belongsTo(User::class, 'assigned_by');
    }
    public function client(){
        return $this->belongsTo(Client::class, 'client_id');
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
    public static function getAllTickets($perPage = 5){
        return Self::with(['type','service', 'priorite'])->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public static function getOneTicket($id){
        return Self::where('id', $id)
                ->with('priorite')
                ->with('type')
                ->with('service')
                ->get();
    }
    public static function deleteTicket($id){
        if (Ticket::where('id', $id)->exists()) {
            $ticket = Ticket::find($id);
            $ticket->delete();
        }
    }
}
