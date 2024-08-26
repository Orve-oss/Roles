<!DOCTYPE html>
<html>
<head>
    <title>Nouveau Ticket Assigné</title>
</head>
<body>
    <h1>Un nouveau ticket vous a été assigné avec une priorité <strong>{{$ticket->priorite->niveau}}</strong></h1>
    <p><strong>Sujet :</strong> {{ $ticket->sujet }}</p>
    <p><strong>Description :</strong> {{ $ticket->description }}</p>
    <p><strong>Priorité :</strong> {{ $ticket->priorite->niveau }}</p>
    <p><strong>Assigné par :</strong> {{ $ticket->assignedBy->email }}</p>
    <p>Cliquez <a href="http://localhost:8080/accueil/agent">ici</a> pour accéder à votre espace</p>
    <p>Merci de prendre en charge ce ticket dès que possible.</p>
</body>
</html>
