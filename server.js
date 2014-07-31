var sockjs = require('sockjs'),
    http = require('http'),
    crypto = require('crypto'),

    redis = require('redis').createClient(6379, 'localhost');

    var randomInt = function(low, high) {
        return Math.floor(Math.random() * (high - low) + low);
    }

    var createConnectionName = function(){
        var tm = new Date().getTime().toString() + randomInt(0, 100000);
        return crypto.createHash('md5').update(tm).digest('hex');
    };


    var sockServer = sockjs.createServer();
    var connections = [];

    sockServer.on('connection', function(conn) {
        var connName = createConnectionName();
        connections.push({
            name: connName,
            //user: uid,
            connection: conn
        });

        conn.on('close', function() {
            for(var i in connections)
            {
                if(connections[i].connection == conn)
                {
                    connections.splice(i,1);
                    console.log('disconnected!');
                    break;
                }
            }
        });
    });

    var httpServer = http.createServer();

    sockServer.installHandlers(httpServer, {
        prefix:'/chat'
    });

    httpServer.listen(9999, '0.0.0.0');

    redis.subscribe('chat-message');

    redis.on('message', function(channel, rawMsgData) {
        for(var conn in connections)
        {
            connections[conn].connection.write(rawMsgData);
        }
    });


/*
 var rq = require('request');

 rq.post(resource, {
     form: {
         access_token: token,
         action: action
     }
 });


 */


