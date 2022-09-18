<template>
    <div class="w-96" v-if="loading">
        <Loader type="pie" />
    </div>
    <div class="w-full" else>
        <canvas id="canvas"></canvas>
    </div>
</template>

<script>
import Loader from "../Components/ContentLoader"

export default {
    props: ['period'],
    components: { Loader },
    data(){
      return{
          loading: true,
          data: []
      }
    },
    mounted(){
        axios.get('/charts/cities?period=' + this.period).then(r => {
            this.loading = false
            this.data = r.data.cities.rows
        })
    },
    watch: {
      loading(v){
          v == false ? this.generateMap() : null;
      }
    },
    methods: {
        generateMap(){

          let $this = this;
          
          fetch("https://raw.githubusercontent.com/deldersveld/topojson/master/countries/spain/spain-province.json")
          .then((r) => r.json())
          .then((esp) => {
            
            var geoData = ChartGeo.topojson.feature(esp, esp.objects.ESP_adm2).features;

            for (let i in geoData) {
              for( let x in $this.data ) {
                  if( $this.data[x][1] == geoData[i].properties.NAME_1.normalize("NFD").replace(/[\u0300-\u036f]/g, "") || $this.data[x][1] == geoData[i].properties.NAME_2.normalize("NFD").replace(/[\u0300-\u036f]/g, "") || $this.data[x][2] == geoData[i].properties.NAME_1.normalize("NFD").replace(/[\u0300-\u036f]/g, "") || $this.data[x][2] == geoData[i].properties.NAME_2.normalize("NFD").replace(/[\u0300-\u036f]/g, "") ) {
                    geoData[i].properties.visitors = parseInt($this.data[x][3]);
                    break;
                  }
              }
            }

           const chart = new Chart(document.getElementById("canvas").getContext("2d"), {
              type: 'choropleth',
              data: {
                labels: geoData.map((d) => d.properties.NAME_2),
                datasets: [{
                  outline: geoData,
                  data: geoData.map((d) => ({ 
                    feature: d, 
                    value: d.properties.visitors 
                  })),
                }]
              },
              options: {
                plugins: {
                  legend: {
                    display: false
                  },
                  
                },
                scales: {
                  xy: {
                    projection: 'equalEarth'  
                  },
                  color: {
                    quantize: 8,
                    interpolate: 'oranges',
                    legend: {
                      
                    },
                  }
                },
              }    
            });        
        });

      }
    }
}
</script>