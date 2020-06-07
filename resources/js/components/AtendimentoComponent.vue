<template>
  <b-container>
    <b-row class="rowinfo">
      <b-col>
        <div class="card border-left-primary shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div
                  class="text-xs font-weight-bold text-primary text-uppercase mb-1"
                >Atendidos (Hoje)</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">12</div>
              </div>
              <div class="col-auto">
                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </b-col>
      <b-col>
        <div class="card border-left-danger shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Na Fila</div>
                <div class="h5 mb-0 font-weight-bold text-danger">{{clientes_nafila}}</div>
              </div>
              <div class="col-auto">
                <i class="fas fa-bell fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </b-col>

      <b-col cols="6">
        <div class="h-100 text-right">
          <b-button class="h-100" variant="warning" size="lg" @click="recebeAtendimento">
            <i class="fas fa-user-plus"></i> Receber Cliente
          </b-button>

          <b-button
            :disabled="!ematendimento"
            class="h-100"
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
                <li class="in" v-if="msg.type == 'in'">
                  <div class="chat-img">
                    <img alt="Avtar" :src="msg.image" />
                  </div>
                  <div class="chat-body">
                    <div class="chat-message">
                      <!-- <h5>{{ msg.name }}</h5> -->
                      <p>{{ msg.message }}</p>
                    </div>
                  </div>
                </li>
                <li class="out" v-if="msg.type == 'out'">
                  <div class="chat-img">
                    <img alt="Avtar" :src="msg.image" />
                  </div>
                  <div class="chat-body">
                    <div class="chat-message">
                      <!-- <h5>{{ msg.name }}</h5> -->
                      <p>{{ msg.message }}</p>
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
      ematendimento: false,
      titulo_atendimento: "Disponível",
      mensagens: [],
      mensagens2: [
        {
          id: 1,
          name: "Pedro",
          message: "Olá, tudo bem... Gostaria de ....lalalalal",
          image: "https://bootdey.com/img/Content/avatar/avatar1.png",
          type: "in",
          channel: "whatsapp"
        },
        {
          id: 2,
          name: "Roger",
          message:
            "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using",
          image: "https://bootdey.com/img/Content/avatar/avatar6.png",
          type: "out",
          channel: "whatsapp"
        },
        {
          id: 3,
          name: "Pedro",
          message:
            "There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. ",
          image: "https://bootdey.com/img/Content/avatar/avatar1.png",
          type: "in",
          channel: "whatsapp"
        },
        {
          id: 4,
          name: "Roger",
          message:
            "Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making ",
          image: "https://bootdey.com/img/Content/avatar/avatar6.png",
          type: "out",
          channel: "whatsapp"
        },
        {
          id: 3,
          name: "Pedro",
          message:
            "There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. ",
          image: "https://bootdey.com/img/Content/avatar/avatar1.png",
          type: "in",
          channel: "whatsapp"
        },
        {
          id: 4,
          name: "Roger",
          message:
            "Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making ",
          image: "https://bootdey.com/img/Content/avatar/avatar6.png",
          type: "out",
          channel: "whatsapp"
        },
        {
          id: 3,
          name: "Pedro",
          message:
            "There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. ",
          image: "https://bootdey.com/img/Content/avatar/avatar1.png",
          type: "in",
          channel: "whatsapp"
        },
        {
          id: 4,
          name: "Roger",
          message:
            "Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making ",
          image: "https://bootdey.com/img/Content/avatar/avatar6.png",
          type: "out",
          channel: "whatsapp"
        }
      ]
    };
  },
  methods: {
    encerrarAtendimento: function() {
      alert("*** NAO IMPLEMENTADO ***");
    },

    recebeAtendimento: async function() {
      var _this = this;
      _this.mensagens = _this.mensagens2;
      _this.clientes_nafila--;
      _this.nomecliente = "Pedro";
      _this.ematendimento = true;
      _this.titulo_atendimento =
        _this.titulo + " Pedro (51) 98246-4536 - ** SUPORTE **";
      await _this.$nextTick();
      _this.scrollToBottom();

      setTimeout(async function() {
        await _this.$nextTick();
        _this.scrollToBottom();
        console.log("Rolando");
      }, 1200);
    },
    sendMessage: async function() {
      if (this.message) {
        this.mensagens.push({
          name: "Roger",
          message: this.message,
          image: "https://bootdey.com/img/Content/avatar/avatar6.png",
          type: "out",
          channel: "whatsapp"
        });
        this.message = "";

        this.shouldScroll =
          this.$refs.msgcontent.scrollTop +
            this.$refs.msgcontent.clientHeight ===
          this.$refs.msgcontent.scrollHeight;
        if (!this.shouldScroll) {
          await this.$nextTick();
          this.scrollToBottom();
          setTimeout(function() {
            this.scrollToBottom();
          }, 1000);
        }

        //alert(this.$refs.msgcontent.scrollHeight);
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

  mounted() {
    console.log("Component mounted.");
    var _this = this;
    setTimeout(async function() {
      await _this.$nextTick();
      _this.scrollToBottom();
      console.log("Rolando");
    }, 1200);

    _this.connect_io(
      _this.socket_host,
      _this.socket_port,
      "chat",
      _this.usuario_id
    );
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
body {
  height: 100%;
}
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
</style>
