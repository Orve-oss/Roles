<!DOCTYPE html>
<html>
    <head>
        <title> Evolution de votre ticket</title>
    </head>
    <body>
        <h1>Bonjour , {{$ticket->client->nom_clt}} </h1>
        <p> Votre ticket avec le sujet <strong> {{$ticket->sujet}} </strong></p>
        <p><strong>Description:</strong></p>
        <p> {{$workDescription}} </p>
        <p>Merci pour votre patience. Cordialement, L'équipe de support</p>
    </body>
</html>
