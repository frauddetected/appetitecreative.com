<template>
    <app-layout title="Dashboard">
        
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
            <div class="w-64 mr-auto ml-4">
                <DatePicker class="w-full" v-model="range" color="teal" is-range mode="date" :masks="masks">
                <template v-slot="{ inputValue, inputEvents, isDragging }">
                    <div class="flex flex-row items-center">
                      <div class="relative flex-grow">
                        <svg
                          class="text-ms-gray-160 w-4 h-full mx-2 absolute pointer-events-none"
                          fill="none"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          viewBox="0 0 24 24"
                          stroke="currentColor"
                        >
                          <path
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                          ></path>
                        </svg>
                        <input
                          class="pl-8 py-1 w-[6.85rem] focus:outline-none"
                          :class="isDragging ? 'text-ms-gray-60' : 'text-ms-gray-160'"
                          :value="inputValue.start"
                          v-on="inputEvents.start"
                        />
                      </div>
                      <span class="mr-3">
                        <svg
                          class="w-4 h-4 stroke-current text-ms-gray-120"
                          viewBox="0 0 24 24"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3"
                          />
                        </svg>
                      </span>
                      <div class="relative flex-grow">
                        <input
                          class="py-1 w-[6.4rem] focus:outline-none"
                          :class="isDragging ? 'text-ms-gray-60' : 'text-ms-gray-160'"
                          :value="inputValue.end"
                          v-on="inputEvents.end"
                        />
                      </div>
                    </div>
                  </template>
                </DatePicker>
            </div>
            <nav class="flex items-center">                
                <button @click="showPanel=true" class="hover:bg-ms-gray-20 text-ms-gray-160 p-3 flex items-center">
                    <i class="ms-Icon ms-Icon--FilterSettings mr-2"></i> Filters
                </button>
            </nav>
        </template>

        <div class="p-6 bg-ms-gray-20">

            <div class="flex">
                <div class="w-6/12 p-6 bg-white rounded-sm mr-3">

                  <div class="flex w-full mb-8">
                      <div class="w-1/4 text-center" v-tippy="{ content: 'Unique Amount of Scans' }">
                          <h2 class="uppercase">Scans</h2>
                          <h1 class="text-4xl ml-auto font-bold">
                              {{ nFormatter(stats.scans.count, 1) }}
                          </h1>
                      </div>
                  </div>
                  <div>
                      <line-chart height="240px" :library="{ interaction: { mode: 'index' }, animation: { duration: 3000 } }" :dataset="{pointRadius: 0}" :legend="false" :data="charts.scans" :colors="['#00bcf2','#8378de']"></line-chart>
                  </div>
                    
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout.vue'
    import SidePanel from '@/Components/SidePanel.vue'
    import VueCountdown from '@chenfengyuan/vue-countdown'
    import { DatePicker } from 'v-calendar'
    import { useToast } from "vue-toastification";

    export default {
        
        props: ['project','period','stats','charts'],

        setup(){
            const toast = useToast();
            return { toast }
        },

        components: {
            AppLayout,
            VueCountdown,
            DatePicker,
            SidePanel
        },

        data(){    
            
            const now = new Date();
            const ends_at = new Date(this.project.ends_at);

            var d = new Date();
            d.setDate(d.getDate()-7);

            return{
                countdown: ends_at - now,
                range: {
                    start: this.period.start,
                    end: this.period.end
                },
                masks: {
                    input: 'DD-MM-YYYY',
                },
                showPanel: false,
                filters: {
                    scans: 'Package',
                    games: 'Start'
                },
                pagination: {
                    scans: 6
                }
            }
        },
        watch: {
            range(v){
                const start = parseInt((new Date(v.start).getTime() / 1000).toFixed(0))
                const end = parseInt((new Date(v.end).getTime() / 1000).toFixed(0))
                this.$inertia.visit(`/?period=${start}.${end}`)
                this.toast('Period updated')
            }
        },
        methods: {
            fancyTimeFormat(duration)
            {   
                // Hours, minutes and seconds
                var hrs = ~~(duration / 3600);
                var mins = ~~((duration % 3600) / 60);
                var secs = ~~duration % 60;

                // Output like "1:01" or "4:03:59" or "123:03:59"
                var ret = "";

                if (hrs > 0) {
                    ret += "" + hrs + ":" + (mins < 10 ? "0" : "");
                }

                ret += "" + mins + "m " + (secs < 10 ? "0" : "");
                ret += "" + secs + "s";
                return ret;
            },
            nFormatter(num, digits) {
                const lookup = [
                    { value: 1, symbol: "" },
                    { value: 1e3, symbol: "k" },
                    { value: 1e6, symbol: "M" },
                    { value: 1e9, symbol: "G" },
                    { value: 1e12, symbol: "T" },
                    { value: 1e15, symbol: "P" },
                    { value: 1e18, symbol: "E" }
                ];
                const rx = /\.0+$|(\.[0-9]*[1-9])0+$/;
                var item = lookup.slice().reverse().find(function(item) {
                    return num >= item.value;
                });
                return item ? (num / item.value).toFixed(digits).replace(rx, "$1") + item.symbol : "0";
            },
            country(c){
                const countries = {
                  KW: 'Kuwait',
                  SA: 'Saudi Arabia',
                }
                return countries[c];
            },
            calcPerc(num, total){
              return Math.round(num * 100 / total) + '%';
            },
            unCamelCase(str){
                return str
                    // insert a space between lower & upper
                    .replace(/([a-z])([A-Z])/g, '$1 $2')
                    // insert a space between lower & number
                    .replace(/([a-z])([0-9])/g, '$1 $2')
                    // space before last upper in a sequence followed by lower
                    .replace(/\b([A-Z]+)([A-Z])([a-z])/, '$1 $2$3')
                    // uppercase the first character
                    .replace(/^./, function(str){ return str.toUpperCase(); })
            }
        },
        mounted(){
            
        }
    }
</script>