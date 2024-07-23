<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
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
            'priorite_id' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $ticketData = $validator->validated();

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('tickets', 'public');
            $ticketData['image'] = $imagePath;
        }

        $ticket = Ticket::create($ticketData);

        return response()->json([
            'message' => 'Ticket créé avec succès',
            'status' => 200,
            'ticket' => $ticket
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

    public function assign(Request $request, Ticket $ticket)
    {
        $request->validate([
            'agent_id' => 'required|exists:users,id'
        ]);

        $agent = User::findOrFail($request->agent_id);
        $ticket->agent_id = $agent->agent_id;
        $ticket->save();
        return response()->json(['message'=> 'Ticket assigné aves succès']);
    }
}
