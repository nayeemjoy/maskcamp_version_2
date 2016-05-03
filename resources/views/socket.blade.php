<!DOCTYPE html>
<html>
    <head>
        <title>Laravel</title>

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                display: table;
                font-weight: 100;
                font-family: 'Lato';
            }

            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-size: 96px;
            }
        </style>
    </head>
    <body>
        <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
        <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
        <script src="https://cdn.socket.io/socket.io-1.3.4.js"></script>
 
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2" >
                  <div id="messages" ></div>
                </div>
            </div>
        </div>
        <script>
            var socket = io.connect('http://localhost:8890');
            socket.on('message', function (data) {
                $( "#messages" ).append( "<p>"+data+"</p>" );
              });
        </script>
    </body>
</html>
