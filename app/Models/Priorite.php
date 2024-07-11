<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Priorite extends Model
{
    use HasFactory;
    protected $fillable = ['niveau'];
    protected $table = 'priorites';
    public static function getAllpriorites(){
        return Self::all();
    }
    public static function addPriorite($request){
        $priorite = new Self();
        $priorite->niveau = $request->niveau;
        $priorite->description = $request->description;
        $priorite->save();
    }
    public static function getOnePriorite($id){
        return Self::where('id', $id)->get();
    }
    public static function updatePriorite($request, $id){
        $priorite = Priorite::findOrFail($id);
        $priorite->niveau = $request->niveau;
        $priorite->description = $request->description;
        $priorite->update();
    }
    public static function deletePriorite($id){
        $prt = Priorite::findOrFail($id);
        $prt->delete();
    }
}
