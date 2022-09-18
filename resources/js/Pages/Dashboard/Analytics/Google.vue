<template>
    <app-layout title="Analytics">
        
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Analytics
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
            </nav>
        </template>

        <transition name="fade" mode="out-in">
        <div v-if="!loading" class="p-6 min-h-screen bg-ms-gray-20">

            <div class="flex bg-white justify-between w-full mb-6">
                <p class="p-5 text-ms-orange-20 text-center font-bold">This section data is provided by Google Analytics.</p>
                <img class="h-16" src="https://developers.google.com/analytics/images/terms/logo_lockup_analytics_icon_vertical_black_2x.png" alt="">
            </div>

            <div class="flex">
                <div class="w-7/12 bg-white">
                    
                    <div class="p-5 border-b">
                        <h2 class="text-lg">Sessions</h2>
                    </div>
                    <div class="p-5">
                        <line-chart :legend="false" :dataset="{ pointRadius: 0 }" :library="{ interaction: { mode: 'index' }, animation: { duration: 3000 } }"  height="260px" :colors="['#00bcf2','#881798']" :data="visitedpages"></line-chart>
                    </div>

                </div>
                <div class="w-5/12 ml-4">
                    <ul class="grid grid-cols-2 gap-4">
                        <li class="bg-white p-7">
                            <h2 class="uppercase">Users <a v-tippy="{ content: `<div class='p-2 w-48 text-center'>In general, users are visitors who have initiated one session with your website or app within a specified period of time.<div>`, followCursor: false }" class="hover:text-yellow-400 duration-300 ml-2" target="_blank" href='https://www.hotjar.com/google-analytics/glossary/users/'><i class="ms-Icon ms-Icon--Info"></i></a></h2>
                            <h1 class="text-4xl ml-auto font-bold">
                                {{ nFormatter(analytics.stats['ga:users'],2) }}
                            </h1>
                        </li>
                        <li class="bg-white p-7">
                            <h2 class="uppercase">Sessions <a v-tippy="{ content: `<div class='p-2 w-48 text-center'>A session is a group of user interactions with your website that take place within a given time frame. For example a single session can contain multiple page views. A single user can open multiple sessions.<div>`, followCursor: false }" class="hover:text-yellow-400 duration-300 ml-2" target="_blank" href='https://www.hotjar.com/google-analytics/glossary/sessions/'><i class="ms-Icon ms-Icon--Info"></i></a></h2>
                            <h1 class="text-4xl font-bold">
                                {{ nFormatter(analytics.stats['ga:sessions'],2) }}
                            </h1>
                        </li>
                        <li class="bg-white p-7">
                            <h2 class="uppercase">Page / Screen Views <a v-tippy="{ content: `<div class='p-2 w-48 text-center'>A pageview (or pageview hit, page tracking hit) is an instance of a page being loaded (or reloaded) in a browser.<div>`, followCursor: false }" class="hover:text-yellow-400 duration-300 ml-2" target="_blank" href='https://support.google.com/analytics/answer/6086080?hl=en'><i class="ms-Icon ms-Icon--Info"></i></a></h2>
                            <h1 class="text-4xl font-bold">
                                {{ nFormatter(analytics.stats['ga:pageviews'],2) }}
                            </h1>
                        </li>
                        <li class="bg-white p-7">
                            <h2 class="uppercase">Bounce Rate <a v-tippy="{ content: `<div class='p-2 w-48 text-center'>A bounce (often called a single-page session) happens when a user lands on a website page and exits without triggering another request to the Google Analytics server.<div>`, followCursor: false }" class="hover:text-yellow-400 duration-300 ml-2" target="_blank" href='https://www.hotjar.com/google-analytics/glossary/bounces/'><i class="ms-Icon ms-Icon--Info"></i></a></h2>
                            <h1 class="text-4xl font-bold">
                                {{ parseFloat(analytics.stats['ga:bounceRate']).toFixed(2) }}%
                            </h1>
                        </li>
                        <li class="bg-white p-7">
                            <h2 class="uppercase">Avg Time <a v-tippy="{ content: `<div class='p-2 w-48 text-center'>A metric that measures the average length of sessions on your website.<div>`, followCursor: false }" class="hover:text-yellow-400 duration-300 ml-2" target="_blank" href='https://www.hotjar.com/google-analytics/glossary/session-duration/'><i class="ms-Icon ms-Icon--Info"></i></a></h2>
                            <h1 class="text-4xl font-bold">
                                {{ fancyTimeFormat(analytics.stats['ga:avgSessionDuration']) }}
                            </h1>
                        </li>
                        <li class="bg-white p-8">
                            
                        </li>
                    </ul>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4 mt-4">
                <div class="flex flex-col bg-white">

                    <h2 class="flex w-full border-b p-4">
                        Users by  
                        <div class="filters-dropdown">
                            <span>{{ filters.geo }}</span>
                            <aside>
                                <ul>
                                    <li :class="{ 'font-bold' : filters.geo == 'City' }" @click="filters.geo='City'">City</li>
                                    <li :class="{ 'font-bold' : filters.geo == 'Country' }" @click="filters.geo='Country'">Country</li>
                                </ul>
                            </aside>
                        </div>
                        <a :href="`/charts/cities?period=${fullUnixRange}&export=true`" class="hover:text-green-500 ml-auto">
                            <i class="ms-Icon ms-Icon--ExcelDocument"></i>
                        </a>
                    </h2>
                   
                   <section v-if="filters.geo=='City'" class="flex-table p-4">
                        <div class="flex-table-header">
                            <div class="col">City</div>
                            <div class="col text-right">Visitors</div>
                        </div>
                        <div
                            v-tippy="{ content: `<div class='p-2'>${city[1]} <br> ${city[0]} <strong class='ml-8'>${city[2]} (${calcPerc(city[2],analytics.cities.total)})</strong></div>`, followCursor: true }"
                            v-for="city in analytics.cities.rows.slice(0,14)" 
                            class="flex-table-row">
                                <div class="col font-bold">{{ city[1] }}</div>
                                <div class="col text-right">{{ city[2] }}</div>
                                <div :style="{ width: calcPerc(city[2],analytics.cities.total) }" class="duration-300 left-0 bottom-0 border-b-2 absolute border-ms-cyan-10"></div>
                        </div>
                    </section>

                   <section v-if="filters.geo=='Country'" class="flex-table p-4">
                        <div class="flex-table-header">
                            <div class="col">Country</div>
                            <div class="col text-right">Visitors</div>
                        </div>
                        <div
                            v-tippy="{ content: `<div class='p-2'>${country[0]} <strong class='ml-8'>${country[1]} (${calcPerc(country[1],analytics.countries.total)})</strong></div>`, followCursor: true }"
                            v-for="country in analytics.countries.rows.slice(0,11)" 
                            class="flex-table-row">
                                <div class="col font-bold">{{ country[0] }}</div>
                                <div class="col text-right">{{ country[1] }}</div>
                                <div :style="{ width: calcPerc(country[1],analytics.countries.total) }" class="duration-300 left-0 bottom-0 border-b-2 absolute border-ms-cyan-10"></div>
                        </div>
                    </section>

                </div>
                <div v-if="map" class="bg-white col-span-2">

                    <h2 class="flex w-full border-b p-4">
                        Users in  
                        <div class="filters-dropdown">
                            <span>Map</span>
                        </div>
                    </h2>

                    <div class="flex justify-center items-center py-12">
                        <Maps :period="fullUnixRange" />
                    </div>

                </div>
                <div class="bg-white" v-if="Object.keys(analytics.heatmap).length==7">
                    
                    <h2 class="flex w-full border-b p-4">
                        Users by  
                        <div class="filters-dropdown">
                            <span>Time of day</span>
                        </div>
                    </h2>

                    <div class="h-96">
                        <heatmap :data="analytics.heatmap" />
                    </div>

                </div>
                <div class="bg-white">

                    <h2 class="flex w-full border-b p-4">
                        Users by  
                        <div class="filters-dropdown">
                            <span>Sources</span>
                        </div>
                    </h2>

                    <section class="flex-table p-4">
                        <div class="flex-table-header">
                            <div class="col">Source</div>
                            <div class="col text-right">Sessions</div>
                        </div>
                        <div
                            v-tippy="{ content: `<div class='p-2'><strong>${source[0]}</strong> <br> ${source[2]} pageviews <br> ${fancyTimeFormat(source[3])} <strong class='ml-8'>${source[1]} (${calcPerc(source[1],analytics.referrals.total)})</strong></div>`, followCursor: true }"
                            v-for="source in analytics.referrals.rows.slice(0,11)" 
                            class="flex-table-row">
                                <div class="col font-bold">{{ source[0] }}</div>
                                <div class="col text-right">{{ source[1] }}</div>
                                <div :style="{ width: calcPerc(source[1],analytics.referrals.total) }" class="duration-300 left-0 bottom-0 border-b-2 absolute border-ms-cyan-10"></div>
                        </div>
                    </section>

                </div>
                    <div class="bg-white">

                    <h2 class="flex w-full border-b p-4">
                        Users by  
                        <div class="filters-dropdown">
                            <span>OS</span>
                        </div>
                    </h2>

                        <section class="flex-table p-4">
                            <div class="flex-table-header">
                                <div class="col">Operation System</div>
                                <div class="col text-right">Sessions</div>
                            </div>
                            <div
                                v-tippy="{ content: `<div class='p-2'><strong>${source[0]}</strong> <br> ${source[2]} pageviews <br> ${fancyTimeFormat(source[3])} <strong class='ml-8'>${source[1]} (${calcPerc(source[1],analytics.os.total)})</strong></div>`, followCursor: true }"
                                v-for="source in analytics.os.rows.slice(0,11)" 
                                class="flex-table-row">
                                    <div class="col font-bold">{{ source[0] }}</div>
                                    <div class="col text-right">{{ source[1] }}</div>
                                    <div :style="{ width: calcPerc(source[1],analytics.os.total) }" class="duration-300 left-0 bottom-0 border-b-2 absolute border-ms-cyan-10"></div>
                            </div>
                        </section>

                    </div>
            </div>

            

        </div>

        <div v-else-if="loading">

            <div class="flex flex-col items-center justify-center p-12 relative">
                <ContentLoader type="bars" />
                <h2 class="text-ms-gray-80 text-lg mt-4">Fetching data from Google</h2>
            </div>

        </div>
        </transition>

    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout.vue'
    import Welcome from '@/Jetstream/Welcome.vue'
    import VueCountdown from '@chenfengyuan/vue-countdown'
    import { DatePicker } from 'v-calendar'
    import ContentLoader from '@/Components/ContentLoader.vue'
    import moment from "moment-timezone"
    import Heatmap from '@/Charts/Heatmap.vue'
    import Maps from '@/Charts/Maps.vue'
    import { useToast } from "vue-toastification"

    export default {
        components: {
            AppLayout,
            Welcome,
            VueCountdown,
            DatePicker,
            Heatmap,
            Maps,
            ContentLoader
        },
        
        props: ['visitedpages','analytics','loaded','period','map'],
        
        setup(){
            const toast = useToast();
            return { toast }
        },

        data(){    
            return{
                moment: moment,
                range: {
                    start: this.period.start,
                    end: this.period.end
                },
                masks: {
                    input: 'DD-MM-YYYY',
                },
                showPanel: false,
                filters: {
                    geo: 'City'
                },
                loading: true
            }
        },
        watch: {
            range(v){
                
                const start = moment(v.start).format('DD-MM-YYYY')
                const end = moment(v.end).format('DD-MM-YYYY')

                this.$inertia.visit(`/analytics?period=${start}@${end}`)
                this.toast('Period updating...')

            }
        },
        computed: {
            rangeUnixStart(){
                return moment(this.range.start).tz("Europe/London").format('DD-MM-YYYY')
            },
            rangeUnixEnd(){
                return moment(this.range.end).tz("Europe/London").format('DD-MM-YYYY')
            },
            fullUnixRange(){
                return this.rangeUnixStart + '@' + this.rangeUnixEnd;
            }
        },
        methods: {
            calcPerc(num, total){
              return Math.round(num * 100 / total) + '%';
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
            }
        },
        mounted(){
            if(this.loaded == false){
                this.$inertia.post(window.location.href, { loader: true }, {
                    onSuccess: (page) => {
                        this.loading = false
                    }
                });
            } else {
                this.loading = false
            }
        }
    }
</script>
