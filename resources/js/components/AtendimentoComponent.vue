<template>
  <b-container>
    <b-row class="h-100">
      <b-col class="h-100">
        <b-card
          :header="titulo + ' - Pedro (51) 98273-2122'"
          header-bg-variant="primary"
          header-text-variant="white"
          class="h-100"
          no-body
        >
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
export default {
  props: ["titulo"],
  data() {
    return {
      message: "",
      shouldScroll: false,

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
      _this.mensagens = _this.mensagens2;
      await _this.$nextTick();
      _this.scrollToBottom();
      console.log("Rolando");
    }, 1200);
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
  background: #5a99ee;
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
  border-right: 20px solid #5a99ee;
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
  background: #fc6d4c;
}

.chat-list .out .chat-message:before {
  right: -12px;
  border-bottom: 20px solid transparent;
  border-left: 20px solid #fc6d4c;
}

.card .card-header:first-child {
  -webkit-border-radius: 0.3rem 0.3rem 0 0;
  -moz-border-radius: 0.3rem 0.3rem 0 0;
  border-radius: 0.3rem 0.3rem 0 0;
}
body {
  height: 100%;
}
</style>
