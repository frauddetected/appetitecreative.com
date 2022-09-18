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

            <div class="bg-white p-6 rounded-sm mb-6" v-if="project.ends_at">
                <vue-countdown :time="countdown" v-slot="{ days, hours, minutes, seconds }">
                    <div class="flex w-full text-lg justify-between">
                        Time left before project end
                        <span>{{ days }} days, {{ hours }} hours, {{ minutes }} minutes, {{ seconds }} seconds</span>
                    </div>
                </vue-countdown>
            </div>

            <div class="flex">
                <div class="w-6/12 p-6 bg-white rounded-sm mr-3">

                  <div class="flex w-full mb-8">
                      <div class="w-1/4 text-center" v-tippy="{ content: 'Unique Amount of Scans' }">
                          <h2 class="uppercase">Scans</h2>
                          <h1 class="text-4xl ml-auto font-bold">
                              {{ nFormatter(stats.scans.count, 1) }}
                          </h1>
                      </div>
                      <div class="w-1/4 text-center" v-tippy="{ content: 'Unique Sessions' }">
                          <h2 class="uppercase">Users</h2>
                          <h1 class="text-4xl ml-auto font-bold">
                              {{ nFormatter(stats.users.count, 1) }}
                          </h1>
                      </div>
                      <div class="w-1/4 text-center" v-tippy="{ content: 'Avg Number of Scans Per User' }">
                          <h2 class="uppercase">Scans / User</h2>
                          <h1 class="text-4xl ml-auto font-bold">
                              {{ nFormatter(stats.scans.count/stats.users.count, 1) }}
                          </h1>
                      </div>
                      <div class="w-1/4 text-center" v-tippy="{ content: 'Participants who registered' }">
                          <h2 class="uppercase">Registrations</h2>
                          <h1 class="text-4xl ml-auto font-bold">
                              {{ nFormatter(stats.participants.count, 1) }}
                          </h1>
                      </div>
                  </div>
                  <div>
                      <line-chart height="240px" :library="{ interaction: { mode: 'index' }, animation: { duration: 3000 } }" :dataset="{pointRadius: 0}" :legend="false" :data="charts.scans_scores" :colors="['#00bcf2','#8378de']"></line-chart>
                  </div>
                    
                </div>
                <div class="w-6/12 ml-3 bg-white rounded-sm flex flex-wrap items-start relative">

                        <div class="w-full">

                            <h2 class="flex w-full border-b p-4">
                                Scans by 
                                <div class="filters-dropdown">
                                    <span>{{ filters.scans }}</span>
                                    <aside>
                                        <ul>
                                            <li>Package</li>
                                            <!--
                                            <li>
                                                Country
                                                <ul>
                                                    <li>Saudi Arabia</li>
                                                    <li>Kuwait</li>
                                                </ul>
                                            </li>
                                            -->
                                        </ul>
                                    </aside>
                                </div>
                            </h2>

                        </div>

                        <div class="p-6 flex w-full">
                        <div class="w-7/12">
                            <geo-chart class="w-full pr-12" :library="{ region: 145, datalessRegionColor: '#f3f2f1'}" :data="charts.scans_countries"></geo-chart>
                        </div>

                        <section v-if="filters.scans=='Package'" class="flex-table !w-5/12">
                            <div class="flex-table-header">
                                <div class="col">Top</div>
                                <div class="col text-right">Scans</div>
                            </div>
                            <div
                                v-tippy="{ content: `<div class='p-2'>${top.title} <br> ${country(top.country)} <strong class='ml-8'>${top.views} (${calcPerc(top.views, stats.scans.count)})</strong></div>`, followCursor: true }"
                                v-for="top in stats.scans.top.slice(0,pagination.scans)" 
                                class="flex-table-row">
                                    <div class="col font-bold">{{ top.title }}</div>
                                    <div class="col text-right">{{ top.views }}</div>
                                    <div :style="{ width: calcPerc(top.views, stats.scans.count) }" class="duration-300 left-0 bottom-0 border-b-2 absolute border-ms-cyan-10"></div>
                            </div>
                        </section>

                        <section v-if="filters.scans=='Country'" class="flex-table !w-5/12">
                            <div class="flex-table-header">
                                <div class="col">Top</div>
                                <div class="col text-right">Scans</div>
                            </div>
                            <div
                                v-tippy="{ content: `<div class='p-2'>${top.title} <br> ${country(top.country)} <strong class='ml-8'>${top.scans} (${calcPerc(top.scans, stats.scans.count)})</strong></div>`, followCursor: true }"
                                v-for="top in stats.scans.top_country.slice(0,pagination.scans)" 
                                class="flex-table-row">
                                    <div class="col font-bold">{{ top.title }}</div>
                                    <div class="col text-right">{{ top.scans }}</div>
                                    <div :style="{ width: calcPerc(top.scans, stats.scans.count) }" class="duration-300 left-0 bottom-0 border-b-2 absolute border-ms-cyan-10"></div>
                            </div>
                        </section>
                        
                        <section class="absolute bottom-4 right-4">
                            <button 
                            v-if="stats.scans.top.length > pagination.scans" 
                            @click="pagination.scans = 50"
                            class="border border-ms-gray-110 hover:bg-ms-gray-20 rounded-sm px-4 py-1">View more</button>
                        </section>
                        </div>

                </div>
            </div>

        <div class="grid grid-cols-3 gap-6 mt-6">

            <div class="bg-white rounded-sm">

                <div class="w-full flex flex-col">

                    <h2 class="flex w-full p-4 border-b mb-4">
                        Slot Machine by 
                        <div class="filters-dropdown">
                            <span>Spins</span>
                            <aside>
                                <ul>
                                    <li :class="{ 'font-bold' : filters.games == 'Spins' }" @click="filters.games='Spins'">Spins</li>
                                </ul>
                            </aside>
                        </div>
                    </h2>

                    <div class="flex w-full mt-4 justify-center">
                        <div class="w-1/3 text-center" v-tippy="{ content: `<div class='p-2 w-40 text-center'>Total number of games started playing<div>`, followCursor: false }">
                            <h2 class="uppercase text-xs">Spins</h2>
                            <h1 class="text-2xl ml-auto font-bold">
                                {{ nFormatter(stats.game_start.count, 1) }}
                            </h1>
                        </div>
                        <div class="w-1/3 text-center" v-tippy="{ content: `<div class='p-2 w-40 text-center'>Total number of games started playing<div>`, followCursor: false }">
                            <h2 class="uppercase text-xs">Wins</h2>
                            <h1 class="text-2xl ml-auto font-bold">
                                {{ nFormatter(stats.game_end.count, 1) }}
                            </h1>
                        </div>
                    </div>

                </div>

                <section v-if="filters.games == 'Start'" class="flex-table p-6 mt-8">
                    <div class="flex-table-header">
                        <div class="col">Name</div>
                        <div class="col text-right">Start</div>
                    </div>
                    <div
                        v-tippy="{ content: `<div class='p-2'>${top.name} <strong class='ml-2'>${top.views} (${calcPerc(top.views, stats.game_start.count)})</strong></div>`, followCursor: true }"
                        v-for="top in stats.game_start.top" 
                        class="flex-table-row">
                            <div class="col font-bold">{{ top.name }}</div>
                            <div class="col text-right">{{ top.views }}</div>
                            <div :style="{ width: calcPerc(top.views, stats.game_start.count) }" class="duration-300 left-0 bottom-0 border-b-2 absolute border-ms-magenta-10"></div>
                    </div>
                </section>

            </div>
         
            <div class="bg-white rounded-sm">
                
                <div class="w-full border-b p-4 mb-4">
                    <h2>Social by <span class="border-b font-bold border-black border-dashed">Clicks</span></h2>
                </div>

                <div class="flex w-full mt-4 justify-center">
                    <div class="w-1/3 text-center" v-tippy="{ content: `<div class='p-2 w-40 text-center'>Total number of games started playing<div>`, followCursor: false }">
                        <h2 class="uppercase text-xs">Total Clicks</h2>
                        <h1 class="text-2xl ml-auto font-bold">
                            {{ nFormatter(stats.share.count, 1) }}
                        </h1>
                    </div>
                </div>

                <section class="flex-table p-6 mt-8">
                    <div class="flex-table-header">
                        <div class="col">Method</div>
                        <div class="col text-right">Shares</div>
                    </div>
                    <div
                        v-tippy="{ content: `<div class='p-2'>${top.method} <strong class='ml-2'>${top.views} (${calcPerc(top.views, stats.share.count)})</strong></div>`, followCursor: true }"
                        v-for="top in stats.share.top" 
                        class="flex-table-row">
                            <div class="col font-bold">{{ top.method }}</div>
                            <div class="col text-right">{{ top.views }}</div>
                            <div :style="{ width: calcPerc(top.views, stats.share.count) }" class="duration-300 left-0 bottom-0 border-b-2 absolute border-ms-cyan-20"></div>
                    </div>
                </section>

            </div>
            </div>
      

        </div>
        
        <SidePanel :show="showPanel" @close="showPanel=false">
            
            <h1 class="text-xl font-bold">Customize Data</h1>

            <div class="py-5">
                Additional options to filter your data will be available shortly.
            </div>

            <footer class="flex justify-end mt-auto">
                <button  class="border border-ms-gray-160 px-4 py-1 hover:bg-ms-gray-40 ml-2 font-bold">Apply</button>
            </footer>

        </SidePanel>

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
