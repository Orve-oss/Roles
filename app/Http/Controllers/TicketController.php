<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $list = Ticket::getAllTickets();
        if ($list->isEmpty()) {
            return response()->json(['message' => 'Aucun Enregistrement']);
        }
        return response()->json([
            'message' => 'Liste des tickets',
            'status' => 200,
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
        $validator = Validator::make($request->all(), [
            'sujet' => 'required|string|max:100',
            'description' => 'required|string',
            'service_id' => 'required',
            'type_ticket_id' => 'required',
            'priorite_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $ticket = Ticket::create([
            'sujet'=>$request->sujet,
            'description'=>$request->description,
            'service_id'=>$request->service_id,
            'type_ticket_id'=>$request->type_ticket_id,
            'priorite_id'=>$request->priorite_id,
        ]);
        return response()->json([
            'message' => 'Ticket Crée',
            'status'=> 200,
            'client_id'=> $ticket->id
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        //
    }

    public function assign(Request $request, $id)
    {
    }
}
