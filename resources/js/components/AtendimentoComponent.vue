<template>
  <b-container>
    <b-row>
      <b-col>
        <b-card :header="titulo" header-bg-variant="primary" header-text-variant="white">OPA</b-card>
      </b-col>
    </b-row>
  </b-container>
</template>

<script>
export default {
  props: ["titulo"],
  mounted() {
    console.log("Component mounted.");
  }
};
</script>
