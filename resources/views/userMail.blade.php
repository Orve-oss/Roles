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
        <h1> Hello, {{ $details['email'] }} </h1>
        <p> Please click the button below to active your account</p>
        <a href="{{ $details['reset_link']}}" class="button"> Active Account</a>
        <p>Thanks. WEQUIPU-TEAM</p>

    </body>


</html>
