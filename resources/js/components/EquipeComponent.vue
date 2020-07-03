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
                      <b-form-group id="group-equipe" label="Nome da Equipe:" label-for="equipe">
                        <b-form-input
                          id="equipe"
                          v-model="form.equipe"
                          required
                          placeholder="Nome da Equipe"
                        ></b-form-input>
                      </b-form-group>
                    </b-col>
                  </b-row>

                  <b-row>
                    <b-col>
                      <b-form-group label="Lista de Usuários" label-for="usuarios_list">
                        <b-form-select
                          id="usuarios_list"
                          v-model="usuarios_selecionados"
                          :options="usuarios_list"
                          :select-size="6"
                          multiple
                        ></b-form-select>
                      </b-form-group>
                      <!-- <pre>{{ usuarios_list }}</pre> -->
                    </b-col>
                    <b-col cols="1">
                      <b-form-group label="Ações">
                        <b-row>
                          <b-button
                            pill
                            block
                            variant="outline-secondary"
                            title="Adicionar"
                            @click="memberUsuarioAdd"
                          >
                            <i class="fas fa-angle-right"></i>
                            <!-- <b-icon icon="chevron-compact-right"></b-icon> -->
                          </b-button>
                        </b-row>
                        <b-row>
                          <b-button
                            pill
                            block
                            variant="outline-secondary"
                            title="Remover"
                            @click="memberUsuarioRemove"
                          >
                            <i class="fas fa-angle-left"></i>
                            <!-- <b-icon icon="chevron-compact-left"></b-icon> -->
                          </b-button>
                        </b-row>
                        <b-row>
                          <b-button
                            pill
                            block
                            variant="outline-secondary"
                            title="Adicionar Todos"
                            @click="memberUsuarioAddAll"
                          >
                            <i class="fas fa-angle-double-right"></i>
                            <!-- <b-icon icon="chevron-double-right"></b-icon> -->
                          </b-button>
                        </b-row>
                        <b-row>
                          <b-button
                            pill
                            block
                            variant="outline-secondary"
                            title="Remover Todos"
                            @click="memberUsuarioRemoveAll"
                          >
                            <i class="fas fa-angle-double-left"></i>
                            <!-- <b-icon icon="chevron-double-left"></b-icon> -->
                          </b-button>
                        </b-row>
                      </b-form-group>
                    </b-col>
                    <b-col>
                      <b-form-group
                        label="Usuários/Membros Selecionados"
                        label-for="membros_usuarios"
                      >
                        <b-form-select
                          id="membros_usuarios"
                          v-model="membros_selecionados"
                          :options="membros_usuarios"
                          :select-size="6"
                          multiple
                        ></b-form-select>
                      </b-form-group>
                      <!-- <pre>{{ membros_usuarios }}</pre> -->
                    </b-col>
                  </b-row>

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
        { key: "equipe", label: "Equipe", sortable: true },
        { key: "qtde_membros", label: "Qtde Membros", sortable: true },
        { key: "actions", label: "", sortable: false, class: "column-action" }
      ],
      dataArray: [],
      filter: null,
      editando: false,
      form: {
        id: null,
        equipe: null
      },
      form_vazio: {},
      errors: null,
      usuarios_list: [],
      usuarios_selecionados: [],
      membros_usuarios: [],
      membros_selecionados: []
    };
  },
  methods: {
    memberUsuarioAdd() {
      var _this = this;
      var selected = _this.usuarios_selecionados.splice(0);
      for (var i = 0; i < selected.length; i++) {
        let value = selected[i];
        var index = _this.usuarios_list.findIndex(function(el) {
          return el.value == value;
        });
        _this.membros_usuarios.push(_this.usuarios_list[index]);
        _this.usuarios_list.splice(index, 1);
        _this.membros_usuarios.sort(function(a, b) {
          if (a.text < b.text) return -1;
          if (a.text > b.text) return 1;
          return 0;
        });
      }
    },
    memberUsuarioRemove() {
      var _this = this;
      var selected = _this.membros_selecionados.splice(0);
      for (var i = 0; i < selected.length; i++) {
        let value = selected[i];
        var index = _this.membros_usuarios.findIndex(function(el) {
          return el.value == value;
        });
        _this.usuarios_list.push(_this.membros_usuarios[index]);
        _this.membros_usuarios.splice(index, 1);
        _this.usuarios_list.sort(function(a, b) {
          if (a.text < b.text) return -1;
          if (a.text > b.text) return 1;
          return 0;
        });
      }
    },
    memberUsuarioRemoveAll() {
      var _this = this;
      _this.usuarios_list = [..._this.usuarios_list, ..._this.membros_usuarios];
      _this.membros_usuarios = [];
      _this.usuarios_list.sort(function(a, b) {
        if (a.text < b.text) return -1;
        if (a.text > b.text) return 1;
        return 0;
      });
    },
    memberUsuarioAddAll() {
      var _this = this;
      _this.membros_usuarios = [
        ..._this.usuarios_list,
        ..._this.membros_usuarios
      ];
      _this.usuarios_list = [];
      _this.membros_usuarios.sort(function(a, b) {
        if (a.text < b.text) return -1;
        if (a.text > b.text) return 1;
        return 0;
      });
    },

    onPaginate: function(page) {
      console.log("Paginate", page);
    },
    addnew: async function() {
      var _this = this;
      _this.resetForm();
      var res_usuarios = await axios.get(
        _this.url_usuarios + "?datatype=list",
        {}
      );
      _this.usuarios_list = res_usuarios.data;
      _this.editando = true;
    },
    edit: async function(item, index, event) {
      var _this = this;
      _this.resetForm();
      _this.editando = true;
      _this.loading = true;
      var url = this.url_show.replace("ID", item.id);
      var res = await axios.get(url, {});
      var res_usuarios = await axios.get(
        _this.url_usuarios + "?datatype=list",
        {}
      );
      _this.usuarios_list = res_usuarios.data;
      _this.form = res.data;
      for (var i = 0; i < _this.form.usuarios.length; i++) {
        for (var j = 0; j < _this.usuarios_list.length; j++) {
          if (_this.form.usuarios[i].id == _this.usuarios_list[j].value) {
            //console.log("Remover", _this.model.ramais[i].ramal);
            _this.membros_usuarios.push(_this.usuarios_list[j]);
            _this.usuarios_list.splice(j, 1);
          }
        }
      }
      _this.membros_usuarios.sort(function(a, b) {
        if (a.text < b.text) return -1;
        if (a.text > b.text) return 1;
        return 0;
      });
      _this.loading = false;
    },
    onSubmit: async function(e) {
      var _this = this;

      try {
        var response = null;
        _this.form.membros_usuarios = _this.membros_usuarios;

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
      _this.usuarios_list = [];
      _this.membros_usuarios = [];
      _this.errors = null;
      Object.keys(_this.form).forEach(function(key, index) {
        _this.form[key] = null;
      });

      // this.form = this.form_vazio;
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
            item => item.equipe.includes(this.filter) || item.id == this.filter
          )
        : this.dataArray;
    }
  }
};
</script>
