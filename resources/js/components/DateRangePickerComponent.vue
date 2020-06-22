<template>
  <date-range-picker
    :date-range="dateRange"
    :startDate="startDate"
    :endDate="endDate"
    @update="console.log(value)"
    :locale-data="locale"
    :opens="opens"
  >
    <!--Optional scope for the input displaying the dates -->
    <div slot="input" slot-scope="picker">...</div>
  </date-range-picker>
</template>
<script>
import DateRangePicker from "vue2-daterange-picker";
//you need to import the CSS manually (in case you want to override it)

export default {
  components: { DateRangePicker },
  data() {
    return {
      dateRange: {
        startDate: this.moment()
          .startOf("month")
          .format("YYYY-MM-DD"),
        endDate: this.moment().format("YYYY-MM-DD")
      },
      opens: "center", //which way the picker opens, default "center", can be "left"/"right"
      locale: {
        direction: "ltr", //direction of text
        format: "dd/MM/yyyy", //fomart of the dates displayed
        separator: " - ", //separator between the two ranges
        applyLabel: "Apply",
        cancelLabel: "Cancel",
        weekLabel: "W",
        customRangeLabel: "Custom Range",
        daysOfWeek: this.moment.weekdaysMin(), //array of days - see moment documenations for details
        monthNames: this.moment.monthsShort(), //array of month names - see moment documenations for details
        firstDay: 1, //ISO first day of week - see moment documenations for details
        showWeekNumbers: true //show week numbers on each row of the calendar
      },
      ranges: {
        //default value for ranges object (if you set this to false ranges will no be rendered)
        Today: [this.moment(), this.moment()],
        Yesterday: [
          this.moment().subtract(1, "days"),
          this.moment().subtract(1, "days")
        ],
        "This month": [
          this.moment().startOf("month"),
          this.moment().endOf("month")
        ],
        "This year": [
          this.moment().startOf("year"),
          this.moment().endOf("year")
        ],
        "Last week": [
          this.moment()
            .subtract(1, "week")
            .startOf("week"),
          this.moment()
            .subtract(1, "week")
            .endOf("week")
        ],
        "Last month": [
          this.moment()
            .subtract(1, "month")
            .startOf("month"),
          this.moment()
            .subtract(1, "month")
            .endOf("month")
        ]
      }
    };
  }
};
</script>
