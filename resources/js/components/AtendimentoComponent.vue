<template>
  <b-container>
    <b-row class="rowinfo">
      <b-col cols="6" sm="6" md="3">
        <b-card no-body border-variant="primary" class="text-center" text-variant="primary">
          <b-card-header
            header-bg-variant="primary"
            header-text-variant="white"
            class="header-card-info"
          >
            <i class="fas fa-user-plus"></i> Atendidas
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
            <i class="fas fa-user-plus"></i> Na Fila
          </b-card-header>
          <b-card-body>
            <span style="font-size: 22px;font-weight: bold;">{{ clientes_nafila }}</span>
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
            <i class="fas fa-comments"></i>
            {{ titulo_atendimento }}
          </b-card-header>
          <div id="messages_content" ref="msgcontent">
            <ul class="chat-list">
              <template v-for="msg in mensagens">
                <li class="in" v-if="msg.direcao == 'in'" v-bind:key="msg.id">
                  <div class="chat-img">
                    <img alt="Avtar" :src="contato.photo_uri" />
                  </div>
                  <div class="chat-body">
                    <div class="chat-message">
                      <!-- <h5>{{ msg.name }}</h5> -->
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
                      <!-- <h5>{{ msg.name }}</h5> -->
                      <p>{{ msg.content }}</p>
                    </div>
                  </div>
                </li>
              </template>
            </ul>
          </div>
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

          <b-row>
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
          </b-row>
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
  props: ["titulo", "socket_host", "socket_port", "usuario_id"],
  data() {
    return {
      message: "",
      shouldScroll: false,
      clientes_nafila: 12,
      nomecliente: null,
      em_atendimento: false,
      titulo_atendimento: "Disponível",
      mensagens: [],
      atendimento: null,
      contato: null,
      qtde_atendimentos_hoje: 0,
      btn_receber: false
    };
  },
  methods: {
    encerrarAtendimento: function() {
      alert("*** NAO IMPLEMENTADO ***");
    },

    refresh_dados: function() {
      var _this = this;
      _this.sio.emit(
        "atendimento_usuario",
        { usuario_id: _this.usuario_id },
        async data => {
          await _this.$nextTick();
          _this.atendimento = data.atendimento;
          _this.mensagens = data.mensagens;
          _this.contato = data.contato;
        }
      );
      _this.sio.emit("atualizar_status_fila", {});
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
      _this.scrollToBottom();

      setTimeout(async function() {
        await _this.$nextTick();
        _this.scrollToBottom();
        console.log("Rolando");
      }, 1200);
    },
    sendMessage: async function() {
      var _this = this;
      if (_this.message) {
        var msg = {
          type: "text/plain",
          content: _this.message,
          to: _this.atendimento.remote_id,
          atendimento_id: _this.atendimento.id,
          direcao: "out"
        };

        _this.sio.emit("send_message", msg, data => {
          if (data.status == "OK") {
            _this.mensagens.push(data.msg);
            _this.message = "";
          }
        });

        _this.shouldScroll =
          _this.$refs.msgcontent.scrollTop +
            _this.$refs.msgcontent.clientHeight ===
          _this.$refs.msgcontent.scrollHeight;
        if (!_this.shouldScroll) {
          await _this.$nextTick();
          _this.scrollToBottom();
          setTimeout(function() {
            _this.scrollToBottom();
          }, 1000);
        }
      }
    },
    scrollToBottom: function() {
      console.log("scrollTop", this.$refs.msgcontent.scrollTop);
      console.log("clientHeight", this.$refs.msgcontent.clientHeight);
      console.log("scrollHeight", this.$refs.msgcontent.scrollHeight);
      this.$refs.msgcontent.scrollTop =
        this.$refs.msgcontent.scrollHeight + 120;
    }
  },

  computed: {
    fila_qtd: function() {
      var _this = this;
      var x = 0;
      Object.keys(_this.fila_status).forEach(key => {
        x += parseInt(_this.fila_status[key].qtde);
      });
      _this.btn_receber = x > 0 ? true : false;
      return x;
    },
    btn_receber_cliente_disabled: function() {
      if (this.em_atendimento) return true;
      return !this.btn_receber;
    }
  },

  mounted() {
    var _this = this;
    setTimeout(async function() {
      await _this.$nextTick();
      _this.scrollToBottom();
      //   console.log("Rolando");
    }, 1200);

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
  }
};
</script>
<style scoped>
#messages_content {
  /* height: 400px; */
  height: calc(50vh - 20px);
  overflow-y: scroll;
}

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
}
/* body {
  height: 100%;
} */
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
}
.rowinfo {
  padding-top: 10px !important;
  padding-bottom: 10px !important;
}

.header-card-info {
  /* font-size: 0.7em; */
  /* font-size: 2vh; */
  padding: 0.75rem 0 !important;
}
</style>
