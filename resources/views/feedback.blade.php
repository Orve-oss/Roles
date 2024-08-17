<!DOCTYPE html>
<html lang="en">
<head>

    <title>Ticket Résolu</title>
</head>
<body>
    <h1>Votre ticket a été résolu</h1>
    <p> Destinataire : {{$ticket->client->email}}, </p>
    <p>Concernant mon ticket avec le sujet <strong>"{{$ticket->sujet}}"</strong> :</p>
    <p>Feedback: <strong>{{$description}}</strong></p>
    <p> Merci a vous</p>
    <p>Cordialement, {{$ticket->client->nom_clt}} </p>
</body>
</html>
