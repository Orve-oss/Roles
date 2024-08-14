<!DOCTYPE html>
<html>
<head>
    <title>Rapport du Ticket </title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .invoice-title {
            margin-bottom: 20px;
        }
        .float-end {
            float: right;
        }
        .font-size-16 {
            font-size: 16px;
        }
        .mb-4 {
            margin-bottom: 1.5rem;
        }
        hr {
            border-top: 1px solid #eee;
        }
        .mt-3 {
            margin-top: 1rem;
        }
        .text-sm-end {
            text-align: right;
        }
        .p-2 {
            padding: 0.5rem;
        }
        .table {
            width: 100%;
            margin-bottom: 1rem;
            border-collapse: collapse;
        }
        .table th, .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }
        .table th {
            text-align: left;
        }
        .d-print-none {
            display: none;
        }
        .float-end {
            float: right;
        }
        .btn {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            text-decoration: none;
            display: inline-block;
            margin: 5px 0;
        }
        .btn-success {
            background-color: #28a745;
        }
        .btn-primary {
            background-color: #007bff;
        }
        .btn .fa {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="invoice-title">
        <h4 class="float-end font-size-16">Ticket N°{{ $ticket->id }}</h4>
        <div class="mb-4">
            <img src="{{ public_path('assets/images/logo.jpg') }}" alt="logo" height="20" />
        </div>
    </div>
    <hr />
    <div>
        <div class="mt-3">
            <address>
                <strong>Au client: </strong>
                {{ $ticket->client->email }}
            </address>
        </div>
        <div class="mt-3 text-sm-end">
            <address>
                <strong>Résolu le:</strong>
                <br />{{ \Carbon\Carbon::parse($ticket->created_at)->format('d/m/Y') }}
                <br />
            </address>
        </div>
    </div>
    <div class="p-2 mt-3">
        <h3 class="font-size-16">Rapport du ticket</h3>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 100px; margin-right: 50px;">Index</th>
                    <th style="padding-left: 300px;">Libelle</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Statut :</td>
                    <td style="padding-left: 300px;">{{ $ticket->status }}</td>
                </tr>
                <tr>
                    <td>Description :</td>
                    <td style="padding-left: 300px;">{{ $ticket->description }}</td>
                </tr>
                <tr>
                    <td>Priorité :</td>
                    <td style="padding-left: 300px;">{{ $ticket->priorite->niveau }}</td>
                </tr>
                <tr>
                    <td>Type :</td>
                    <td style="padding-left: 300px;">{{ $ticket->type->libelle}}</td>
                </tr>
                <tr>
                    <td>Service :</td>
                    <td style="padding-left: 300px;">{{ $ticket->service->nom_service }}</td>
                </tr>
                <tr>
                    <td>Attribué à :</td>
                    <td style="padding-left: 300px;">{{ $ticket->user->email}}</td>
                </tr>
                <tr>
                    <td>Ouvert le :</td>
                    <td style="padding-left: 300px;">{{ \Carbon\Carbon::parse($ticket->created_at)->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td>Fermé le :</td>
                    <td style="padding-left: 300px;">{{ \Carbon\Carbon::parse($ticket->updated_at)->format('d/m/Y') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
