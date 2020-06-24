<template>
  <div id="chart">
    <apexchart type="bar" width="400" height="350" :options="chartOptions" :series="series"></apexchart>
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
      series: [
        {
          data: [400, 430, 448, 470, 540, 580, 690, 1100, 1200, 1380]
        }
      ],
      chartOptions: {
        chart: {
          type: "bar",
          height: 350,
          width: 400
        },
        title: {
          text: "Graph Barras",
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
        xaxis: {
          categories: [
            "South Korea",
            "Canada",
            "United Kingdom",
            "Netherlands",
            "Italy",
            "France",
            "Japan",
            "United States",
            "China",
            "Germany"
          ]
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
