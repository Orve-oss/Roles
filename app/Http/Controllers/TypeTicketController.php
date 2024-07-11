<?php

namespace App\Http\Controllers;

use App\Models\TypeTicket;
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
            return response()->json(['message'=>'Aucun Enregistrement']);
        }
        return response()->json([
            'message'=>'Liste des types',
            'Types de services'=>$list,
            'status'=> 200,
        ]);
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
            'libelle'=>'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json([
                "message" => "La validation a échoué",
                "status" => "Error"
            ]);
        }
        TypeTicket::addType($request);
        return response()->json([
            'message'=> 'Type de ticket crée',
            'status'=>200
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
                'message'=> 'Type de ticket',
                'service'=>$type,
                'status'=>200
            ]);
        } else {
            return response()->json([
                'message'=> 'Le type de ticket n\'existe pas',
                'status'=>401
            ]);
        }
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TypeTicket $typeTicket)
    {
        //
    }
}
