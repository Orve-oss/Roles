<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom_service'
    ];
    protected $table = 'services';

    public function tickets(){
        return $this->hasMany(Ticket::class);
    }
    public static function getAllservices(){
        return Self::all();
    }
    public static function addService($request){
        $src = new Self();
        $src->nom_service = $request->nom_service;
        $src->description = $request->description;
        $src->save();
    }

    public static function getOneService($id){
        return Self::where('id', $id)->get();
    }

    public static function updateService($request, $id){
        $service = Service::findOrFail($id);
        $service->nom_service = $request->nom_service;
        $service->description = $request->description;
        $service->update();
    }

    public static function deleteService($id){
        $srvc = Service::findOrFail($id);
        $srvc->delete();
    }
}
