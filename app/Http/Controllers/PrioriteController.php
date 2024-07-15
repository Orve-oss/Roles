<?php

namespace App\Http\Controllers;

use App\Models\Priorite;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PrioriteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $list = Priorite::getAllservices();
        if ($list->isEmpty()) {
            return response()->json(['message'=>'Aucun Enregistrement']);
        }
        return response()->json([
            'message'=>'Liste des prorites',
            'priorites'=>$list,
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
        $rule = [
            'niveau'=>'required|string|unique:priorites,niveau'
        ];
        $message = [
            'niveau.unique'=>'le niveau entré existe déjà'
        ];
        $validator = Validator::make($request->all(), $rule, $message);
        if ($validator->fails()) {
            return response()->json([
                "message" => "La validation a échoué",
                "status" => "Error"
            ]);
        }
        Priorite::addService($request);
        return response()->json([
            'message'=> 'Priorite crée',
            'status'=>201
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Priorite $priorite)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Priorite $priorite)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            if (Priorite::where('id', $id)->exists()) {
                Priorite::updatePriorite($request, $id);
                return response()->json([
                    'message'=> 'Priorite modifié',
                    'status'=>200
                ]);
            } else {
                return response()->json([
                    'message'=> 'La priorité n\'existe pas',
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
        if (Priorite::where('id', $id)->exists()) {
            Priorite::deletePriorite($id);
            return response()->json([
                'message'=> 'Priorite supprimé',
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
