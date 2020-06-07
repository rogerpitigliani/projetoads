import io from 'socket.io-client';
import funcoes from './funcoes.js';
export default {
    data() {
        return {
            sio: null,
            chat_atual: null
        }
    },
    created: function () {

    },
    methods: {
        load_events: function () {
            var _this = this;
            _this.sio.on('atualiza_chat', (data) => {
                console.log("Server Data", data);
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
