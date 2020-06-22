import io from 'socket.io-client';
import funcoes from './funcoes.js';
export default {
    data() {
        return {
            sio: null,
            chat_atual: null,
            fila_status: [],
        }
    },
    created: function () {

    },
    methods: {
        load_events: function () {
            var _this = this;

            _this.sio.on('atualiza_chat', (data) => {
                console.log("atualiza_chat", data);
            });

            _this.sio.on('atualiza_status_fila', (data) => {

                _this.sio.emit(
                    "get_qtde_fila_usuario",
                    { usuario_id: _this.usuario_id },
                    data => {
                        _this.qtde_fila = data.qtde;
                    }
                );

            });

            _this.sio.on('nova_mensagem_recebida', (msg) => {
                if (this.mensagens) {
                    this.mensagens.push(msg);
                }

            });

        },
        connect_io: function (socket_host, socket_port, socket_resource, usuario_id) {

            var _this = this;
            return new Promise(async (resolve, reject) => {
                var urlsocket = `http://${socket_host}:${socket_port}/${socket_resource}?usuarioid=${usuario_id}`;
                console.log("URL SOCKET: ", urlsocket)
                _this.sio = io.connect(urlsocket);
                _this.sio.on('connect', function () {
                    if (_this.sio.connected) {
                        _this.load_events();
                        console.log("Socket.IO Connected. OK");
                    }
                    resolve(true);
                });
                _this.sio.on('connect_error', function (error) {
                    console.log("Error connect Socket Chat: ", error);
                    reject(false);
                });
                _this.sio.on('error', function (error) {
                    console.log("Error on connect Socket.IO! ", error);
                    reject(false);
                });



            });

        },



    }
}
