<template>
  <div id="chart">
    <apexchart
      ref="chart"
      type="bar"
      width="400"
      height="350"
      :options="chartOptions"
      :series="series"
    ></apexchart>
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
      this.series[0].data = data.series;
      this.$refs.chart.updateOptions({
        labels: data.labels,
        xaxis: { categories: data.labels }
      });
    }
  },
  data() {
    return {
      series: [
        {
          data: []
        }
      ],
      chartOptions: {
        chart: {
          type: "bar",
          height: 350,
          width: 400
        },
        title: {
          text: this.title,
          align: "left"
        },
        plotOptions: {
          bar: {
            horizontal: true
          }
        },
        legend: {
          show: false
        },
        dataLabels: {
          enabled: false
        },
        noData: {
          text: "Nenhum Registro"
        },
        xaxis: {
          categories: []
        }
      }
    };
  }
};
</script>
<style scoped>
#chart {
  text-align: center;
}
</style>
