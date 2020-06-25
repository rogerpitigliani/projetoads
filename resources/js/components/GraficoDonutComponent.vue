<template>
  <div id="chart">
    <apexchart ref="chart" type="donut" width="500" :options="chartOptions" :series="series"></apexchart>
  </div>
</template>

<script>
// import ApexCharts from "apexcharts";
export default {
  props: ["title"],

  mounted() {
    console.log("Component mounted.");
  },
  methods: {
    updateChart: function(data) {
      this.series = data.series;
      this.$refs.chart.updateOptions({ labels: data.labels });
    }
  },
  data() {
    return {
      series: [],
      chartOptions: {
        chart: {
          width: "100%",
          type: "pie"
        },
        title: {
          text: this.title,
          align: "left"
        },
        legend: {
          show: true,
          position: "bottom"
        },
        noData: {
          text: "Nenhum Registro"
        },
        // labels: ["1", "2", "3"],
        labels: [],
        responsive: [
          {
            breakpoint: 480,
            options: {
              chart: {
                width: 200
              },
              legend: {
                position: "bottom"
              }
            }
          }
        ]
      }
    };
  }
};
</script>
<style scoped>
#chart {
  width: 100%;
  text-align: center;
  margin-bottom: 10px;
}
</style>
