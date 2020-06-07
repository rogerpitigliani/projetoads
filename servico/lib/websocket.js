const app = require('express')();
const http = require('http').createServer(app);
const config = require('config');
const db = require('./db');
const io = require('socket.io')(http);
var chat_rooms = [];

app.get('/', function (req, res) {
    return res.send('<h3 style="width: 100%; text-align: center;margin-top: 100px; text-transform: uppercase; ">ProjetoADS - Web Socket Connection</h3>');
});

var socketio_port = (config.has("socket.port")) ? config.get("socket.port") : 3001; // Porta 3001 default

http.listen(socketio_port, function () {
    console.log(`SOCKET.IO - Listenning on *:${socketio_port}`);
});

const io_chat = io.of("/chat");
io_chat.on('connection', function (socket) {
    socket.on('disconnect', function () {
        console.log(`socket::chat - UsuarioID ${socket.handshake.query.usuarioid} Desconectado`);




    });

    if (!socket.handshake.query.usuarioid) {
        console.log("socket::chat - UsuarioID nao informado na conexao");
        socket.disconnect;
        return;
    }
    console.log(`socket::chat - join to chat-room-${socket.handshake.query.usuarioid}`);
    socket.join(`chat-room-${socket.handshake.query.usuarioid}`);
    // conectados.push(socket.handshake.query.usuarioid);
    // conectados = [...new Set(conectados)];


    var rooms = socket.adapter.rooms;
    Object.keys(rooms).forEach(room => {
        if (room.indexOf('chat-room') > -1) {
            chat_rooms.push(room);
            chat_rooms = [...new Set(chat_rooms)];
        }
    });

});

setInterval(function () {
    var clients = io.of('/chat').clients();
    chat_rooms = [];
    Object.keys(clients.connected).forEach(key => {
        var rooms = clients.connected[key].rooms;
        Object.keys(rooms).forEach(room => {
            if (room.indexOf('chat-room') > -1) {
                chat_rooms.push(room);
                chat_rooms = [...new Set(chat_rooms)];
            }
        });
    });
    console.log(chat_rooms);

    for (var i = 0; i < chat_rooms.length; i++) {
        io_chat.to(chat_rooms[i]).emit('atualiza_chat', { dados: 'Atualizar Chat' });
    }

}, 5000);

exports.io_chat = io_chat;
