<?php

namespace App\Http\Controllers;

use App\Mail\ResolutionMail;
use App\Mail\TicketReassign;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
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
            'sujet' => 'required|string|max:100',
            'description' => 'required|string',
            'service_id' => 'required',
            'type_ticket_id' => 'required',
            'priorite_id' => 'required',
            'client_id'=> 'required|exists:clients,id',
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
        $ticketData['status'] = 'En attente';

        $ticket = Ticket::create($ticketData);

        return response()->json([
            'message' => 'Ticket créé avec succès',
            'status' => 200,
            'ticket' => $ticket
        ]);
    }

    public function getTicketByStatus($status)
    {
        $tickets = Ticket::where('status', $status)->get();
        return response()->json($tickets);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        if (Ticket::where('id', $id)->exists()) {
            $ticket = Ticket::getOneTicket($id);
            return response()->json($ticket);
        } else {
            return response()->json([
                'message' => 'Le ticket n\'existe pas',
                'status' => 401
            ]);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status'=>'required'
        ]);
        $ticket = Ticket::findOrFail($id);
        $ticket->status = $request->status;
        $ticket->save();
        return response()->json(['message' => 'Statut mis à jour']);
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
    public function update(Request $request, $id)
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
        $ticket = Ticket::find($id);

        if (!$ticket) {
            return response()->json(['message' => 'Ticket non trouvé'], 404);
        }
        $ticketData = $validator->validated();

        if ($request->hasFile('image')) {

            if ($ticket->image) {
                Storage::disk('public')->delete($ticket->image);
            }

            $imagePath = $request->file('image')->store('tickets', 'public');
            $ticketData['image'] = $imagePath;
        }

        $ticket->update($ticketData);

        return response()->json([
            'message' => 'Ticket mis à jour avec succès',
            'status' => 200,
            'ticket' => $ticket
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (Ticket::deleteTicket($id)) {
            return response()->json([
                'message' => 'Ticket supprimé',
                'status' => 200
            ]);
        } else {
            return response()->json([
                'message' => 'Le Ticket n\'existe pas',
                'status' => 401
            ]);
        }
    }

    public function assign(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $ticket = Ticket::findOrFail($id);
        $ticket->user_id = $request->user_id;
        $ticket->assigned_by = auth()->user()->id;
        $ticket->save();
        return response()->json(['message' => 'Ticket assigné aves succès']);
    }

    public function getticketsByAgent($agentId)
    {
        $tickets = Ticket::where('user_id', $agentId)->with(['type', 'priorite', 'service'])->get();
        return response()->json($tickets);
    }
    public function getTicketsByservice($servicename){
        $service = Service::where('nom_service', $servicename)->first();
        if (!$service) {
            return response()->json(['error' => 'Service not found'], 400);
        }
        $tickets = Ticket::where('service_id', $service->id)->get();
        return response()->json($tickets);
    }
    public function sendEmail($id){
        $ticket = Ticket::findOrFail($id);
        $admin = $ticket->assignedBy;
        if (!$admin) {
            return response()->json(['message'=>'Admin not found']);
        }
        Mail::to($admin->email)->send(new TicketReassign($ticket));
        return response()->json(['message'=>'Email envoyé']);
    }
    public function sendResolution($id){
        $ticket = Ticket::findOrFail($id);
        $client = $ticket->client;
        if (!$client || !$client->email) {
            return response()->json(['message' => 'Email du client introuvable'], 404);
        }
        Mail::to($client->email)->send(new ResolutionMail($ticket));
    }
}
