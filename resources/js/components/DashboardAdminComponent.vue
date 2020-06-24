<template>
  <b-container>
    <b-card header-bg-variant="primary" header-text-variant="white" no-body>
      <b-card-header header-bg-variant="primary" header-text-variant="white">
        <i class="far fa-chart-bar"></i>
        {{ titulo }}
        <div class="float-right" style="padding: 0px">
          <b-button pill variant="primary" title="Reload" size="sm">
            <i class="fas fa-redo"></i>
          </b-button>
          <b-dropdown size="sm" variant="primary" pill>
            <template v-slot:button-content>
              <i class="fas fa-filter" aria-hidden="true"></i>
            </template>
            <b-dropdown-item-button @click="changePeriodo('hoje')">Hoje</b-dropdown-item-button>
            <b-dropdown-item-button @click="changePeriodo('ontem')">Ontem</b-dropdown-item-button>
            <b-dropdown-item-button @click="changePeriodo('estasemana')">Esta Semana</b-dropdown-item-button>
            <b-dropdown-item-button @click="changePeriodo('estemes')">Este Mês</b-dropdown-item-button>
            <b-dropdown-item-button @click="changePeriodo('ultimahora')">Ultima Hora</b-dropdown-item-button>
          </b-dropdown>
          <!-- <div class="dropdown d-inline-block">
            <button
              class="btn btn-secondary dropdown-toggle"
              type="button"
              id="dropdownMenuButton"
              data-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
            >Dropdown</button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
              <a class="dropdown-item" href="#">Action</a>
              <a class="dropdown-item" href="#">Another action</a>
              <a class="dropdown-item" href="#">Something else here</a>
            </div>
          </div>-->
        </div>
      </b-card-header>
      <b-card-body class="text-center">
        <b-container>
          <b-row>
            <b-col cols="12" md="3">
              <card-info-component
                value="1"
                label="Facebook"
                variant="primary"
                icon="fab fa-facebook"
              ></card-info-component>
            </b-col>
            <b-col cols="12" md="3">
              <card-info-component
                value="1"
                label="Telegram"
                variant="primary"
                icon="fab fa-telegram"
              ></card-info-component>
            </b-col>
            <b-col cols="12" md="3">
              <card-info-component
                value="2"
                label="WhatsApp"
                variant="success"
                icon="fab fa-whatsapp"
              ></card-info-component>
            </b-col>

            <b-col cols="12" md="3">
              <card-info-component
                value="2"
                label="Chat/Site"
                variant="warning"
                icon="fas fa-comment"
              ></card-info-component>
            </b-col>
          </b-row>

          <b-row>
            <b-col cols="12" md="6">
              <grafico-donut-component></grafico-donut-component>
            </b-col>
            <b-col cols="12" md="6">
              <grafico-donut-component></grafico-donut-component>
            </b-col>
          </b-row>

          <b-row>
            <b-col cols="12" md="6">
              <grafico-barra-component></grafico-barra-component>
            </b-col>
            <b-col cols="12" md="6">
              <grafico-barra-component></grafico-barra-component>
            </b-col>
          </b-row>
        </b-container>
      </b-card-body>
    </b-card>
  </b-container>
</template>

<script>
export default {
  props: ["usuario", "titulo"],
  data() {
    return {
      filtro_periodo: "hoje",
      periodo: {
        start: this.moment().format("YYYY-MM-DD 00:00:00"),
        end: this.moment().format("YYYY-MM-DD 23:59:59")
      }
    };
  },
  methods: {
    changePeriodo: function(v) {
      console.log("Periodo Alterado para ", v);
    }
  },
  mounted() {
    console.log("Component mounted.");
  }
};
</script>
