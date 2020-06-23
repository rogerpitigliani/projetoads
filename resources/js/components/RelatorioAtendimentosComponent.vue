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
                    :date-range="filter.periodo"
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
              <b-col cols="4">
                <b-form-group
                  label-cols-sm="12"
                  label-align-sm="right"
                  label-size="sm"
                  label-for="filterInput"
                  label="Pesquisar/Protocolo"
                >
                  <b-input-group>
                    <b-form-input v-model="filter.text" placeholder="Pesquisar"></b-form-input>
                  </b-input-group>
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
                    <b-form-select v-model="filter.equipe_id" :options="lista_equipes"></b-form-select>
                    <b-form-select v-model="filter.usuario_id" :options="lista_usuarios"></b-form-select>
                    <b-form-select
                      v-model="filter.classificacao_id"
                      :options="lista_classificacoes"
                    ></b-form-select>
                    <b-form-select v-model="filter.canal" :options="lista_canais"></b-form-select>

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
              show-empty
            >
              <template v-slot:empty="scope">
                <div class="text-center">Nenhum registro encontrado</div>
              </template>

              <template v-slot:table-busy>
                <div class="text-center text-primary my-2">
                  <b-spinner class="align-middle"></b-spinner>
                  <strong>Carregando...</strong>
                </div>
              </template>

              <template
                v-slot:cell(datahora_inicio)="data"
              >{{ moment(data.value, 'YYYY-MM-DD HH:mm:ss').format("DD/MM/YYYY HH:mm:ss") }}</template>

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

              <template v-slot:cell(canal)="row">
                <div v-if="row.item.canal == 'chat'">
                  <i class="far fa-comment"></i> Chat/Site
                </div>
                <div v-if="row.item.canal == 'whatsapp'">
                  <i class="fab fa-whatsapp"></i> WhatsApp
                </div>
                <div v-if="row.item.canal == 'telegram'">
                  <i class="fab fa-telegram"></i> Telegram
                </div>
                <div v-if="row.item.canal == 'facebook'">
                  <i class="fab fa-facebook"></i> Facebook
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
                  @click="view(row.item, row.index, $event.target)"
                  title="Visualizar"
                >
                  <i class="fas fa-eye"></i>
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
  props: [
    "titulo",
    "url_data",
    "usuario",
    "usuarios",
    "classificacoes",
    "equipes"
  ],
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
        { key: "datahora_inicio", label: "Data/Hora", sortable: true },
        { key: "canal", label: "Canal", sortable: true },
        { key: "login", label: "Atendente", sortable: true },
        { key: "equipe", label: "Equipe", sortable: true },
        { key: "status", label: "Status", sortable: true },
        { key: "classificacao", label: "Classificacao", sortable: true },
        { key: "protocolo", label: "Protocolo", sortable: true },
        { key: "actions", label: "", sortable: false, class: "column-action" }
      ],
      dataArray: [],
      filter: {
        usuario_id: null,
        classificacao_id: null,
        equipe_id: null,
        canal: "",
        text: "",
        periodo: {
          startDate: this.moment().startOf("month"),
          endDate: this.moment()
        }
      },
      //filter_usuario_id: null,
      //filter_classificacao_id: null,
      //filter_equipe_id: null,
      //filter_canal: "",
      //filter_text: "",
      //filter_date_range: null,
      //filter_periodo: {
      //  startDate: this.moment().startOf("month"),
      //  endDate: this.moment()
      //},
      lista_usuarios: [],
      lista_classificacoes: [],
      lista_equipes: [],
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
    view: function(row, index, e) {
      console.log("ROW", row);
    },
    updateValues: function(data) {
      console.log("Data Alterada: ", data);
      this.filter.periodo = data;
    },

    limparFiltros: async function() {
      this.filter.usuario_id = null;
      this.filter.classificacao_id = null;
      this.filter.equipe_id = null;
      this.filter.canal = "";
      this.filter.text = "";
      this.filter.periodo.startDate = this.moment().startOf("month");
      this.filter.periodo.endDate = this.moment();
      await this.$nextTick();
      this.filtrar();
    },
    filtrar: function() {
      this.loadData();
    },

    onPaginate: function(page) {
      console.log("Paginate", page);
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
      var params = _this.filter;

      params.periodo.startDate = _this
        .moment(params.periodo.startDate)
        .format("YYYY-MM-DD 00:00:00");
      params.periodo.endDate = _this
        .moment(params.periodo.endDate)
        .format("YYYY-MM-DD 23:59:59");

      console.log("Params", params);

      var res = await axios.post(_this.url_data, params);
      _this.dataArray = res.data;
      _this.loading = false;
    }
  },
  mounted() {
    var _this = this;
    _this.form_vazio = _this.form;
    _this.usuario_logado = JSON.parse(_this.usuario);
    _this.lista_equipes = [
      ...[{ value: null, text: "Equipe" }],
      ...JSON.parse(_this.equipes)
    ];

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
      return this.dataArray;
      //   return this.filter
      //     ? this.dataArray.filter(
      //         item =>
      //           item.canal.includes(this.filter) ||
      //           item.nome.includes(this.filter) ||
      //           item.id == this.filter
      //       )
      //     : this.dataArray;
    }
  }
};
</script>
