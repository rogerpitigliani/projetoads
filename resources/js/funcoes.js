export default {
    teste: async (data) => {
        return new Promise(async (resolve, reject) => {
            return resolve("OK");
        });
    },
    get_socket_url: async (data) => {

        return new Promise(async (resolve, reject) => {

            let response = await axios.get(`/servidor/cliente/${data.cliente_id}`);
            console.log("SRV", response);
            resolve(`http://${response.data.host_externo}:3001/pbx?clienteid=${data.cliente_id}`);
        });

    },
}
