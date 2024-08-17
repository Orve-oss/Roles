<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeTicket extends Model
{
    use HasFactory;

    public function problemes(){
        return $this->hasMany(Probleme::class);
    }
    public function service(){
        return $this->belongsTo(Service::class);
    }

    protected $fillable = ['libelle', 'service_id'];
    protected $table = 'type_tickets';
    public static function getAlltypes(){
        return Self::all();
    }
    public static function addType($request){
        $type = new Self();
        $type->libelle = $request->libelle;
        $type->save();
    }
    public static function getOneType($id){
        return Self::where('id', $id)->get();
    }
    public static function updateType($request, $id){
        $type = TypeTicket::findOrFail($id);
        $type->libelle = $request->libelle;
        $type->update();
    }

    public static function deleteType($id){
        $srvc = TypeTicket::findOrFail($id);
        $srvc->delete();
    }
}
