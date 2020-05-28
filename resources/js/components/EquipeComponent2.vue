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

              <template v-slot:cell(admin)="row">
                <div>
                  <b-form-checkbox
                    v-model="row.item.admin"
                    name="check-button"
                    switch
                    disabled
                    size="lg"
                  ></b-form-checkbox>
                </div>
              </template>

              <template v-slot:cell(supervisor)="row">
                <div>
                  <b-form-checkbox
                    v-model="row.item.supervisor"
                    name="check-button"
                    switch
                    disabled
                    size="lg"
                  ></b-form-checkbox>
                </div>
              </template>

              <template v-slot:cell(atendente)="row">
                <div>
                  <b-form-checkbox
                    v-model="row.item.atendente"
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
                      <b-form-group id="group-login" label="Usuário:" label-for="login">
                        <b-form-input
                          id="login"
                          v-model="form.login"
                          required
                          placeholder="Usuário"
                        ></b-form-input>
                      </b-form-group>
                    </b-col>
                    <b-col>
                      <b-form-group id="group-name" label="Nome:" label-for="name">
                        <b-form-input id="name" v-model="form.name" required placeholder="Nome"></b-form-input>
                      </b-form-group>
                    </b-col>
                  </b-row>
                  <b-row>
                    <b-col>
                      <b-form-group id="group-password" label="Senha:" label-for="password">
                        <b-form-input
                          id="password"
                          v-model="form.password"
                          :required="(form.id == null)"
                          placeholder="Senha"
                          type="password"
                        ></b-form-input>
                      </b-form-group>
                    </b-col>
                    <b-col>
                      <b-form-group
                        id="group-password_confirmation"
                        label="Confirmação Senha:"
                        label-for="password_confirmation"
                      >
                        <b-form-input
                          id="password_confirmation"
                          :required="!(form.password == null)"
                          v-model="form.password_confirmation"
                          placeholder="Confirmação Senha"
                          type="password"
                        ></b-form-input>
                      </b-form-group>
                    </b-col>
                  </b-row>
                  <b-row>
                    <b-col>
                      <b-form-group
                        id="group-admin"
                        label="Administrador"
                        label-for="checkboxes-admin"
                      >
                        <b-form-checkbox-group v-model="form.admin" id="checkboxes-admin">
                          <b-form-checkbox :value="true">Sim</b-form-checkbox>
                          <b-form-checkbox :value="false">Não</b-form-checkbox>
                        </b-form-checkbox-group>
                      </b-form-group>
                    </b-col>
                    <b-col>
                      <b-form-group
                        id="group-supervisor"
                        label="Supervisor"
                        label-for="checkboxes-supervisor"
                      >
                        <b-form-checkbox-group v-model="form.supervisor" id="checkboxes-supervisor">
                          <b-form-checkbox :value="true">Sim</b-form-checkbox>
                          <b-form-checkbox :value="false">Não</b-form-checkbox>
                        </b-form-checkbox-group>
                      </b-form-group>
                    </b-col>
                    <b-col>
                      <b-form-group
                        id="group-atendente"
                        label="Atendente"
                        label-for="checkboxes-atendente"
                      >
                        <b-form-checkbox-group v-model="form.atendente" id="checkboxes-atendente">
                          <b-form-checkbox :value="true">Sim</b-form-checkbox>
                          <b-form-checkbox :value="false">Não</b-form-checkbox>
                        </b-form-checkbox-group>
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
  props: [
    "titulo",
    "url",
    "url_show",
    "url_update",
    "url_store",
    "sort_by",
    "url_usuarios"
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
        { key: "login", label: "Usuário", sortable: true },
        { key: "name", label: "Nome", sortable: true },
        { key: "admin", label: "Admin", sortable: true },
        { key: "supervisor", label: "Supervisor", sortable: true },
        { key: "atendente", label: "Atendente", sortable: true },
        { key: "actions", label: "", sortable: false, class: "column-action" }
      ],
      dataArray: [],
      filter: null,
      editando: false,
      form: {
        id: null,
        login: null,
        senha: null,
        empresa: null,
        password: null,
        admin: false,
        supervisor: false,
        atendente: false
      },
      form_vazio: {},
      errors: null
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
      res.data.password = null;
      res.data.password_confirmation = null;
      _this.form = res.data;
      _this.loading = false;
    },
    onSubmit: async function(e) {
      var _this = this;

      try {
        var response = null;
        if (_this.form.id) {
          var url = _this.url_update.replace("ID", _this.form.id);
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
        _this.form[key] = null;
      });
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
              item.login.includes(this.filter) ||
              item.empresa.includes(this.filter) ||
              item.id == this.filter
          )
        : this.dataArray;
    }
  }
};
</script>
