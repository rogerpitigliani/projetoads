<template>
  <b-container>
    <b-card header-bg-variant="primary" header-text-variant="white" no-body>
      <b-card-header header-bg-variant="primary" header-text-variant="white">
        <i class="far fa-chart-bar"></i>
        {{ titulo }} ({{filtro_periodo_label}})
        <div class="float-right" style="padding: 0px">
          <b-button pill variant="primary" title="Reload" size="sm" @click="loadData">
            <i class="fas fa-redo"></i>
          </b-button>
          <b-dropdown size="sm" variant="primary" pill right>
            <template v-slot:button-content>
              <i class="fas fa-filter" aria-hidden="true"></i>
            </template>
            <b-dropdown-item-button @click="changePeriodo('hoje')">Hoje</b-dropdown-item-button>
            <b-dropdown-item-button @click="changePeriodo('ontem')">Ontem</b-dropdown-item-button>
            <b-dropdown-item-button @click="changePeriodo('estasemana')">Esta Semana</b-dropdown-item-button>
            <b-dropdown-item-button @click="changePeriodo('estemes')">Este Mês</b-dropdown-item-button>
            <b-dropdown-item-button @click="changePeriodo('ultimahora')">Última Hora</b-dropdown-item-button>
          </b-dropdown>
        </div>
      </b-card-header>
      <b-card-body class="text-center">
        <b-container>
          <b-row>
            <b-col cols="12">
              <b-container style="padding: 10px !important;">
                <b-row>
                  <b-col cols="12" md="4">
                    <card-info-component
                      :value="dash.total"
                      label="Total Atendidos"
                      variant="atendida"
                      icon="fas fa-flag"
                    ></card-info-component>
                  </b-col>
                  <b-col cols="6" md="4">
                    <card-info-component
                      :value="dash.fila"
                      label="Na Fila"
                      variant="fila"
                      icon="fas fa-clock"
                    ></card-info-component>
                  </b-col>
                  <b-col cols="6" md="4">
                    <card-info-component
                      :value="dash.chat"
                      label="Em Atendimento"
                      variant="chat"
                      icon="fas fa-comment-dots"
                    ></card-info-component>
                  </b-col>
                </b-row>
              </b-container>
            </b-col>
          </b-row>
          <b-row>
            <b-col cols="12" md="12">
              <b-container style="padding: 10px !important;">
                <b-row>
                  <b-col cols="6" md="3">
                    <card-info-component
                      :value="dash.qtde.facebook"
                      label="Facebook"
                      variant="primary"
                      icon="fab fa-facebook"
                    ></card-info-component>
                  </b-col>
                  <b-col cols="6" md="3">
                    <card-info-component
                      :value="dash.qtde.telegram"
                      label="Telegram"
                      variant="primary"
                      icon="fab fa-telegram"
                    ></card-info-component>
                  </b-col>
                  <b-col cols="6" md="3">
                    <card-info-component
                      :value="dash.qtde.whatsapp"
                      label="WhatsApp"
                      variant="success"
                      icon="fab fa-whatsapp"
                    ></card-info-component>
                  </b-col>

                  <b-col cols="6" md="3">
                    <card-info-component
                      :value="dash.qtde.chat"
                      label="Chat/Site"
                      variant="warning"
                      icon="fas fa-comment"
                    ></card-info-component>
                  </b-col>
                </b-row>
              </b-container>
            </b-col>
          </b-row>

          <b-row>
            <b-col cols="12" md="6">
              <grafico-donut-component ref="equipe_donut" title="Equipe"></grafico-donut-component>
            </b-col>
            <b-col cols="12" md="6">
              <grafico-donut-component ref="tipoclassif_donut" title="Tipo de Classificação"></grafico-donut-component>
            </b-col>
          </b-row>

          <b-row>
            <b-col cols="12" md="6">
              <grafico-barra-component ref="top10usuario" title="Top 10 Atendentes"></grafico-barra-component>
            </b-col>
            <b-col cols="12" md="6">
              <grafico-barra-component ref="classif" title="Classificação"></grafico-barra-component>
            </b-col>
          </b-row>
        </b-container>
      </b-card-body>
    </b-card>
  </b-container>
</template>

<script>
export default {
  props: ["usuario", "titulo", "url_data"],
  data() {
    return {
      filtro_periodo: "hoje",
      filtro_periodo_label: "Hoje",
      periodo: {
        start: this.moment().format("YYYY-MM-DD 00:00:00"),
        end: this.moment().format("YYYY-MM-DD 23:59:59")
      },
      dash: {
        qtde: {
          facebook: 0,
          whatsapp: 0,
          telegram: 0,
          chat: 0
        },
        classif_values: [],
        classif_labels: [],
        equipe_values: [],
        equipe_labels: []
      }
    };
  },
  methods: {
    changePeriodo: function(v) {
      var _this = this;
      switch (v) {
        case "hoje":
          _this.periodo.start = _this.moment().format("YYYY-MM-DD 00:00:00");
          _this.periodo.end = _this.moment().format("YYYY-MM-DD 23:59:59");
          _this.filtro_periodo_label = "Hoje";
          break;
        case "ontem":
          _this.periodo.start = _this
            .moment()
            .subtract(1, "days")
            .format("YYYY-MM-DD 00:00:00");
          _this.periodo.end = _this
            .moment()
            .subtract(1, "days")
            .format("YYYY-MM-DD 23:59:59");
          _this.filtro_periodo_label = "Ontem";
          break;
        case "estasemana":
          _this.periodo.start = _this
            .moment()
            .startOf("week")
            .format("YYYY-MM-DD 00:00:00");
          _this.periodo.end = _this
            .moment()
            .endOf("week")
            .format("YYYY-MM-DD 23:59:59");
          _this.filtro_periodo_label = "Esta Semana";
          break;
        case "estemes":
          _this.periodo.start = _this
            .moment()
            .startOf("month")
            .format("YYYY-MM-DD 00:00:00");
          _this.periodo.end = _this
            .moment()
            .endOf("month")
            .format("YYYY-MM-DD 23:59:59");
          _this.filtro_periodo_label = "Este Mês";
          break;
        case "ultimahora":
          _this.periodo.start = _this
            .moment()
            .startOf("hour")
            .format("YYYY-MM-DD HH:mm:ss");
          _this.periodo.end = _this.moment().format("YYYY-MM-DD HH:mm:ss");
          _this.filtro_periodo_label = "Última Hora";
          break;
        default:
          _this.periodo.start = _this.moment().format("YYYY-MM-DD 00:00:00");
          _this.periodo.end = _this.moment().format("YYYY-MM-DD 23:59:59");
          _this.filtro_periodo_label = "Hoje";
          break;
      }
      _this.filtro_periodo = v;
      _this.loadData();
    },
    loadData: async function() {
      var _this = this;
      let params = {
        start: _this.periodo.start,
        end: _this.periodo.end
      };
      console.log("Params", params);
      let res = await axios.post(_this.url_data, params);

      _this.dash = res.data;

      _this.$refs.top10usuario.updateChart({
        series: res.data.top10usuario.map(v => v.qtde),
        labels: res.data.top10usuario.map(v => v.login)
      });
      _this.$refs.classif.updateChart({
        series: res.data.classif.map(v => v.qtde),
        labels: res.data.classif.map(v => v.classificacao)
      });

      _this.$refs.equipe_donut.updateChart({
        series: res.data.equipe_values,
        labels: res.data.equipe_labels
      });
      _this.$refs.tipoclassif_donut.updateChart({
        series: res.data.tipoclassif.map(v => v.qtde),
        labels: res.data.tipoclassif.map(v => v.tipo)
      });

      console.log(res.data.tipoclassif.map(v => v.qtde));
    }
  },
  mounted() {
    console.log("Component mounted.");
    this.loadData();
  }
};
</script>
