<template>
  <div id="chart">
    <apexchart type="donut" width="500" :options="chartOptions" :series="series"></apexchart>
  </div>
</template>

<script>
// import ApexCharts from "apexcharts";
export default {
  mounted() {
    console.log("Component mounted.");
  },
  data() {
    return {
      series: [44, 55, 13, 43, 22],
      chartOptions: {
        chart: {
          width: "100%",
          type: "pie"
        },
        title: {
          text: "Graph Donut",
          align: "left"
        },
        legend: {
          show: true,
          position: "bottom"
        },
        labels: ["Team A", "Team B", "Team C", "Team D", "Team E"],
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
