const config = require("config");
const blipai = require("./lib/blipai");
const websocket = require("./lib/websocket");
const db = require("./lib/db");



(async function () {
    // await db.limpar_atendimentos();
})();

console.log(`Rodando!`);
blipai.init();

