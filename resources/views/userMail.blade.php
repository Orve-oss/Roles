<!DOCTYPE html>
<html>
    <head>
        <title>Password reset</title>
        <style>
            .button{
                display: inline-block;
                padding: 10px 20px;
                font-size: 16px;
                cursor: pointer;
                text-align: center;
                text-decoration: none;
                color: #fff;
                background-color: #4caf50;
                border: none;
                border-radius: 15px;
                box-shadow: 0 9px #999;
            }
            .button:hover {background-color: #3e8e41}
        </style>
    </head>
    <body>
        <h1> Hello, {{ $user->name }} </h1>
        <p> Veuillez cliquer sur ce bouton pour activer votre compte</p>
        <a href="http://localhost:8080/reset/{{$user->reset_token}}" class="button"> Active Account</a>
        <p>Merci. WEQUIPU-TEAM</p>

    </body>


</html>
