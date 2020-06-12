const config = require("config");
const { Pool } = require('pg');

console.log("Connecting to DB");
const pool = new Pool(config.get("db"));

const limpar_atendimentos = (a) => {
    return new Promise(async (resolve, reject) => {

        console.log("### LIMPANDO TABELAS DE MENSAGENS ###");

        await pool.query(`truncate table atendimento restart identity cascade;`);
        await pool.query(`truncate table mensagem restart identity cascade;`);
        await pool.query(`truncate table contato restart identity cascade;`);
        return resolve(true);
    });
}




const atendimento_menu = (a) => {
    return new Promise(async (resolve, reject) => {
        let qry = `UPDATE atendimento SET status = 'menu' WHERE id = ${a.id};`;
        await pool.query(qry);
        return resolve(true);
    });
}

const associa_atendimento_contato = (a, c) => {
    return new Promise(async (resolve, reject) => {

        console.log("Associando Contato ao Atendimento...")
        if (c.id) {
            let qry = `UPDATE atendimento SET contato_id = ${c.id} WHERE id = ${a.id};`;
            await pool.query(qry);
        }
        return resolve(true);
    });
}
const atendimento_para_equipe = (a, rb) => {
    return new Promise(async (resolve, reject) => {

        console.log("Associando Equipe ao Atendimento...")
        if (rb.equipe_id) {
            let qry = `UPDATE atendimento
            SET
                status = 'fila',
                equipe_id = ${rb.equipe_id},
                datahora_fila = CURRENT_TIMESTAMP::timestamp(0),
                datahora_ultima_recebida = CURRENT_TIMESTAMP::timestamp(0)
            WHERE id = ${a.id};`;

            await pool.query(qry);
        }
        return resolve(true);
    });
}


const atendimento_fila = (a) => {
    return new Promise(async (resolve, reject) => {
        let qry = `UPDATE atendimento SET status = 'fila', datahora_fila = CURRENT_TIMESTAMP::timestamp(0) WHERE id = ${a.id};`;
        await pool.query(qry);
        return resolve(true);
    });
}


const atendimento_opcaoinvalida = (a) => {
    return new Promise(async (resolve, reject) => {
        let qry = `UPDATE atendimento SET invalidas = invalidas+1 WHERE id = ${a.id};`;
        await pool.query(qry);
        return resolve(true);
    });
}
const atendimento_encerra_invalidas = (a) => {
    return new Promise(async (resolve, reject) => {
        let qry = `UPDATE atendimento SET status = 'closed_invalid', finalizado = true, datahora_termino = CURRENT_TIMESTAMP::timestamp(0) WHERE id = ${a.id};`;
        await pool.query(qry);
        return resolve(true);
    });
}

const registra_contato = (c) => {
    return new Promise(async (resolve, reject) => {

        try {
            let res = await pool.query(`SELECT * FROM contato WHERE identity = '${c.resource.identity}' LIMIT 1`);
            if (res.rows.length > 0) {
                let sql = `UPDATE contato
                SET
                full_name = '${c.resource.fullName}',
                photo_uri = '${c.resource.photoUri}',
                updated_at = CURRENT_TIMESTAMP::timestamp(0)
                WHERE identity = '${c.resource.identity}'
                RETURNING * `;
                let retupd = await pool.query(sql);
                return resolve(retupd.rows[0]);
            } else {
                let sql = `INSERT INTO contato (identity,full_name,source,photo_uri,created_at,updated_at)
                VALUES
                (
                    '${c.resource.identity}',
                    '${c.resource.fullName}',
                    '${c.resource.source}',
                    '${c.resource.photoUri}',
                    CURRENT_TIMESTAMP::timestamp(0),
                    CURRENT_TIMESTAMP::timestamp(0)
                ) RETURNING * `;
                let retadd = await pool.query(sql);
                return resolve(retadd.rows[0]);
            }
        } catch (e) {

            console.log("Error registra_contato", e.message);
            return resolve(null);

        }
    });
}



const get_atendimento_by_remoteid = (message) => {

    return new Promise(async (resolve, reject) => {
        try {

            // console.log(message);

            var canal = "chat";
            if (message.direcao == 'in') {
                remoteid = message.from;
            } else {
                remoteid = message.to;
            }

            let qry = `SELECT a.*
            FROM atendimento as a
            WHERE ( a.remote_id = '${message.from}' ) AND ( a.finalizado = false )
            ORDER BY a.id DESC
            LIMIT 1;`;

            var ret = await pool.query(qry);
            if (ret.rows.length == 0) {

                var data = new Date();
                // let proto = ("0" + data.getDate()).substr(-2) + ("0" + (data.getMonth() + 1)).substr(-2) + data.getFullYear() + Math.floor(1000 + Math.random() * 9000);
                let proto = "" + data.getFullYear() + ("0" + (data.getMonth() + 1)) + "" + ("0" + data.getDate()) + Math.floor(1000 + Math.random() * 9000);

                let qryadd = `INSERT INTO atendimento (message_id, canal, status, remote_id, protocolo, datahora_inicio, datahora_ultima_recebida)
                    VALUES ('${message.id}','${canal}','new','${remoteid}','${proto}',CURRENT_TIMESTAMP::timestamp(0),CURRENT_TIMESTAMP::timestamp(0))
                    RETURNING *;`;
                ret = await pool.query(qryadd);
            } else {
                await pool.query(`UPDATE atendimento SET datahora_ultima_recebida = CURRENT_TIMESTAMP::timestamp(0) WHERE id = ${ret.rows[0].id}`);
            }
            resolve(ret.rows[0]);
        } catch (e) {

            reject(e);
        }
    });

};

const get_bot_configs = () => {

    return new Promise(async (resolve, reject) => {
        try {
            let qry = `select * from bot_config bc order by id desc limit 1;`;
            var ret = await pool.query(qry);
            if (ret.rows.length == 0) {
                console.log("#### CONFIGS DE BOT NAO ENCONTRADAS ####");
                return resolve(null);
            }
            return resolve(ret.rows[0]);
        } catch (e) {
            return reject(e);
        }
    });

};

const get_resposta_bot = (message) => {

    return new Promise(async (resolve, reject) => {
        try {
            let resposta = message.content.trim().toLowerCase();
            let qry = `select * from bot_resposta br where resposta = '${resposta}' limit 1;;`;
            var ret = await pool.query(qry);
            if (ret.rows.length == 0) {
                return resolve(null);
            }
            return resolve(ret.rows[0]);
        } catch (e) {
            console.log("Error on get_resposta_bot", e.message)
            return reject(e);
        }
    });

};
const get_atentimentos_expirados = (tempo) => {

    return new Promise(async (resolve, reject) => {
        try {

            let qry = `select * from atendimento a where status = 'menu' AND EXTRACT(EPOCH from (current_timestamp - datahora_ultima_recebida)) > ${tempo};`;
            var ret = await pool.query(qry);
            return resolve(ret.rows);
        } catch (e) {
            console.log("Error get_atentimentos_expirados", e.message);
            return reject(e);
        }
    });

};
const encerrar_atendimento_timeout = (at) => {

    return new Promise(async (resolve, reject) => {
        try {


            let qry = `UPDATE atendimento SET status = 'timeout', datahora_termino = CURRENT_TIMESTAMP::timestamp(0), finalizado = true WHERE id = ${at.id};`;
            var ret = await pool.query(qry);
            return resolve(true);

        } catch (e) {
            console.log("Error get_atentimentos_expirados", e.message);
            return reject(e);
        }
    });

};

const add_message_in = (m) => {

    return new Promise(async (resolve, reject) => {
        try {

            console.log("Add Message", m);


            let qry = `INSERT INTO mensagem (
                    atendimento_id,
                    direcao,
                    type,
                    content,
                    created_at,
                    updated_at)
            VALUES
            (       ${m.atendimento_id},
                    '${m.direcao}',
                    '${m.type}',
                    '${m.content}',
                    CURRENT_TIMESTAMP::timestamp(0),
                    CURRENT_TIMESTAMP::timestamp(0)
            )`;

            var ret = await pool.query(qry);
            console.log(ret);
            return resolve(true);

        } catch (e) {
            console.log("Error get_atentimentos_expirados", e.message);
            return reject(e);
        }
    });

};

const add_message_out = (m) => {

    return new Promise(async (resolve, reject) => {
        try {

            console.log("Add Message", m);


            let qry = `INSERT INTO mensagem (
                    atendimento_id,
                    direcao,
                    type,
                    content,
                    created_at,
                    updated_at)
            VALUES
            (       ${m.atendimento_id},
                    'out',
                    '${m.type}',
                    '${m.content}',
                    CURRENT_TIMESTAMP::timestamp(0),
                    CURRENT_TIMESTAMP::timestamp(0)
            )`;

            var ret = await pool.query(qry);
            console.log(ret);
            return resolve(true);

        } catch (e) {
            console.log("Error get_atentimentos_expirados", e.message);
            return reject(e);
        }
    });

};




exports.get_atendimento_by_remoteid = get_atendimento_by_remoteid;
exports.atendimento_menu = atendimento_menu;
exports.atendimento_fila = atendimento_fila;
exports.atendimento_opcaoinvalida = atendimento_opcaoinvalida;
exports.atendimento_encerra_invalidas = atendimento_encerra_invalidas;
exports.registra_contato = registra_contato;
exports.limpar_atendimentos = limpar_atendimentos;
exports.associa_atendimento_contato = associa_atendimento_contato;
exports.atendimento_para_equipe = atendimento_para_equipe;
exports.get_bot_configs = get_bot_configs;
exports.get_atentimentos_expirados = get_atentimentos_expirados;
exports.encerrar_atendimento_timeout = encerrar_atendimento_timeout;
exports.get_resposta_bot = get_resposta_bot;
exports.add_message_in = add_message_in;
exports.add_message_out = add_message_out;
