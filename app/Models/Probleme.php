<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Probleme extends Model
{
    use HasFactory;
    protected $fillable = ['libelle', 'type_ticket_id'];

    public function typeTicket()
    {
        return $this->belongsTo(TypeTicket::class);
    }
}
