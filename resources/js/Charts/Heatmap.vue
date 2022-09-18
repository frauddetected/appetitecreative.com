<template>
    <apexchart type="heatmap" height="100%" width="100%" :options="chartOptions" :series="series"></apexchart>
</template>

<script>
export default {
    props: ['data','width'],
    mounted(){

    },
    data(){
        return{
            series: [
                {
                    name: 'Mon',
                    data: this.data['Monday'].map(item => {
                        return [item.hour, item.count]
                    })
                },
                {
                    name: 'Tue',
                    data: this.data['Tuesday'].map(item => {
                        return [item.hour, item.count]
                    })
                },
                {
                    name: 'Wed',
                    data: this.data['Wednesday'].map(item => {
                        return [item.hour, item.count]
                    })
                },
                {
                    name: 'Thu',
                    data: this.data['Thursday'].map(item => {
                        return [item.hour, item.count]
                    })
                },
                {
                    name: 'Fri',
                    data: this.data['Friday'].map(item => {
                        return [item.hour, item.count]
                    })
                },
                {
                    name: 'Sat',
                    data: this.data['Saturday'].map(item => {
                        return [item.hour, item.count]
                    })
                },
                {
                    name: 'Sun',
                    data: this.data['Sunday'].map(item => {
                        return [item.hour, item.count]
                    })
                }
          ],
          chartOptions: {
            plotOptions: {
              heatmap: {
                shadeIntensity: 0,
                radius: 0,
                useFillColorAsStroke: true
              }
            },
            colors: ["#ffaa44"],
            dataLabels: {
              enabled: false
            },
            stroke: {
              width: 1
            },
            legend: {
                show: false
            },
             chart: {
                 toolbar: {
                     show: false
                 },
                 
             },
        xaxis: {
          type: 'category',
          labels: {
            show: false
          }
        },
            title: {
              show: false
            },
          },

        }
    }
}
</script>