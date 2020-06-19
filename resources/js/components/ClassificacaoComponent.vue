<template>
  <b-container>
    <b-row>
      <b-col>
        <b-card :header="titulo" header-bg-variant="primary" header-text-variant="white">
          <div v-show="!editando">
            <b-row>
              <b-col></b-col>
              <b-col>
                <b-form-group
                  label-cols-sm="3"
                  label-align-sm="right"
                  label-size="sm"
                  label-for="filterInput"
                >
                  <b-input-group size="sm">
                    <b-form-input
                      v-model="filter"
                      type="search"
                      id="filterInput"
                      placeholder="Pesquisar"
                    ></b-form-input>
                    <b-input-group-append>
                      <b-button
                        pill
                        variant="outline-warning"
                        class="success"
                        :disabled="!filter"
                        @click="filter = ''"
                      >
                        <i class="fas fa-eraser"></i> Limpar
                      </b-button>
                      <b-button pill variant="outline-primary" class="success" @click="addnew">
                        <i class="fas fa-plus-circle"></i> Novo
                      </b-button>
                    </b-input-group-append>
                  </b-input-group>
                </b-form-group>
              </b-col>
            </b-row>
            <b-table
              id="tblmain"
              striped
              hover
              responsive="sm"
              :borderless="true"
              :busy="loading"
              :items="items"
              :fields="fields"
              :per-page="perPage"
              :current-page="currentPage"
              :sort-by.sync="sortBy"
              :sort-desc.sync="sortDesc"
            >
              <template v-slot:table-busy>
                <div class="text-center text-primary my-2">
                  <b-spinner class="align-middle"></b-spinner>
                  <strong>Carregando...</strong>
                </div>
              </template>

              <template v-slot:cell(enabled)="row">
                <div>
                  <b-form-checkbox
                    v-model="row.item.enabled"
                    name="check-button"
                    switch
                    disabled
                    size="lg"
                  ></b-form-checkbox>
                </div>
              </template>
              <template v-slot:cell(default_timeout)="row">
                <div>
                  <b-form-checkbox
                    v-model="row.item.default_timeout"
                    name="check-button"
                    switch
                    disabled
                    size="lg"
                  ></b-form-checkbox>
                </div>
              </template>

              <template v-slot:cell(actions)="row">
                <b-button
                  size="sm"
                  @click="edit(row.item, row.index, $event.target)"
                  title="Editar"
                >
                  <i class="fas fa-edit"></i>
                </b-button>
              </template>
            </b-table>
            <b-pagination
              v-model="currentPage"
              :total-rows="rows"
              :per-page="perPage"
              aria-controls="tblmain"
              align="right"
              size="sm"
              @change="onPaginate"
            ></b-pagination>
          </div>
          <div v-show="editando">
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
                        id="group-classificacao"
                        label="Classificação:"
                        label-for="classificacao"
                      >
                        <b-form-input
                          id="classificacao"
                          v-model="form.classificacao"
                          required
                          placeholder="Classificação"
                        ></b-form-input>
                      </b-form-group>
                    </b-col>
                    <b-col>
                      <b-form-group id="group-descricao" label="Descrição:" label-for="descricao">
                        <b-form-input
                          id="descricao"
                          v-model="form.descricao"
                          required
                          placeholder="Descrição"
                        ></b-form-input>
                      </b-form-group>
                    </b-col>
                  </b-row>

                  <b-row>
                    <b-col cols="6">
                      <b-form-group id="group-tipo" label="Tipo de Classificação:" label-for="tipo">
                        <b-form-select v-model="form.tipo" :options="tipos" class="mt-3"></b-form-select>
                      </b-form-group>
                    </b-col>

                    <b-col>
                      <b-form-group
                        id="group-default_timeout"
                        label="Padrão Timeout"
                        label-for="default_timeout"
                        title="Classificação aplicada quando o atendimento encerrado automaticamente por timeout"
                      >
                        <b-form-checkbox
                          id="default_timeout"
                          v-model="form.default_timeout"
                          name="default_timeout"
                          switch
                          size="lg"
                          :value="true"
                          :unchecked-value="false"
                        >{{ (form.default_timeout)?'Sim':'Não' }}</b-form-checkbox>
                      </b-form-group>
                    </b-col>
                    <b-col>
                      <b-form-group
                        id="group-enabled"
                        label="Habilitado"
                        label-for="enabled"
                        title="Habilitado"
                      >
                        <b-form-checkbox
                          id="enabled"
                          v-model="form.enabled"
                          name="enabled"
                          switch
                          size="lg"
                          :value="true"
                          :unchecked-value="false"
                        >{{ (form.enabled)?'Sim':'Não' }}</b-form-checkbox>
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
                        <b-button type="button" @click="onCancel" variant="outline-danger">
                          <i class="fas fa-times"></i> Cancelar
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
  props: ["titulo", "url", "url_show", "url_update", "url_store", "sort_by"],
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
        { key: "tipo", label: "Tipo Classificação", sortable: true },
        { key: "enabled", label: "Habilitado", sortable: true },
        { key: "default_timeout", label: "Padrão Timeout", sortable: true },
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
        } else {
          _this.$msgError(response.data.message);
        }
      } catch (errors) {
        if (errors.response.status == 422) {
          _this.errors = errors.response.data;
          _this.$msgError("Erros encontrados");
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
    _this.loadData();
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
