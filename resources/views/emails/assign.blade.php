<!DOCTYPE html>
<html>
<head>
    <title>Nouveau Ticket Assigné</title>
</head>
<body>
    <h1>Un nouveau ticket vous a été assigné</h1>
    <p><strong>Sujet :</strong> {{ $ticket->sujet }}</p>
    <p><strong>Description :</strong> {{ $ticket->description }}</p>
    <p><strong>Priorité :</strong> {{ $ticket->priorite->niveau }}</p>
    <p><strong>Assigné par :</strong> {{ $ticket->assignedBy->email }}</p>
    <p>Merci de prendre en charge ce ticket dès que possible.</p>
</body>
</html>
