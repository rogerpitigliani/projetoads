<template>
  <b-container>
    <b-modal id="bv-modal-encerra" @ok="confirmaEncerramento">
      <template v-slot:modal-title>
        <i class="fas fa-check-circle"></i> Encerrar Atendimento
      </template>

      <b-container fluid>
        <form ref="form" @submit.stop.prevent="confirmaEncerramento">
          <b-row>
            <b-col>
              <b-form-group
                id="group-classificacao"
                label="Selecione uma Classificação para o atendimento realizado:"
                label-for="classificacao_id"
              >
                <b-form-select v-model="classificacao_id" :options="lista_classificacoes"></b-form-select>
              </b-form-group>
            </b-col>
          </b-row>
          <b-row>
            <b-col>
              <b-form-group id="group-classificacao" label="Observações:" label-for="observacoes">
                <b-form-textarea id="observacoes" size="lg" placeholder="..." v-model="observacoes"></b-form-textarea>
              </b-form-group>
            </b-col>
          </b-row>
        </form>
      </b-container>
    </b-modal>
    <b-row class="rowinfo">
      <b-col cols="6" sm="6" md="3">
        <b-card no-body border-variant="primary" class="text-center" text-variant="primary">
          <b-card-header
            header-bg-variant="primary"
            header-text-variant="white"
            class="header-card-info"
          >
            <i class="fas fa-user-check"></i> Atendidas
          </b-card-header>
          <b-card-body>
            <span style="font-size: 22px; font-weight: bold;">{{ qtde_atendimentos_hoje }}</span>
          </b-card-body>
        </b-card>
      </b-col>

      <b-col cols="6" sm="6" md="3">
        <b-card no-body border-variant="danger" class="text-center" text-variant="danger">
          <b-card-header
            header-bg-variant="danger"
            header-text-variant="white"
            class="header-card-info"
          >
            <i class="fas fa-user-clock"></i> Na Fila
          </b-card-header>
          <b-card-body>
            <span style="font-size: 22px;font-weight: bold;">{{ qtde_fila }}</span>
          </b-card-body>
        </b-card>
      </b-col>

      <b-col cols="12" sm="10" offset-sm="1" md="4" offset-md="2">
        <div style="padding-top: 10px;">
          <b-button
            v-if="!em_atendimento"
            variant="warning"
            size="lg"
            class="btn-block"
            @click="recebeAtendimento"
            :disabled="btn_receber_cliente_disabled"
          >
            <i class="fas fa-user-plus"></i> Receber Cliente
          </b-button>
          <b-button
            v-if="em_atendimento"
            class="btn-block"
            variant="success"
            size="lg"
            @click="encerrarAtendimento"
          >
            <i class="fas fa-times-circle"></i> Encerrar
          </b-button>
        </div>
      </b-col>
    </b-row>

    <b-row class="h-100">
      <b-col class="h-100">
        <b-card no-body>
          <b-card-header header-bg-variant="primary" header-text-variant="white" class="h-100">
            <div v-if="em_atendimento" v-html="get_dados_contato"></div>

            <span v-else>
              <i class="fas fa-comments"></i> Disponível
            </span>
          </b-card-header>

          <div id="messages_content" ref="msgcontent" class="scroll">
            <template v-for="msg in mensagens">
              <b-list-group :key="msg.id">
                <b-list-group-item
                  class="d-flex align-items-center mgs-group-item-in"
                  v-if="msg.direcao=='in'"
                  title="Recebida"
                >
                  <b-avatar :src="contato.photo_uri" class="mr-3"></b-avatar>
                  <span class="mr-auto msg-content">
                    {{msg.content}}
                    <span
                      class="msg-horario"
                    >{{ moment(msg.created_at,"YYYY-MM-DD HH:mm:ss").format("DD/MM/YYYY HH:mm:ss") }}</span>
                  </span>
                  <b-avatar size="sm" variant="danger-outline" icon="arrow-down" class="mr-3"></b-avatar>
                </b-list-group-item>

                <b-list-group-item
                  class="d-flex align-items-center mgs-group-item-out"
                  v-if="msg.direcao=='out'"
                  title="Enviada"
                >
                  <b-avatar size="sm" variant="success-outline" icon="arrow-up" class="mr-3"></b-avatar>
                  <span class="mr-auto msg-content-out text-right">
                    {{msg.content}}
                    <br />
                    <span
                      class="msg-horario"
                    >{{ moment(msg.created_at,"YYYY-MM-DD HH:mm:ss").format("DD/MM/YYYY HH:mm:ss") }}</span>
                  </span>
                  <b-avatar variant="primary" src="/img/bot_imagem.png" class="mr-3"></b-avatar>
                </b-list-group-item>
              </b-list-group>
            </template>
          </div>
          <!--
          <div id="messages_content" ref="msgcontent" class="scroll">
            <ul class="chat-list">
              <template v-for="msg in mensagens">
                <li class="in" v-if="msg.direcao == 'in'" v-bind:key="msg.id">
                  <div class="chat-img">
                    <img alt="Avtar" :src="contato.photo_uri" />
                  </div>
                  <div class="chat-body">
                    <div class="chat-message">
                      <p>{{ msg.content }}</p>
                    </div>
                  </div>
                </li>
                <li class="out" v-if="msg.direcao == 'out'" v-bind:key="msg.id">
                  <div class="chat-img">
                    <img alt="Avtar" src="/img/bot_imagem.png" />
                  </div>
                  <div class="chat-body">
                    <div class="chat-message">
                      <p>{{ msg.content }}</p>
                    </div>
                  </div>
                </li>
              </template>
            </ul>
          </div>-->
          <b-row>
            <b-col>&nbsp;</b-col>
          </b-row>
          <b-row>
            <b-col>
              <b-input-group>
                <b-form-textarea
                  no-resize
                  v-model="message"
                  id="message"
                  wrap="<br>"
                  size="lg"
                  placeholder="Digite a mensagem aqui!"
                  :disabled="!em_atendimento"
                  @keyup.enter.exact="sendMessage($event)"
                ></b-form-textarea>
                <b-button
                  variant="primary"
                  size="lg"
                  title="Enviar Mensagem"
                  :disabled="this.message==''"
                  @click="sendMessage()"
                >
                  <i class="fas fa-paper-plane fa-2x"></i>
                </b-button>
              </b-input-group>
            </b-col>
          </b-row>

          <!-- <b-row>
            <b-col>
              <div class="text-right">
                <b-button type="link" variant="light" title="Anexar">
                  <i class="fas fa-paperclip"></i>
                </b-button>
                <b-button type="link" variant="light" title="Audio">
                  <i class="fas fa-microphone"></i>
                </b-button>
              </div>
            </b-col>
          </b-row>-->
          <b-row>
            <b-col sm="10"></b-col>
          </b-row>
        </b-card>
      </b-col>
    </b-row>
  </b-container>
</template>

<script>
import socketio from "../socketio";
export default {
  mixins: [socketio],
  props: ["titulo", "socket_host", "socket_port", "usuario_id", "usuario"],
  data() {
    return {
      message: "",
      shouldScroll: false,
      clientes_nafila: 12,
      nomecliente: null,
      em_atendimento: false,
      titulo_atendimento: "Disponível",
      mensagens: [],
      atendimento: {
        id: 0,
        canal: "none"
      },
      contato: null,
      qtde_atendimentos_hoje: 0,
      qtde_fila: 0,
      btn_receber: false,
      usuario_logado: null,
      usuario_equipes: [],
      lista_classificacoes: [],
      observacoes: null,
      classificacao_id: null
    };
  },
  methods: {
    scrollToEnd() {
      var container = document.querySelector(".scroll");
      var scrollHeight = container.scrollHeight;
      container.scrollTop = scrollHeight;
    },
    confirmaEncerramento: function(e) {
      var _this = this;
      if (!_this.classificacao_id) {
        _this.$msgError("Selecione uma classificação para encerrar!");
        e.preventDefault();
        return false;
      } else {
        _this.atendimento.classificacao_id = _this.classificacao_id;
        _this.atendimento.observacoes = _this.observacoes;
        _this.sio.emit("encerrar_atendimento", _this.atendimento, data => {
          if (data.status == "OK") {
            _this.$msgSuccess(data.message);
            _this.refresh_dados();
          } else {
            _this.$msgError(data.message);
            e.preventDefault();
          }
        });
      }
    },
    encerrarAtendimento: async function() {
      var _this = this;
      _this.classificacao_id = null;
      _this.observacoes = "";

      _this.sio.emit(
        "get_lista_classificacoes",
        { usuario_id: _this.usuario_id },
        data => {
          _this.lista_classificacoes = data.list;
          _this.$bvModal.show("bv-modal-encerra");
        }
      );
    },

    refresh_dados: function() {
      var _this = this;
      _this.em_atendimento = false;
      _this.sio.emit(
        "atendimento_usuario",
        { usuario_id: _this.usuario_id },
        async data => {
          await _this.$nextTick();
          _this.atendimento = data.atendimento;
          _this.mensagens = data.mensagens;
          _this.contato = data.contato;
          if (_this.atendimento) _this.em_atendimento = true;
        }
      );

      _this.sio.emit(
        "get_qtde_fila_usuario",
        { usuario_id: _this.usuario_id },
        data => {
          _this.qtde_fila = data.qtde;
        }
      );
      _this.sio.emit(
        "get_qtde_atendimentos_hoje",
        { usuario_id: _this.usuario_id },
        data => {
          _this.qtde_atendimentos_hoje = data.qtde;
        }
      );
    },

    recebeAtendimento: async function() {
      var _this = this;
      var params = { usuario_id: _this.usuario_id };

      _this.sio.emit("get_proximo_atendimento", params, async data => {
        console.log("get_proximo_atendimento", data);
        await _this.$nextTick();

        if (data.atendimento) {
          _this.refresh_dados();
        }

        _this.atendimento = data.atendimento;
        _this.mensagens = data.mensagens;
        _this.contato = data.contato;
      });

      //   _this.mensagens = _this.mensagens2;
      //   _this.clientes_nafila--;
      //   _this.nomecliente = "Pedro";
      _this.em_atendimento = true;
      //   _this.titulo_atendimento =
      //   _this.titulo + " Pedro (51) 98246-4536 - ** SUPORTE **";

      await _this.$nextTick();
      // _this.scrollToBottom();

      //   setTimeout(async function() {
      //     await _this.$nextTick();
      //     _this.scrollToBottom();
      //     console.log("Rolando");
      //   }, 1200);
    },
    sendMessage: async function() {
      var _this = this;
      if (_this.message) {
        var msg = {
          type: "text/plain",
          content: _this.message,
          to: _this.atendimento.remote_id,
          atendimento_id: _this.atendimento.id,
          direcao: "out",
          created_at: this.moment().format("YYYY-MM-DD HH:mm:ss")
        };

        _this.sio.emit("send_message", msg, data => {
          if (data.status == "OK") {
            _this.mensagens.push(data.msg);
            _this.message = "";
          }
        });

        // _this.shouldScroll =
        //   _this.$refs.msgcontent.scrollTop +
        //     _this.$refs.msgcontent.clientHeight ===
        //   _this.$refs.msgcontent.scrollHeight;
        // if (!_this.shouldScroll) {
        //   await _this.$nextTick();
        //   _this.scrollToBottom();
        //   setTimeout(function() {
        //     _this.scrollToBottom();
        //   }, 1000);
        // }
      }
    },
    // scrollToBottom: function() {
    //   console.log("scrollTop", this.$refs.msgcontent.scrollTop);
    //   console.log("clientHeight", this.$refs.msgcontent.clientHeight);
    //   console.log("scrollHeight", this.$refs.msgcontent.scrollHeight);
    //   this.$refs.msgcontent.scrollTop =
    //     this.$refs.msgcontent.scrollHeight + 120;
    // },
    checkSessao: async function() {
      let res = await axios.get("/sessao", {});
      if (res.data.status == "offline") {
        window.location.href = "/home";
      }
    }
  },

  computed: {
    get_dados_contato: function() {
      var icone = "";
      if (this.atendimento) {
        switch (this.atendimento.canal) {
          case "telegram":
            icone = ` - Telegram <i class="fab fa-telegram"></i>`;
            break;
          case "facebook":
            icone = ` - Facebook <i class="fab fa-facebook"></i>`;
            break;
          case "whatsapp":
            icone = ` - Facebook <i class="fab fa-whatsapp"></i>`;
            break;
          case "chat":
            icone = ` - Chat <i class="fas fa-comment"></i>`;
            break;
        }
      }

      return `<i class="fas fa-comments"></i> Atendimento - Nome: ${this.contato.full_name}${icone}`;
      //   <span
      //     v-if="atendimento.canal == 'whatsap'"
      //   >
      //     WhatsApp
      //
      //   </span>
      //   <span v-if="atendimento.canal == 'telegram'">
      //     Telegram
      //     <i class="fab fa-telegram"></i>
      //   </span>
      //   <span v-if="atendimento.canal == 'chat'">
      //     Chat/Site
      //
      //   </span>
      //   <span v-if="atendimento.canal == 'facebook'">
      //
      //   </span>
    },
    fila_qtd: function() {
      var _this = this;
      var x = 0;
      _this.btn_receber = x > 0 ? true : false;
      return x;
    },
    btn_receber_cliente_disabled: function() {
      if (this.em_atendimento) return true;
      return this.qtde_fila == 0;
    }
  },

  mounted() {
    var _this = this;
    _this.usuario_logado = JSON.parse(_this.usuario);
    _this.usuario_equipes = _this.usuario_logado.equipes;

    (async function() {
      _this.connect_io(
        _this.socket_host,
        _this.socket_port,
        "chat",
        _this.usuario_id
      );

      await _this.$nextTick();
      _this.refresh_dados();
    })();

    _this.checkSessao();
    setInterval(function() {
      _this.checkSessao();
    }, 5000);

    this.scrollToEnd();
  },
  updated() {
    this.scrollToEnd();
  }
};
</script>
<style scoped>
.msg-chat.msg-in {
  background-color: aquamarine;
  color: #000;
}
.msg-chat.msg-out {
  background-color: coral;
  color: #000;
}

/* .chat-message p {
  line-height: 18px;
  margin: 0;
  padding: 0;
  white-space: pre-line;
  text-align: left;
  padding-top: 0px;
  padding-bottom: 0px;
} */

.msg-content {
  white-space: pre-line;
  width: 80%;
  padding-left: 10px;
}

.msg-content-out {
  white-space: pre-line;
  width: 85%;
}

.msg-icon {
  width: 30px;
  height: 30px;
  margin: 30px 0px;
}
.mgs-group-item-in {
  border-color: #e86f6f;
  margin-bottom: 5px;
  border-radius: 20px;
  color: #d30f20;
  font-family: Tahoma;
}

.mgs-group-item-out {
  border-color: #3f9652;
  margin-bottom: 5px;
  border-radius: 20px;
  color: #05330f;
  font-family: Tahoma;
}
.msg-horario {
  font-size: 8px;
  color: #727171;
}

/* AQUI COMECA ANTIGO */

#messages_content {
  height: calc(50vh - 80px);
  overflow-y: scroll;
}
/*
.chat-list {
  padding: 0;
  font-size: 0.8rem;
  min-height: 300px;
}

.chat-list li {
  margin-bottom: 10px;
  overflow: auto;
  color: #ffffff;
}

.chat-list .chat-img {
  float: left;
  width: 48px;
}

.chat-list .chat-img img {
  -webkit-border-radius: 50px;
  -moz-border-radius: 50px;
  border-radius: 50px;
  width: 100%;
}

.chat-list .chat-message {
  -webkit-border-radius: 50px;
  -moz-border-radius: 50px;
  border-radius: 50px;
  background: #5a9aee;
  display: inline-block;
  padding: 10px 20px;
  position: relative;
}

.chat-list .chat-message p {
}

.chat-list .chat-message:before {
  content: "";
  position: absolute;
  top: 15px;
  width: 0;
  height: 0;
}

.chat-list .chat-message h5 {
  margin: 0 0 5px 0;
  font-weight: 600;
  line-height: 100%;
  font-size: 0.9rem;
}

.chat-list .chat-message p {
  line-height: 18px;
  margin: 0;
  padding: 0;
  white-space: pre-line;
  text-align: left;
  padding-top: 10px;
  padding-bottom: 10px;
}

.chat-list .chat-body {
  margin-left: 20px;
  float: left;
  width: 70%;
}

.chat-list .in .chat-message:before {
  left: -12px;
  border-bottom: 20px solid transparent;
  border-right: 20px solid #5a9aee;
}

.chat-list .out .chat-img {
  float: right;
}

.chat-list .out .chat-body {
  float: right;
  margin-right: 20px;
  text-align: right;
}

.chat-list .out .chat-message {
  background: #e45a23;
}

.chat-list .out .chat-message:before {
  right: -12px;
  border-bottom: 20px solid transparent;
  border-left: 20px solid #e45a23;
}

.card .card-header:first-child {
  -webkit-border-radius: 0.3rem 0.3rem 0 0;
  -moz-border-radius: 0.3rem 0.3rem 0 0;
  border-radius: 0.3rem 0.3rem 0 0;
} */
/*
.border-left-primary {
  border-left: 0.25rem solid #4e73df !important;
}
.border-left-danger {
  border-left: 0.25rem solid #f70606 !important;
}
.text-gray-300 {
  color: #dddfeb !important;
}
.fa,
.fas {
  font-weight: 900;
}
.fa,
.far,
.fas {
  font-family: "Font Awesome 5 Free";
}
.fa-2x {
  font-size: 2em;
}
.fa,
.fab,
.fad,
.fal,
.far,
.fas {
  -moz-osx-font-smoothing: grayscale;
  -webkit-font-smoothing: antialiased;
  display: inline-block;
  font-style: normal;
  font-variant: normal;
  text-rendering: auto;
  line-height: 1;
}
*,
::after,
::before {
  box-sizing: border-box;
} */
.rowinfo {
  padding-top: 0px !important;
  padding-bottom: 10px !important;
}

.header-card-info {
  /* font-size: 0.7em; */
  /* font-size: 2vh; */
  padding: 0.75rem 0 !important;
}
</style>
