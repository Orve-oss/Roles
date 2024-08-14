<?php

namespace App\Http\Controllers;

use App\Mail\AllMail;
use App\Mail\ResolutionMail;
use App\Mail\TicketMailDescription;
use App\Mail\TicketReassign;
use App\Mail\TicketReportMail;
use App\Models\Historique;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('perPage', 5);
        $list = Ticket::getAllTickets($perPage);
        if ($list->isEmpty()) {
            return response()->json(['message' => 'Aucun Enregistrement']);
        }
        return response()->json($list);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function getTicketRapport($ticketId)
    {
        $ticket = Ticket::with('user', 'client', 'type', 'priorite', 'service')->findOrFail($ticketId);
        return response()->json([
            'ticket_id' => $ticket->id,
            'statut' => $ticket->status,
            'description' => $ticket->description,
            'priorite' => $ticket->priorite->niveau,
            'service' => $ticket->service->nom_service,
            'type' => $ticket->type->libelle,
            'agent' => $ticket->user->email,
            'client' => $ticket->client->email,
            'created_at' => $ticket->created_at,
            'updated_at' => $ticket->updated_at,
            'commentaire' => $ticket->comments
        ]);
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
            'client_id' => 'required|exists:clients,id',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:2048000'
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
        // $this->notifyUser($ticket);

        return response()->json([
            'message' => 'Ticket créé avec succès',
            'status' => 200,
            'ticket' => $ticket
        ]);
    }
    private function notifyUser($ticket)
    {
        $users = User::where('role', 'Agent')
            ->orWhere('role', 'Admin')
            ->get();
        foreach ($users as $key => $user) {
            Mail::to($user->email)->send(new AllMail());
        }
    }
    public function getUnassigned()
    {
        $tickets = Ticket::whereNull('user_id')
            ->with(['type', 'priorite', 'service'])
            ->get();
        return response()->json($tickets);
    }
    public function assignToAgent(Request $request, $ticketId)
    {
        $agentId = $request->input('agentId');
        $ticket = Ticket::find($ticketId);

        if ($ticket) {
            $ticket->user_id = $agentId;
            $ticket->status = 'Assigné';
            $ticket->save();
            return response()->json(['message' => 'Ticket assigné', 'status' => 200]);
        } else {
            return response()->json(['message' => 'Ticket non trouvé', 'status' => 404]);
        }
    }

    public function close(Ticket $ticket)
    {
        $ticket->status = 'Fermé';
        $ticket->save();

        return response()->json(['message' => 'Ticket fermé avec succès'], 200);
    }


    public function getTicketByStatus($status, Request $request)
    {
        $perPage = $request->get('perPage', 5);

        // Récupère les tickets par statut avec pagination
        $tickets = Ticket::where('status', $status)->with(['type', 'service', 'priorite'])->orderBy('created_at', 'desc')->paginate($perPage);

        // Retourne la réponse en JSON avec les données paginées
        return response()->json($tickets);
        // $tickets = Ticket::where('status', $status)->with(['type', 'priorite', 'service'])->get();
        // return response()->json($tickets);
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
            'status' => 'required'
        ]);
        $ticket = Ticket::findOrFail($id);
        $ticket->status = $request->status;
        $ticket->save();
        return response()->json(['message' => 'Statut mis à jour']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function getdashboard($agentId)
    {
        $services = Service::all();
        $dashboardData = [];
        foreach ($services as $key => $service) {
            $tickets = Ticket::where('user_id', $agentId)
                ->where('service_id', $service->id)
                ->with(['type', 'priorite', 'service'])->get();
            $totalAssigned = $tickets->count();
            $pending = $tickets->where('status', 'En attente')->count();
            $progress = $tickets->where('status', 'En cours')->count();
            $resolved = $tickets->where('status', 'Résolu')->count();

            $dashboardData[] = [
                'service' => $service->nom_service,
                'totalAssigned' => $totalAssigned,
                'chartSeries' => [
                    $totalAssigned > 0 ? 100 : 0,
                    $totalAssigned > 0 ? ($pending / $totalAssigned) * 100 : 0,
                    $totalAssigned > 0 ? ($progress / $totalAssigned) * 100 : 0,
                    $totalAssigned > 0 ? ($resolved / $totalAssigned) * 100 : 0,

                ]
            ];


        }
        return response()->json(['serviceData' => $dashboardData]);
    }
    public function getdashboardClient($clientId)
    {
        $services = Service::all();
        $dashboardData = [];
        foreach ($services as $key => $service) {
            $tickets = Ticket::where('client_id', $clientId)
                ->where('service_id', $service->id)
                ->with(['type', 'priorite', 'service'])->get();
            $totalCreated = $tickets->count();
            $pending = $tickets->where('status', 'En attente')->count();
            $progress = $tickets->where('status', 'En cours')->count();
            $resolved = $tickets->where('status', 'Résolu')->count();

            $dashboardData[] = [
                'service' => $service->nom_service,
                'created' => $totalCreated,
                'pending' => $pending,
                'progress' => $progress,
                'resolved' => $resolved

            ];


        }
        return response()->json( $dashboardData);
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
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();
        $ticket->comments()->delete();
        return response()->json(['message' => 'Ticket archivé']);
    }

    public function assign(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'assigned_by' => 'required|exists:users,id'

        ]);

        $ticket = Ticket::findOrFail($id);
        $ticket->user_id = $request->user_id;
        $ticket->assigned_by = $request->assigned_by;
        $ticket->status = 'Assigné';
        $ticket->save();
        return response()->json(['message' => 'Ticket assigné aves succès']);
    }

    public function getticketsByAgent($agentId)
    {
        $tickets = Ticket::where('user_id', $agentId)->with(['type', 'priorite', 'service'])->get();
        return response()->json($tickets);
    }
    public function getStatusByAgent($agentId, $status)
    {
        $tickets = Ticket::where('user_id', $agentId)->with(['type', 'priorite', 'service'])
            ->where('status', $status)->with(['type', 'priorite', 'service'])
            ->get();
        return response()->json($tickets);
    }
    public function getStatusByClient($clientId, $status)
    {
        $tickets = Ticket::where('client_id', $clientId)->with(['type', 'priorite', 'service'])
            ->where('status', $status)->with(['type', 'priorite', 'service'])
            ->get();
        return response()->json($tickets);
    }
    public function getTicketsByservice($servicename)
    {
        $service = Service::where('nom_service', $servicename)->first();
        if (!$service) {
            return response()->json(['error' => 'Service not found'], 400);
        }
        $tickets = Ticket::where('service_id', $service->id)->get();
        return response()->json($tickets);
    }
    public function sendEmail($id)
    {
        $ticket = Ticket::findOrFail($id);
        $admin = $ticket->assignedBy;
        if (!$admin) {
            return response()->json(['message' => 'Admin not found']);
        }
        Mail::to($admin->email)->send(new TicketReassign($ticket));
        return response()->json(['message' => 'Email envoyé']);
    }

    public function email(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ticket_id' => 'required|exists:tickets,id',
            'work_description' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $ticket = Ticket::find($request->ticket_id);
        $client = $ticket->client;
        $workDescription = $request->work_description;
        try {
            Mail::to($client->email)->send(new TicketMailDescription($ticket, $workDescription));
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
    public function sendResolution($id)
    {
        $ticket = Ticket::findOrFail($id);
        $client = $ticket->client;
        if (!$client || !$client->email) {
            return response()->json(['message' => 'Email du client introuvable'], 404);
        }

        Mail::to($client->email)->send(new ResolutionMail($ticket));
        return response()->json([
            'message' => 'Mail envoyé',
            'status' => 200
        ]);
    }
    public function generateReport($ticketId)
    {
        try {
            $ticket = Ticket::findOrFail($ticketId);
            $report = Historique::create([
                'ticket_id' => $ticket->id,
                'description' => 'Rapport du ticket ID ' . $ticket->id,
                
            ]);
            Mail::to($ticket->client->email)->send(new TicketReportMail($ticket, $report));
            return response()->json([
                'message' => 'Rapport créé',
                'report' => $report
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la création du rapport',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getTicketByClient($clientId)
    {

        $ticket = Ticket::where('client_id', $clientId)->with(['type', 'priorite', 'service'])->get();
        return response()->json($ticket);
    }
    public function getComments($ticketId)
    {
        $ticket = Ticket::findOrFail($ticketId);
        $comments = $ticket->comments;
        return response()->json($comments);
    }
}
