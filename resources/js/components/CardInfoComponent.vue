<template>
  <div class="flex xs12 sm4">
    <div class="va-card mb-4" :style="style">
      <!---->
      <!---->
      <!---->
      <div class="icone" style="position: absolute; float: right; top: 10px; right: 10px; ">
        <i v-if="icon" :class="iconeclass"></i>
      </div>
      <div class="va-card__body va-card__body--padding-top">
        <p class="display-2 mb-0" style="color: white;">{{value}}</p>
        <p>{{label}}</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["value", "label", "color", "variant", "icon"],
  computed: {
    iconeclass: function() {
      if (this.icon) {
        return `${this.icon} fa-2x`;
      }
    },
    style: function() {
      switch (this.variant) {
        case "primary":
          return "color: rgb(255, 255, 255); background: linear-gradient(to right, rgb(52, 181, 229), rgb(46, 132, 224)); box-shadow: rgba(52, 56, 85, 0.25) 0px 2px 3px 0px;";
          break;
        case "warning":
          return "color: rgb(255, 255, 255); background: linear-gradient(to right, rgb(222, 174, 71), rgb(212, 148, 30)); box-shadow: rgba(52, 56, 85, 0.25) 0px 2px 3px 0px;";
          break;
        case "danger":
          return "color: rgb(255, 255, 255); background: linear-gradient(to right, rgb(255, 118, 87), rgb(227, 74, 74)); box-shadow: rgba(52, 56, 85, 0.25) 0px 2px 3px 0px;";
          break;
        case "success":
          return "color: rgb(255, 255, 255); background: linear-gradient(to right, rgb(102, 229, 181), rgb(41, 224, 105)); box-shadow: rgba(52, 56, 85, 0.25) 0px 2px 3px 0px;";
          break;
        default:
          return "color: rgb(255, 255, 255); background: linear-gradient(to right, rgb(102, 229, 181), rgb(41, 224, 105)); box-shadow: rgba(52, 56, 85, 0.25) 0px 2px 3px 0px;";
      }
    }
  },
  mounted() {
    console.log("Card Info");
  }
};
</script>
<style scoped>
.va-card {
  margin-bottom: 0 !important;
}
.flex > * {
  -webkit-box-flex: 1;
  -ms-flex: 1 1 auto;
  flex: 1 1 auto;
}
.va-card {
  border-radius: 0.375rem;
  border: none;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
  word-wrap: break-word;
  background-color: #fff;
  position: relative;
  overflow: hidden;
}
.flex.sm4 {
  -ms-flex-preferred-size: 33.33333%;
  flex-basis: 33.33333%;
  -webkit-box-flex: 0;
  -ms-flex-positive: 0;
  flex-grow: 0;
  max-width: 100%;
  margin-bottom: 5px;
}
</style>
