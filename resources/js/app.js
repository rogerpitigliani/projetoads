/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import { BootstrapVue, BootstrapVueIcons } from "bootstrap-vue";
import VueApexCharts from 'vue-apexcharts'


import Notifications from 'vue-notification';
import moment from 'moment'

Vue.prototype.moment = moment;
moment.locale('pt-BR');

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('usuario-component', require('./components/UsuarioComponent.vue').default);
Vue.component('classificacao-component', require('./components/ClassificacaoComponent.vue').default);
Vue.component('equipe-component', require('./components/EquipeComponent.vue').default);
Vue.component('botconfig-component', require('./components/BotConfigComponent.vue').default);
Vue.component('atendimento-component', require('./components/AtendimentoComponent.vue').default);
Vue.component('relatorio-atendimentos-component', require('./components/RelatorioAtendimentosComponent.vue').default);
Vue.component('dashboard-admin-component', require('./components/DashboardAdminComponent.vue').default);



Vue.component('card-info-component', require('./components/CardInfoComponent.vue').default);
Vue.component('card-info-icon-component', require('./components/CardInfoIconComponent.vue').default);

Vue.component('grafico-donut-component', require('./components/GraficoDonutComponent.vue').default);
Vue.component('grafico-barra-component', require('./components/GraficoBarrasComponent.vue').default);


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */


String.prototype.includes = function () {
    'use strict';
    return String.prototype.indexOf.apply(this, arguments) !== -1;
};

Vue.prototype.$eventBus = new Vue();

Vue.prototype.$msgSuccess = function (msg) {
    this.$notify({
        group: 'alert',
        title: 'Sucesso!',
        text: msg,
        type: 'success',
        duration: 3000,
        speed: 500,
    });
}
Vue.prototype.$msgError = function (msg) {
    this.$notify({
        group: 'alert',
        title: 'Erro!',
        text: msg,
        type: 'error',
        duration: 3000,
        speed: 500,
    });
}
Vue.prototype.$msgWarning = function (msg) {
    this.$notify({
        group: 'alert',
        title: 'Alerta!',
        text: msg,
        type: 'warn',
        duration: 3000,
        speed: 500,
    });
}

Vue.use(BootstrapVue);
Vue.use(BootstrapVueIcons)

Vue.use(Notifications);

Vue.use(VueApexCharts)

Vue.component('apexchart', VueApexCharts)

const app = new Vue({
    el: '#app',
});
