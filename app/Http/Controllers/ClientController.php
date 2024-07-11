<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientResource;
use App\Models\Client;
use Exception;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $list = Client::getAllclients();
            if ($list) {
                $resp = ClientResource::collection($list);
                return response()->json([
                    'message' => 'Liste des Clients',
                    'Clients'=> $resp,
                    'Status'=> 201
                ]);
            } elseif($list->isEmpty()) {
                return response()->json(['message'=>'Aucun Enregistrement']);
            }
            else{
                return response()->json([
                    'message' => 'Aucun client n\'existe',
                    'Status'=> 'None'
                ]);
            }

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'Status'=> 'Fail'
            ]);
        }

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
        try {
            Client::addClient($request);
            return response()->json([
                'message' => 'Liste des Clients',
                'Client'=> Client::addClient($request),
                'Status'=> 201
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'Status'=> 'Fail'
            ]);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        //
    }
}
