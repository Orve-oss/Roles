<!DOCTYPE html>
<html lang="en">
<head>

    <title>Ticket Résolu</title>
</head>
<body>
    <h1>Votre ticket a été résolu</h1>
    <p> Bonjour {{$ticket->client->nom_clt}}, </p>
    <p>Votre ticket avec le sujet "{{$ticket->sujet}}" a été résolu</p>
    <p>N'hesitez pas à consulter le rapport de résolution de votre ticket</p>
    <p> Cordialement, l'équipe WEQUIPU</p>
</body>
</html>
