<?php

namespace App\Http\Controllers;

use App\Models\TypeTicket;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TypeTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $list = TypeTicket::getAlltypes();
        if ($list->isEmpty()) {
            return response()->json(['message' => 'Aucun Enregistrement']);
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
        $validator = Validator::make($request->all(), [
            'libelle' => 'required|string|max:50'
        ]);
        if ($validator->fails()) {
            return response()->json([
                "message" => "La validation a échoué",
                "status" => "Error"
            ]);
        }
        TypeTicket::addType($request);
        return response()->json([
            'message' => 'Type de ticket crée',
            'status' => 200
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        if (TypeTicket::where('id', $id)->exists()) {
            $type = TypeTicket::getOneType($id);
            return response()->json([
                'message' => 'Type de ticket',
                'service' => $type,
                'status' => 200
            ]);
        } else {
            return response()->json([
                'message' => 'Le type de ticket n\'existe pas',
                'status' => 401
            ]);
        }
    }
    public function getTypesByService($serviceId)
    {
        $types = TypeTicket::where('service_id', $serviceId)
            ->with('problemes')
            ->get();
        return response()->json($types);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TypeTicket $typeTicket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            if (TypeTicket::where('id', $id)->exists()) {
                TypeTicket::updateType($request, $id);
                return response()->json([
                    'message' => 'Type modifié',
                    'status' => 200
                ]);
            } else {
                return response()->json([
                    'message' => 'Le type n\'existe pas',
                    'status' => 401
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
        //
    }
}
