<template>
    <app-layout title="Dashboard">
        
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
            <nav class="flex items-center">
                <DatePicker class="w-full" v-model="range" color="teal" is-range mode="date" :masks="masks">
    <template v-slot="{ inputValue, inputEvents, isDragging }">
          <div class="flex flex-col sm:flex-row justify-start items-center">
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
                class="flex-grow pl-8 pr-2 py-1 border-ms-gray-120 border w-full"
                :class="isDragging ? 'text-ms-gray-60' : 'text-ms-gray-160'"
                :value="inputValue.start"
                v-on="inputEvents.start"
              />
            </div>
            <span class="flex-shrink-0 m-2">
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
                class="flex-grow pl-8 pr-2 py-1 border-ms-gray-120 border w-full"
                :class="isDragging ? 'text-ms-gray-60' : 'text-ms-gray-160'"
                :value="inputValue.end"
                v-on="inputEvents.end"
              />
            </div>
          </div>
        </template>
                </DatePicker>
            </nav>
        </template>

        <div class="p-8 min-h-screen bg-ms-gray-40">

            <div class="bg-white p-4" v-if="project.ends_at">
                <vue-countdown :time="countdown" v-slot="{ days, hours, minutes, seconds }">
                    <div class="flex w-full text-lg justify-between">
                        Time left before project end
                        <span>{{ days }} days, {{ hours }} hours, {{ minutes }} minutes, {{ seconds }} seconds</span>
                    </div>
                </vue-countdown>
            </div>
<!-- 
            <div class="flex mt-2">
                <div class="w-7/12 p-4 bg-white">

                  <div class="flex">
                      <div>
                          <h2 class="uppercase">Scans</h2>
                           <h1 class="text-4xl ml-auto font-bold">100</h1>
                      </div>
                  </div>
                  <div>
                      <area-chart height="260px" :colors="['#00bcf2','#881798']"></area-chart>
                  </div>
                    
                </div>
            </div> -->

        </div>

    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout.vue'
    import Welcome from '@/Jetstream/Welcome.vue'
    import VueCountdown from '@chenfengyuan/vue-countdown'
    import { Calendar, DatePicker } from 'v-calendar'

    export default {
        components: {
            AppLayout,
            Welcome,
            VueCountdown,
            DatePicker
        },
        props: ['project'],
        data(){    
            
            const now = new Date();
            const ends_at = new Date(this.project.ends_at);

            var d = new Date();
            d.setDate(d.getDate()-7);

            return{
                countdown: ends_at - now,
                range: {
                    start: d,
                    end: new Date(),
                },
                masks: {
                    input: 'DD-MM-YYYY',
                },
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
            }
        },
        mounted(){
            console.log(this.visitedpages)
        }
    }
</script>
