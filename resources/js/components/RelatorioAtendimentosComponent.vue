<template>
  <b-container>
    <b-row>
      <b-col>
        <b-card :header="titulo" header-bg-variant="primary" header-text-variant="white">
          <div>
            <b-row>
              <b-col>
                <b-form-group
                  label-cols-sm="12"
                  label-align-sm="left"
                  label-size="sm"
                  label-for="filterInput"
                  label="Período"
                >
                  <date-range-picker
                    :date-range="filter_periodo"
                    @update="updateValues"
                    :locale-data="locale"
                    opens="right"
                    :autoApply="true"
                    :ranges="false"
                    :append-to-body="true"
                  >
                    <!--Optional scope for the input displaying the dates -->
                    <div
                      slot="input"
                      slot-scope="picker"
                    >{{ moment(picker.startDate).format('DD/MM/YYYY') }} - {{ moment(picker.endDate).format('DD/MM/YYYY') }}</div>
                  </date-range-picker>
                </b-form-group>
              </b-col>
            </b-row>
            <b-row>
              <b-col>
                <b-form-group
                  label-cols-sm="12"
                  label-align-sm="right"
                  label-size="sm"
                  label-for="filterInput"
                >
                  <b-input-group size="sm">
                    <b-form-input v-model="filter_text" placeholder="Palavra / Mensagem"></b-form-input>
                    <b-form-select v-model="filter_usuario_id" :options="lista_usuarios"></b-form-select>
                    <b-form-select
                      v-model="filter_classificacao_id"
                      :options="lista_classificacoes"
                    ></b-form-select>
                    <b-form-select v-model="filter_canal" :options="lista_canais"></b-form-select>

                    <b-input-group-append>
                      <b-button pill variant="outline-primary" class="success" @click="filtrar">
                        <i class="fas fa-search"></i> Filtrar
                      </b-button>
                      <b-button
                        pill
                        variant="outline-warning"
                        class="success"
                        @click="limparFiltros"
                      >
                        <i class="fas fa-eraser"></i> Limpar
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
          </div>>
        </b-card>
      </b-col>
    </b-row>
  </b-container>
</template>

<script>
import DateRangePicker from "vue2-daterange-picker";
// import "vue2-daterange-picker/dist/vue2-daterange-picker.css";
import "vue2-daterange-picker/dist/vue2-daterange-picker.css";

export default {
  props: ["titulo", "url_data", "usuario", "usuarios", "classificacoes"],
  components: {
    DateRangePicker
  },
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
        { key: "datahora", label: "Data/Hora", sortable: true },
        { key: "canal", label: "Canal", sortable: true },
        { key: "usuario", label: "Atendente", sortable: true },
        { key: "classificacao", label: "Classificacao", sortable: true },
        { key: "actions", label: "", sortable: false, class: "column-action" }
      ],
      dataArray: [],
      filter: null,
      filter_usuario_id: null,
      filter_classificacao_id: null,
      filter_canal: "",
      filter_text: "",
      filter_date_range: null,
      filter_periodo: {
        startDate: this.moment().startOf("month"),
        endDate: this.moment()
      },
      lista_usuarios: [],
      lista_classificacoes: [],
      lista_canais: [
        { value: "", text: "Qualquer Canal" },
        { value: "whatsapp", text: "WhatsApp" },
        { value: "telegram", text: "Telegram" },
        { value: "facebook", text: "Facebook" },
        { value: "chat", text: "Chat/Site" }
      ],
      locale: {
        direction: "ltr", //direction of text
        format: "dd/mm/yyyy", //fomart of the dates displayed
        separator: " - ", //separator between the two ranges
        weekLabel: "W",
        customRangeLabel: "Custom Range",
        daysOfWeek: this.moment.weekdaysMin(), //array of days - see moment documenations for details
        monthNames: this.moment.monthsShort(), //array of month names - see moment documenations for details
        firstDay: 1, //ISO first day of week - see moment documenations for details
        showWeekNumbers: true //show week numbers on each row of the calendar
      },
      ranges: {
        //default value for ranges object (if you set this to false ranges will no be rendered)
        Today: [this.moment(), this.moment()],
        Ontem: [
          this.moment().subtract(1, "days"),
          this.moment().subtract(1, "days")
        ],
        "Este mês": [
          this.moment().startOf("month"),
          this.moment().endOf("month")
        ],
        "Semana passada": [
          this.moment()
            .subtract(1, "week")
            .startOf("week"),
          this.moment()
            .subtract(1, "week")
            .endOf("week")
        ],
        "Mês passado": [
          this.moment()
            .subtract(1, "month")
            .startOf("month"),
          this.moment()
            .subtract(1, "month")
            .endOf("month")
        ]
      }
    };
  },
  methods: {
    updateValues: function(data) {
      console.log("Data Alterada: ", data);
    },

    limparFiltros: async function() {
      this.filter_usuario_id = null;
      this.filter_classificacao_id = null;
      this.filter_canal = "";
      this.filter_text = "";
      await this.$nextTick();
      this.filtrar();
    },
    filtrar: function() {
      alert("Filtrar");
    },

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
        if (key == "admin") {
          _this.form[key] = false;
        } else if (key == "atendente") {
          _this.form[key] = false;
        } else if (key == "supervisor") {
          _this.form[key] = false;
        } else {
          _this.form[key] = null;
        }
      });

      // console.log("RESET", _this.form);
    },
    loadData: async function() {
      var _this = this;
      _this.loading = true;
      var res = await axios.get(_this.url_data + "?datatype=json", {});
      _this.dataArray = res.data;
      _this.loading = false;
    }
  },
  mounted() {
    var _this = this;
    _this.form_vazio = _this.form;
    _this.usuario_logado = JSON.parse(_this.usuario);
    _this.lista_usuarios = [
      ...[{ value: null, text: "Usuário" }],
      ...JSON.parse(_this.usuarios)
    ];
    _this.lista_classificacoes = [
      ...[{ value: null, text: "Classificação" }],
      ...JSON.parse(_this.classificacoes)
    ];

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
              item.nome.includes(this.filter) ||
              item.id == this.filter
          )
        : this.dataArray;
    }
  }
};
</script>
