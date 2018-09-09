<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Welcome/Bienvenue</title>
        <style>
            body {
                margin: 0 20px;
                font-family:Helvetica,Arial,sans-serif;
                color:#666666
            }

            h1 {
                color:#555555;
            }

            #logo img {
                margin-left: auto;
                margin-right: auto;
                display: block;
            }
            
            .button {
                background-color: #858585;
                border: none;
                color: white;
                padding: 20px;
                text-align: center;
                text-decoration: none;
                display: inline-block;
                font-size: 16px;
                margin: 4px 2px;
                border-radius: 12px;
            }
        </style>
    </head>
    <body>
        <div id="logo">
            <img src="{{ $message->embed($podmytubeLogo) }}">
        </div>
        <h1> {{ __('emails.welcome_h1_welcome', ['name' => $user->name]) }}</h1>
        <p> @lang('emails.welcome_p_first_line') </p>
        <p> @lang('emails.welcome_p_register') </p>
        
        <a href="{{ url('/') }}" class="button" style="color:white;"> @lang('emails.welcome_a_add_one_channel') </a>
        
    </body>
</html>