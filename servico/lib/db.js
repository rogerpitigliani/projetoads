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

        let classificacao_id = 'null';
        let res = await pool.query("select id from classificacao c  where default_invalidas = true limit 1;");
        if (res.rows.length > 0) classificacao_id = res.rows[0].id;

        let qry = `UPDATE atendimento
        SET
            status = 'closed_invalid',
            finalizado = true,
            datahora_termino = CURRENT_TIMESTAMP::timestamp(0),
            classificacao_id = ${classificacao_id}
            WHERE id = ${a.id};`;
        await pool.query(qry);
        return resolve(true);
    });
}

const registra_contato = (c) => {
    return new Promise(async (resolve, reject) => {

        console.log("REGISTRANDO CONTATO", c);

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

            console.log("get_atendimento_by_remoteid", message);

            var canal = "chat";

            if (message.from.indexOf('messenger.gw') > -1) {
                canal = "facebook";
            } else if (message.from.indexOf('telegram.gw') > -1) {
                canal = "telegram";
            } else if (message.from.indexOf('whatsapp.gw') > -1) {
                canal = "whatsapp";
            }

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
const get_status_filas = () => {

    return new Promise(async (resolve, reject) => {
        try {

            let qry = `select
                e.id as equipe_id,
                count(a.id) as qtde
            from equipe e
            left join atendimento a on ( a.equipe_id = e.id and a.status = 'fila' )
            group by e.id;`;

            var ret = await pool.query(qry);
            return resolve(ret.rows);

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
const get_atendimento_atual_usuario = (usuario_id) => {

    return new Promise(async (resolve, reject) => {
        try {
            let qry = `select * from atendimento where usuario_id = '${usuario_id}' and status = 'chat' order by id desc limit 1`;
            var ret = await pool.query(qry);
            if (ret.rows.length == 0) {
                return resolve(null);
            }
            var a = ret.rows[0];
            return resolve(a);
        } catch (e) {
            console.log("Error on get_atendimento_atual_usuario", e.message)
            return reject(e);
        }
    });

};
const get_atendimentos_expirados = (tempo) => {

    return new Promise(async (resolve, reject) => {
        try {

            let qry = `select * from atendimento a where status = 'menu' AND EXTRACT(EPOCH from (current_timestamp - datahora_ultima_recebida)) > ${tempo};`;
            var ret = await pool.query(qry);
            return resolve(ret.rows);
        } catch (e) {
            console.log("Error get_atendimentos_expirados", e.message);
            return reject(e);
        }
    });

};
const get_lista_classificacoes = (tempo) => {

    return new Promise(async (resolve, reject) => {
        try {

            let qry = `select id as value, classificacao  as text
                from classificacao c
                where ( c.enabled = true ) and ( c.default_timeout = false ) and ( c.default_invalidas = false )
                order by classificacao asc;`;
            var ret = await pool.query(qry);
            return resolve(ret.rows);
        } catch (e) {
            console.log("Error get_lista_classificacoes", e.message);
            return reject(e);
        }
    });

};
const encerrar_atendimento_timeout = (at) => {

    return new Promise(async (resolve, reject) => {
        try {

            let res = await pool.query("select id from classificacao c  where default_timeout = true limit 1;");
            let classificacao_id = 'null';
            if (res.rows.length > 0) classificacao_id = res.rows[0].id;
            let qry = `UPDATE atendimento
                SET status = 'timeout',
                    datahora_termino = CURRENT_TIMESTAMP::timestamp(0),
                    finalizado = true,
                    classificacao_id = ${classificacao_id}
                WHERE id = ${at.id};`;

            var ret = await pool.query(qry);
            return resolve(true);

        } catch (e) {
            console.log("Error encerrar_atendimento_timeout", e.message);
            return reject(e);
        }
    });

};

const encerrar_atendimento = (at) => {

    return new Promise(async (resolve, reject) => {
        try {


            let qry = `UPDATE atendimento
                SET status = 'end',
                    datahora_termino = CURRENT_TIMESTAMP::timestamp(0),
                    finalizado = true,
                    classificacao_id = ${at.classificacao_id},
                    observacoes = '${at.observacoes}'
                WHERE id = ${at.id};`;
            var ret = await pool.query(qry);
            if (ret.rowCount == 1) {
                return resolve(true);
            } else {
                return resolve(false);
            }

        } catch (e) {
            console.log("Error encerrar_atendimento", e.message);
            return reject(e);
        }
    });

};

const get_proximo_atendimento = (data) => {

    return new Promise(async (resolve, reject) => {

        try {

            let qry = `select a.*
from atendimento a
join equipe e on a.equipe_id  = e.id
join equipe_usuario eu on eu.equipe_id  = e.id and eu.usuario_id = ${data.usuario_id}
where a.status = 'fila'
order by a.datahora_fila asc
limit 1
`;

            var ret = await pool.query(qry);
            if (ret.rows.length == 0) return resolve(null);

            var a = ret.rows[0];
            ret = await pool.query(`UPDATE atendimento
            SET
                status = 'chat',
                datahora_atende = CURRENT_TIMESTAMP::timestamp(0),
                usuario_id = ${data.usuario_id}
            WHERE id = ${a.id}
            RETURNING * `);
            var a = ret.rows[0];
            return resolve(a);

        } catch (e) {
            console.log("Error get_proximo_atendimento", e.message);
            return reject(e);
        }
    });

};
const get_mensagens_atendimento = (a) => {

    return new Promise(async (resolve, reject) => {
        try {
            let qry = `select * from mensagem where atendimento_id = ${a.id} order by created_at ASC`;
            var ret = await pool.query(qry);
            return resolve(ret.rows);
        } catch (e) {
            console.log("Error get_mensagens_atendimento", e.message);
            return reject(e);
        }
    });

};
const get_qtde_atendimentos_usuario = (data) => {

    return new Promise(async (resolve, reject) => {
        try {
            let qry = `select count(1) as qtde
            from atendimento
            where usuario_id = ${data.usuario_id}
            and finalizado = true
            and datahora_inicio >= (current_date || ' 00:00:00')::timestamp(0);
            `;
            var ret = await pool.query(qry);
            return resolve(ret.rows[0].qtde);
        } catch (e) {
            console.log("Error get_mensagens_atendimento", e.message);
            return reject(e);
        }
    });

};

const get_qtde_fila_usuario = (data) => {

    return new Promise(async (resolve, reject) => {
        try {

            let qry = `select count(1) as qtde
            from atendimento a
            join equipe_usuario eu on ( a.equipe_id  = eu.equipe_id )
            where ( a.status = 'fila' ) and ( eu.usuario_id = ${data.usuario_id} )
            `;
            var ret = await pool.query(qry);
            return resolve(ret.rows[0].qtde);
        } catch (e) {
            console.log("Error get_mensagens_atendimento", e.message);
            return reject(e);
        }
    });

};

const get_contato_atendimento = (a) => {

    return new Promise(async (resolve, reject) => {
        try {
            let qry = `select * from contato where id = ${a.contato_id} order by created_at DESC LIMIT 1`;
            var ret = await pool.query(qry);
            if (ret.rows.length == 0) return resolve(null);
            return resolve(ret.rows[0]);
        } catch (e) {
            console.log("Error get_mensagens_atendimento", e.message);
            return reject(e);
        }
    });

};

const add_message_in = (m) => {

    return new Promise(async (resolve, reject) => {
        try {

            // console.log("Add Message", m);


            let qry = `INSERT INTO mensagem (
                    atendimento_id,
                    direcao,
                    type,
                    content,
                    created_at,
                    updated_at)
            VALUES
            (       ${m.atendimento_id},
                    'in',
                    '${m.type}',
                    '${m.content}',
                    CURRENT_TIMESTAMP::timestamp(0),
                    CURRENT_TIMESTAMP::timestamp(0)
            )
            RETURNING * `;

            var ret = await pool.query(qry);
            // console.log(ret);
            if (ret.rows.length == 0) return resolve(false);
            return resolve(ret.rows[0]);

        } catch (e) {
            console.log("Error get_atendimentos_expirados", e.message);
            return reject(e);
        }
    });

};

const add_message_out = (m) => {

    return new Promise(async (resolve, reject) => {
        try {

            // console.log("Add Message", m);


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
            )
            RETURNING * `;

            var ret = await pool.query(qry);
            // console.log(ret);
            if (ret.rows.length == 0) return resolve(false);
            return resolve(ret.rows[0]);

        } catch (e) {
            console.log("Error get_atendimentos_expirados", e.message);
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
exports.get_atendimentos_expirados = get_atendimentos_expirados;
exports.encerrar_atendimento_timeout = encerrar_atendimento_timeout;
exports.get_resposta_bot = get_resposta_bot;
exports.add_message_in = add_message_in;
exports.add_message_out = add_message_out;
exports.get_status_filas = get_status_filas;
exports.get_proximo_atendimento = get_proximo_atendimento;
exports.get_mensagens_atendimento = get_mensagens_atendimento;
exports.get_contato_atendimento = get_contato_atendimento;
exports.get_qtde_atendimentos_usuario = get_qtde_atendimentos_usuario;
exports.get_qtde_fila_usuario = get_qtde_fila_usuario;
exports.get_atendimento_atual_usuario = get_atendimento_atual_usuario;
exports.get_lista_classificacoes = get_lista_classificacoes;
exports.encerrar_atendimento = encerrar_atendimento;
