const app = require('express')();
const http = require('http').createServer(app);
const config = require('config');
const db = require('./db');
const chat = require('./blipai');
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

    // var rooms = socket.adapter.rooms;
    // Object.keys(rooms).forEach(room => {
    //     if (room.indexOf('chat-room') > -1) {
    //         chat_rooms.push(room);
    //         chat_rooms = [...new Set(chat_rooms)];
    //     }
    // });

    socket.on("get_proximo_atendimento", async (data, fn) => {
        var a = await db.get_proximo_atendimento(data);
        var mensagens = [];
        var contato = null;

        if (a) {
            mensagens = await db.get_mensagens_atendimento(a);
            contato = await db.get_contato_atendimento(a);
        }

        if (!contato) {
            contato = {
                photo_uri: '/img/avatar_unknow.png',
                name: 'Cliente Chat'
            };
        }

        fn({
            atendimento: a,
            mensagens: mensagens,
            contato: contato
        });
    });

    socket.on("atualizar_status_fila", async (data) => {

        console.log("WEB SOLICITOU ATAUALIZAR FILAS");
        await atualiza_fila_status();

    });

    socket.on("get_qtde_atendimentos_hoje", async (data, fn) => {

        console.log("WEB SOLICITOU QTDE ATENDIMENTOS");
        var qtde = await db.get_qtde_atendimentos_usuario(data);
        fn({ qtde: qtde });

    });
    socket.on("encerrar_atendimento", async (a, fn) => {

        console.log("WEB SOLICITOU ENCERRAR ATENDIMENTO", a);
        var res = await db.encerrar_atendimento(a);
        if (res) {
            fn({ status: 'OK', message: 'Atendimento encerrado' });
        } else {
            fn({ status: 'ERR', message: 'Ops! Algo deu errado. Atendimento nao foi finalizado.' });
        }

    });

    socket.on("get_qtde_fila_usuario", async (data, fn) => {

        console.log("WEB SOLICITOU QTDE FILA", data);
        var qtde = await db.get_qtde_fila_usuario(data);
        fn({ qtde: qtde });

    });
    socket.on("get_lista_classificacoes", async (data, fn) => {

        console.log("WEB SOLICITOU LISTA DE CLASSIFICACOES", data);
        var res = await db.get_lista_classificacoes(data);
        fn({ list: res });

    });
    socket.on("atendimento_usuario", async (data, fn) => {

        var a = await db.get_atendimento_atual_usuario(data.usuario_id);
        var contato = null;
        var mensagens = null;

        if (a) {
            mensagens = await db.get_mensagens_atendimento(a);
            contato = await db.get_contato_atendimento(a);
        }

        if (!contato) {
            contato = {
                photo_uri: '/img/avatar_unknow.png',
                name: 'Cliente Chat'
            };
        }

        fn({
            atendimento: a,
            mensagens: mensagens,
            contato: contato
        });

    });

    socket.on("send_message", async (msg, fn) => {

        console.log("ENVIAR MSG", msg);
        await chat.sendMessage(msg);
        fn({ status: "OK", msg: msg });

    });


});

// setInterval(function () {
//     var clients = io.of('/chat').clients();
//     chat_rooms = [];
//     Object.keys(clients.connected).forEach(key => {
//         var rooms = clients.connected[key].rooms;
//         Object.keys(rooms).forEach(room => {
//             if (room.indexOf('chat-room') > -1) {
//                 chat_rooms.push(room);
//                 chat_rooms = [...new Set(chat_rooms)];
//             }
//         });
//     });

//     for (var i = 0; i < chat_rooms.length; i++) {
//         io_chat.to(chat_rooms[i]).emit('atualiza_chat', { dados: 'Atualizar Chat' });
//     }

// }, 5000);


const get_rooms = () => {
    return new Promise(async (resolve, reject) => {
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
        return resolve(chat_rooms);
    });
}

const atualiza_fila_status = () => {
    return new Promise(async (resolve, reject) => {
        // let filas = await db.get_status_filas();
        var rooms = await get_rooms();
        // console.log("status filas", filas, rooms);
        for (var i = 0; i < rooms.length; i++) {
            //  console.log("Emitindo atualiza_status_fila", rooms[i]);
            io_chat.to(rooms[i]).emit('atualiza_status_fila', {});
        }
        return resolve(true);
    });
}

const nova_mensagem_recebida = (data) => {
    return new Promise(async (resolve, reject) => {
        io_chat.to(`chat-room-${data.usuario_id}`).emit('nova_mensagem_recebida', data.msg);
        return resolve(true);
    });
}

exports.io_chat = io_chat;
exports.atualiza_fila_status = atualiza_fila_status;
exports.nova_mensagem_recebida = nova_mensagem_recebida;
