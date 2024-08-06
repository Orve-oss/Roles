<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commentaire extends Model
{
    use HasFactory;
    protected $fillable =['contenu', 'ticket_id'];
    public function ticket(){
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }
}
