<!DOCTYPE html>
<html>
<head>
    <title>Nouveau Ticket Créé</title>
</head>
<body>
    <h1>Nouveau Ticket Créé avec une priorité <strong>{{$ticket->priorite->niveau}}</strong></h1>
    <p>Crée par le client <a href="mailto:{{$ticket->client->email}}">{{$ticket->client->email}}</a></p>
    <p><strong>Sujet :</strong> {{ $ticket->sujet }}</p>
    <p><strong>Description :</strong> {{ $ticket->description }}</p>
    <p>Merci de prendre les mesures nécessaires.</p>
    <p>Cliquez <a href="http://localhost:8080/connexionAdmin">ici</a> pour accéder à votre expace</p>
</body>
</html>
