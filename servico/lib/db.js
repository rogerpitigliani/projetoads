const config = require("config");
const { Pool } = require('pg');

console.log("Connecting to DB");
const pool = new Pool(config.get("db"));

const atendimento_menu = (a) => {
    return new Promise(async (resolve, reject) => {
        let qry = `UPDATE atendimento SET status = 'menu' WHERE id = ${a.id};`;
        await pool.query(qry);
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
                return resolve(retupd.rows[0]);
            }
        } catch (e) {

        }
    });
}



const get_atendimento_by_remoteid = (message) => {

    return new Promise(async (resolve, reject) => {
        try {

            console.log(message);

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

                let qryadd = `INSERT INTO atendimento (canal, status, remote_id)
                    VALUES ('${canal}','new','${remoteid}')
                    RETURNING *;`;
                ret = await pool.query(qryadd);
            }
            resolve(ret.rows[0]);
        } catch (e) {

            reject(e);
        }
    });

};


exports.get_atendimento_by_remoteid = get_atendimento_by_remoteid;
exports.atendimento_menu = atendimento_menu;
exports.atendimento_fila = atendimento_fila;
exports.atendimento_opcaoinvalida = atendimento_opcaoinvalida;
exports.atendimento_encerra_invalidas = atendimento_encerra_invalidas;
exports.registra_contato = registra_contato;
