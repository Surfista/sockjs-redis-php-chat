<?php

require __DIR__ . '/vendor/predis/predis/src/Autoloader.php';
Predis\Autoloader::register();
$redis = new Predis\Client();

if(isset($_POST['message']))
{
    $redis->publish('chat-message', $_POST['message']);
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Chat</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="UTF-8" />

        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
        <script type="text/javascript" src="http://code.jquery.com/jquery-2.1.1.min.js"></script>
        <script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
        <script src="http://cdn.sockjs.org/sockjs-0.3.min.js"></script>

        <style>
            body {
                margin:10px;
            }
            #chat-body
            {
                height:500px;
                overflow: auto;
                border:1px solid #666;
                margin-bottom:10px;
                padding:5px;
            }
        </style>

        <script type="text/javascript">

            var sockJS;
            var interval = 2000;

            var initSocket = function(){
                
                sockJS = new SockJS('/chat');
                sockJS.onopen    = function()  {
                    console.log('[*] open', sockJS.protocol);

                };

                sockJS.onmessage = function(e) {
                    console.log('[.] message', e.data);
                    $("#chat-body").append("<div>"+ e.data +"</div>");
                };

                sockJS.onclose   = function()  {
                    console.log('[*] close');

                    interval+=100;
                    setTimeout(initSocket, interval);
                };
            };

            initSocket();

            $(function(){
                $("#send").click(function(){
                    var val = $("#input-area").val();
                    $("#input-area").val('');

                    $.post(document.location.toString(), {
                        message: val
                    });
                });

                $("#input-area").keyup(function(e){
                    if(e.keyCode == 13)
                    {
                        $("#send").click();
                    }
                });
            });
        </script>
    </head>
    <body>
        <div id="chat-body">

        </div>

        <div class="input-group">

            <input type="text" class="form-control" id="input-area">
            <span class="input-group-btn">
                <button class="btn btn-primary" id="send" type="button">Send!</button>
            </span>
        </div><!-- /input-group -->
    </body>
</html>