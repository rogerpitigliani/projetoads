
// import * as BlipSdk from 'blip-sdk';
// import * as WebSocketTransport from 'lime-transport-websocket';

const BlipSdk = require("blip-sdk");
const WebSocketTransport = require("lime-transport-websocket");
const Lime = require('lime-js');

const config = require("config");
const db = require("./db");
var client = null;
var connected = false;

const start_check_timeout = () => {

    setInterval(async function () {

        if (!connected) return;

        try {

            var bc = await db.get_bot_configs();
            if (!bc) {
                console.log("BotConfig nao carregada");
                return;
            }
            let ats = await db.get_atentimentos_expirados(bc.timeout_encerra);
            for (var i = 0; i < ats.length; i++) {
                let at = ats[i];
                console.log("Finalizando Atendimento");
                var msg = { type: "text/plain", content: bc.msg_encerramento_timeout, to: at.remote_id };
                await db.encerrar_atendimento_timeout(at);
                await sendMessage(msg);
            }

        } catch (e) {
            console.log("Error on start_check_timeout ", e.message);
        }

    }, 5000);

}

const init = () => {

    client = new BlipSdk.ClientBuilder()
        .withIdentifier(config.get("blip.identifier"))
        .withAccessKey(config.get("blip.key"))
        .withTransportFactory(() => new WebSocketTransport())
        .build();

    client.connect() // This method return a 'promise'.
        .then(function (session) {
            // Connection success. Now is possible send and receive envelopes from server. */
            console.log('BlipClient Connected..');
            console.log("listening", client.listening);
            connected = true;
            //  console.log(client);
            // console.log("State", client.);

            start_check_timeout();

        })
        .catch(function (err) {
            console.log("Erro Conectar BlipAi", err.message);
            connected = false;
        });


    // client.closing().then(function (session) {
    //     console.log('BlipClient Disconnecting..');
    //     connected = false;
    // });


    client.addMessageReceiver(true, async function (message) {
        console.log("Mensagem Recebida", message);

        message.direcao = 'in';

        // Existe Atendimento em Aberto pra esse id?
        let a = await db.get_atendimento_by_remoteid(message);
        console.log(`Atendimento para ${message.from}`, a);

        var bc = await db.get_bot_configs();

        // O Contato existe?
        let ci = await contactInfo(message.from);
        console.log(`Contato para ${message.from}`, a);

        // Associa Contato ao Atendimento
        if (ci) {
            await db.associa_atendimento_contato(a, ci);
        }

        console.log("Status do Atendimento:", a.status);

        if (a.status == 'new') {

            await sendMessage({ type: "text/plain", content: bc.msg_inicial + '\n' + bc.msg_menu, to: message.from });
            await db.atendimento_menu(a);

        } else if (a.status == 'menu') {

            var rb = null;
            if (message.type == 'text/plain') {
                rb = await db.get_resposta_bot(message);
            }

            if (rb) {
                console.log("Resposta Encontrada", rb);

                // PROCESSAR RESPOSTA VALIDA

            } else {

                // Não encontrada! Registra Invalida - Informa
                await db.atendimento_opcaoinvalida(a);

                if (a.invalidas == 2) {
                    // Encerra, ja é a terceira - Informa (Excedeu 3 tentativas de opcao invalida)
                    await db.atendimento_encerra_invalidas(a);
                    await sendMessage({ type: "text/plain", content: bc.msg_encerramento_tentativas, to: message.from });
                } else {
                    await sendMessage({ type: "text/plain", content: bc.msg_invalid, to: message.from });
                }
            }


        }





    });

    client.addNotificationReceiver("received", function (notification) {
        console.log("Notificacao Recebida", notification);
    });

    setInterval(async function () {

        // try {
        //     var data = await client.sendCommand({
        //         id: Lime.Guid(),
        //         method: Lime.CommandMethod.GET,
        //         uri: '/contacts?$skip=0&$take=3'
        //     });

        //     console.log("contacts", data.resource);

        // } catch (e) {

        //     console.log("Erro", e.message);

        // }

        // console.log(".");
        if (!client.listening) {
            console.log("Opa! Parou de escutar!");
        }
    }, 5000);


}


const sendMessage = (msg) => {
    return new Promise((resolve, reject) => {
        try {

            if (!client) {
                var ret = {
                    status: "ERR",
                    message: "Servidor nao conectado! Tente novamente!",
                    error: null,
                }
                return resolve(ret);
            }



            client.sendMessage(msg);

            console.log("Enviando MSG", msg);

            var ret = {
                status: "OK",
                message: "Mensgem enviada",
                error: null,
            }



            return resolve(ret);

        } catch (e) {
            let ret = {
                status: "ERR",
                message: "Erro ao tentar enviar mensagem",
                error: e.message
            };

            console.log(ret)
            return resolve(ret);
        }
    });
}


const contactInfo = (contact_id) => {

    return new Promise(async (resolve, reject) => {


        try {

            var remote = contact_id.split('@');
            var command_uri = 'lime://' + remote[1] + '/accounts/' + remote[0];
            var command_to = 'postmaster@' + remote[1];

            let command = {
                id: Lime.Guid(),
                method: "get",
                uri: command_uri,
                to: command_to,
            }

            console.log("Consultando Dados Contato", contact_id);
            var response = await client.sendCommand(command);
            if (response.resource) {
                var contato = await db.registra_contato(response);
                return resolve(contato);
            } else {
                return resolve(null);
            }


        } catch (error) {
            console.log("ERROR contact_info", error);
            return reject(error);
        }


    })

}


exports.init = init;
exports.sendMessage = sendMessage;
exports.contactInfo = contactInfo;
