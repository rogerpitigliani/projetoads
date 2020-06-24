<template>
  <div class="card">
    <div class="card-body">
      <div class="row">
        <div class="col mt-0">
          <h5 class="card-title">{{ label }}</h5>
        </div>

        <div class="col-auto">
          <div class="avatar">
            <div class="avatar-title rounded-circle bg-primary-dark">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="feather feather-truck align-middle"
              >
                <rect x="1" y="3" width="15" height="13" />
                <polygon points="16 8 20 8 23 11 23 16 16 16 16 8" />
                <circle cx="5.5" cy="18.5" r="2.5" />
                <circle cx="18.5" cy="18.5" r="2.5" />
              </svg>
            </div>
          </div>
        </div>
      </div>
      <h1 class="display-5 mt-1 mb-3">{{ value }}</h1>
      <div class="mb-0">
        <span class="text-danger">{{ description }}</span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["value", "label", "description", "color", "variant", "icon"],
  computed: {
    style: function() {
      switch (this.variant) {
        case "primary":
          return "color: rgb(255, 255, 255); background: linear-gradient(to right, rgb(52, 181, 229), rgb(46, 132, 224)); box-shadow: rgba(52, 56, 85, 0.25) 0px 2px 3px 0px;";
          break;
        case "warning":
          return "color: rgb(255, 255, 255); background: linear-gradient(to right, rgb(102, 229, 181), rgb(41, 224, 105)); box-shadow: rgba(52, 56, 85, 0.25) 0px 2px 3px 0px;";
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
.card {
  position: relative;
  display: flex;
  flex-direction: column;
  min-width: 0;
  word-wrap: break-word;
  background-color: #fff;
  background-clip: border-box;
  border: 0 solid rgba(0, 0, 0, 0.125);
  border-radius: 0.2rem;
}

.avatar {
  width: 40px;
  height: 40px;
}

.bg-primary-dark {
  background-color: #2469ce !important;
}
.avatar-title {
  display: flex;
  width: 100%;
  height: 100%;
  align-items: center;
  justify-content: center;
  color: #fff;
}
.rounded-circle {
  border-radius: 50% !important;
}
</style>
