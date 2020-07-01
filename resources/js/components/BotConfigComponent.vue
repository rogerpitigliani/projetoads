<template>
  <b-container>
    <b-row>
      <b-col>
        <b-card :header="titulo" header-bg-variant="primary" header-text-variant="white">
          <div>
            <div>
              <b-row v-if="errors">
                <b-col>
                  <b-alert variant="warning" show>
                    <ul>
                      <li v-for="(value,index) in errors" :key="index">{{ value[0] }}</li>
                    </ul>
                  </b-alert>
                </b-col>
              </b-row>
              <b-overlay :show="loading" opacity="0.6" spinner-variant="primary" variant="primary">
                <b-form @submit.prevent="onSubmit" @reset="onReset">
                  <b-row>
                    <b-col>
                      <b-form-group
                        id="grp-msg-saudacao"
                        label="Mensagem Saudação:"
                        label-for="msg-saudacao"
                      >
                        <b-form-textarea
                          id="msg-saudacao"
                          placeholder="..."
                          rows="2"
                          v-model="form.msg_inicial"
                        ></b-form-textarea>
                      </b-form-group>
                    </b-col>
                  </b-row>

                  <b-row>
                    <b-col cols="8">
                      <b-form-group id="grp-msg-menu" label="Menu Opções:" label-for="msg-menu">
                        <b-form-textarea
                          v-model="form.msg_menu"
                          id="msg-menu"
                          placeholder="..."
                          rows="3"
                        ></b-form-textarea>
                      </b-form-group>
                    </b-col>
                    <b-col cols="4">
                      <b-form-group
                        v-model="form.msg_encerramento"
                        id="grp-timeout-encerra"
                        label="Timeout Resposta (Seg):"
                        label-for="timeout-encerra"
                      >
                        <b-form-input
                          type="number"
                          id="timeout-encerra"
                          placeholder="..."
                          value="120"
                        ></b-form-input>
                      </b-form-group>
                    </b-col>
                  </b-row>

                  <b-row>
                    <b-col>
                      <b-form-group
                        id="group-msg-encaminhamento"
                        label="Mensagem Encaminhamento para Equipe:"
                        label-for="msg-encaminhamento"
                      >
                        <b-form-textarea
                          v-model="form.msg_encaminhamento"
                          id="msg-encaminhamento"
                          placeholder="..."
                          rows="2"
                        ></b-form-textarea>
                      </b-form-group>
                    </b-col>
                    <b-col>
                      <b-form-group
                        id="group-msg-encerramento"
                        label="Mensagem Encerramento"
                        label-for="msg-encerramento"
                      >
                        <b-form-textarea
                          id="msg-encerramento"
                          placeholder="..."
                          rows="2"
                          v-model="form.msg_encerramento"
                        ></b-form-textarea>
                      </b-form-group>
                    </b-col>
                  </b-row>
                  <b-row>
                    <b-col>
                      <b-form-group
                        id="group-msg-invalid"
                        label="Mensagem Opção Inválida"
                        label-for="msg-invalid"
                      >
                        <b-form-textarea
                          id="msg-invalid"
                          placeholder="..."
                          rows="2"
                          v-model="form.msg_invalid"
                        ></b-form-textarea>
                      </b-form-group>
                    </b-col>
                    <b-col>
                      <b-form-group
                        id="group-msg-encerramento-timeout"
                        label="Mensagem Encerra - Timeout"
                        label-for="msg-encerramento-timeout"
                      >
                        <b-form-textarea
                          id="msg-encerramento-timeout"
                          placeholder="..."
                          rows="2"
                          v-model="form.msg_encerramento_timeout"
                        ></b-form-textarea>
                      </b-form-group>
                    </b-col>
                    <b-col>
                      <b-form-group
                        id="group-msg-encerramento-invalidas"
                        label="Mensagem Encerra - Tentativas Invalidas"
                        label-for="msg-encerramento-invalidas"
                      >
                        <b-form-textarea
                          id="msg-encerramento-invalidas"
                          placeholder="..."
                          rows="2"
                          v-model="form.msg_encerramento_tentativas"
                        ></b-form-textarea>
                      </b-form-group>
                    </b-col>
                  </b-row>
                  <!-- <b-row>
                    <b-col>{{ form }}</b-col>
                  </b-row>-->

                  <!-- <b-form-group id="input-group-4">
                  <b-form-checkbox-group v-model="form.enabled" id="checkboxes-4">
                    <b-form-checkbox value="me">Check me out</b-form-checkbox>
                    <b-form-checkbox value="that">Check that out</b-form-checkbox>
                  </b-form-checkbox-group>
                  </b-form-group>-->

                  <b-row>
                    <b-col>
                      <div class="float-right">
                        <b-button type="submit" variant="outline-primary">
                          <i class="fas fa-check"></i> Confirmar
                        </b-button>
                      </div>
                    </b-col>
                  </b-row>
                </b-form>
              </b-overlay>
            </div>
          </div>
        </b-card>
      </b-col>
    </b-row>
  </b-container>
</template>

<script>
export default {
  props: [
    "titulo",
    "url",
    "url_show",
    "url_update",
    "url_store",
    "sort_by",
    "usuario",
    "botconfig"
  ],
  data() {
    return {
      loading: false,
      currentPage: 1,
      perPage: 10,
      totalRows: 0,
      sortBy: this.sort_by || "id",
      sortDesc: false,
      fields: [
        { key: "id", label: "ID", sortable: true, class: "column-id" },
        { key: "classificacao", label: "Classificacao", sortable: true },
        { key: "descricao", label: "Descricao", sortable: true },
        { key: "tipo", label: "Tipo", sortable: true },
        { key: "enabled", label: "Habilitado", sortable: true },
        { key: "default_timeout", label: "Timeout", sortable: true },
        { key: "default_invalidas", label: "Invalidas", sortable: true },
        { key: "actions", label: "", sortable: false, class: "column-action" }
      ],
      dataArray: [],
      filter: null,
      editando: false,
      form: {
        id: null,
        descricao: null,
        classificacao: null,
        tipo: "Neutra",
        enabled: true,
        default_timeout: false
      },
      form_vazio: {},
      errors: null,
      tipos: [
        { text: "Neutra", value: "Neutra" },
        { text: "Positiva", value: "Positiva" },
        { text: "Negativa", value: "Negativa" }
      ],
      options_checkbox: [
        { text: "Sim", value: true },
        { text: "Não", value: false }
      ]
    };
  },
  methods: {
    onPaginate: function(page) {
      console.log("Paginate", page);
    },
    addnew: function() {
      var _this = this;
      _this.resetForm();
      _this.editando = true;
    },
    edit: async function(item, index, event) {
      var _this = this;
      _this.resetForm();
      _this.editando = true;
      _this.loading = true;
      var url = this.url_show.replace("ID", item.id);
      var res = await axios.get(url, {});
      _this.form = res.data;
      _this.loading = false;
    },
    onSubmit: async function(e) {
      var _this = this;

      try {
        var response = null;

        if (_this.form.id) {
          var url = _this.url_update.replace("ID", _this.form.id);
          console.log(url);
          var response = await axios.put(url, _this.form);
        } else {
          var url = _this.url_store;
          var response = await axios.post(url, _this.form);
        }

        if (response.data.status == "OK") {
          _this.editando = false;
          _this.loadData();
          _this.$msgSuccess(response.data.message);
          _this.loading = false;
        } else {
          _this.$msgError(response.data.message);
          _this.loading = false;
        }
      } catch (errors) {
        if (errors.response.status == 422) {
          _this.errors = errors.response.data;
          _this.$msgError("Erros encontrados");
          _this.loading = false;
          // console.log("ERRORS", _this.errors);
        }
      }
    },
    onReset: function() {
      console.log("Reset Form");
    },
    onCancel: async function() {
      // await this.$nextTick();
      await this.$nextTick();
      this.resetForm();
      this.editando = false;
      this.loading = false;
      // this.resetForm();
    },
    resetForm: function() {
      var _this = this;
      _this.errors = null;
      Object.keys(_this.form).forEach(function(key, index) {
        if (key == "enabled") {
          _this.form[key] = true;
        } else if (key == "tipo") {
          _this.form[key] = "Neutra";
        } else {
          _this.form[key] = null;
        }
      });

      // console.log("RESET", _this.form);
    },
    loadData: async function() {
      var _this = this;
      _this.loading = true;
      var res = await axios.get(_this.url + "?datatype=json", {});
      _this.dataArray = res.data;
      _this.loading = false;
    }
  },
  mounted() {
    var _this = this;
    _this.form_vazio = _this.form;
    _this.form = JSON.parse(_this.botconfig);
    // _this.loadData();
  },
  computed: {
    rows() {
      return this.items.length;
    },
    items() {
      return this.filter
        ? this.dataArray.filter(
            item =>
              item.classificacao.includes(this.filter) ||
              item.descricao.includes(this.filter) ||
              item.id == this.filter
          )
        : this.dataArray;
    }
  }
};
</script>
