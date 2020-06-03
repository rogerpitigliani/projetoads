
// import * as BlipSdk from 'blip-sdk';
// import * as WebSocketTransport from 'lime-transport-websocket';

const BlipSdk = require("blip-sdk");
const WebSocketTransport = require("lime-transport-websocket");
const Lime = require('lime-js');

const config = require("config");
const db = require("./db");
var client = null;
var connected = false;


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

        if (message.type == 'text/plain') {

            try {
                let a = await db.get_atendimento_by_remoteid(message);

                if (a.status == 'new') {
                    var msgmenu = {
                        type: "text/plain",
                        content: `Bem Vindo ao ProjetoADS.
Este é um sistema de demonstração!
Por favor selecione o assunto desejado:

1 - Suporte
2 - Comercial
`,
                        to: message.from
                    };
                    var ret = await sendMessage(msgmenu);
                    await db.atendimento_menu(a);
                }

                if (a.status == 'menu') {

                    var msg = "Ops! Opção invalida, tente novamente!";
                    var equipe_id = null;

                    if (a.invalidas > 3) {
                        msg = `Ops!, Tentativas excedidas... :(
                        Encerrando Atendimento..
                        Tente novamente mais tarde...`;
                        await db.atendimento_encerra_invalidas(a);

                    } else if (message.content.trim() == '1' || message.content.trim().toLowerCase() == 'suporte') {
                        msg = `Ok, entendi!
                        Encaminhando para equipe de Suporte.
                        Aguarde um momento! :)`;
                        equipe_id = 1;
                    } else if (message.content.trim() == '2' || message.content.trim().toLowerCase() == 'comercial') {
                        msg = `Ok, entendi!
                        Encaminhando para equipe Comercial.
                        Aguarde um momento! :)`;
                        equipe_id = 2;
                    } else {
                        await db.atendimento_opcaoinvalida(a);
                    }

                    var respostamenu = { type: "text/plain", content: msg, to: message.from };
                    var ret = await sendMessage(respostamenu);

                }


            } catch (e) {
                console.log("Erro ", e.message);
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


exports.init = init;
exports.sendMessage = sendMessage;
