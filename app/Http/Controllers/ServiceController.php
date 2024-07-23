<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $list = Service::getAllservices();
        if ($list->isEmpty()) {
            return response()->json(['message'=>'Aucun Enregistrement']);
        }
        return response()->json($list);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nom_service'=>'required|string|unique:services,nom_service'
        ]);
        if ($validator->fails()) {
            return response()->json([
                "message" => "La validation a échoué",
                "status" => "Error"
            ]);
        }
        Service::addService($request);
        return response()->json([
            'message'=> 'Service crée',
            'status'=>201
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        if (Service::where('id', $id)->exists()) {
            $service = Service::getOneService($id);
            return response()->json([
                'message'=> 'Voici le service',
                'service'=>$service,
                'status'=>201
            ]);
        } else {
            return response()->json([
                'message'=> 'Le service n\'existe pas',
                'status'=>401
            ]);
        }


    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            if (Service::where('id', $id)->exists()) {
                Service::updateService($request, $id);
                return response()->json([
                    'message'=> 'Service modifié',
                    'status'=>201
                ]);
            } else {
                return response()->json([
                    'message'=> 'Le service n\'existe pas',
                    'status'=>401
                ]);
            }

        } catch (Exception $e) {
            return response()->json([
                "statut" => "fail",
                "Error" => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (Service::where('id', $id)->exists()) {
            Service::deleteService($id);
            return response()->json([
                'message'=> 'Service supprimé',
                'status'=>201
            ]);
        } else {
            return response()->json([
                'message'=> 'Le service n\'existe pas',
                'status'=>401
            ]);
        }
    }
}
