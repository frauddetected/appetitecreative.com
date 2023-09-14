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

                <VDropdown>
                    <button class="hover:bg-ms-gray-20 text-ms-gray-160 p-3 flex items-center">
                        <i class="ms-Icon ms-Icon--ExcelDocument mr-2"></i> Export
                    </button>
                    <template #popper>
                        <div class="flex flex-col">
                            <form action="" class="mb-5 w-40">
                                <p class="font-light text-lg py-2">Export Fields</p>
                                <label class="flex items-center">
                                    <input type="checkbox" v-model="exporting.fields" value="name" class="mr-1"> Name
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" v-model="exporting.fields" value="email" class="mr-1"> Email
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" v-model="exporting.fields" value="gender" class="mr-1"> Gender
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" v-model="exporting.fields" value="age" class="mr-1"> Age Group
                                </label>
                                <label class="flex items-center my-1">
                                    <input type="checkbox" v-model="exporting.fields" value="score" class="mr-1"> Score
                                </label>
                                <label class="flex items-center my-1">
                                    <input type="checkbox" v-model="exporting.fields" value="origin" class="mr-1"> Origin (Score)
                                </label>
                                <label class="flex items-center my-1">
                                    <input type="checkbox" v-model="exporting.fields" value="package" class="mr-1"> Package
                                </label>
                                <label class="flex items-center my-1">
                                    <input type="checkbox" v-model="exporting.fields" value="date" class="mr-1"> Date
                                </label>
                                <p class="font-light text-lg py-2">Deleted Entries</p>
                                <label class="flex items-center my-1">
                                    <input type="checkbox" v-model="exporting.fields" value="deleted_entries" class="mr-1"> Include
                                </label>
                                <p class="font-light text-lg py-2">Export Period</p>
                                <label class="flex items-center">
                                    <input type="radio" v-model="exporting.period" :value="`${rangeUnixStart}@${rangeUnixEnd}`" class="mr-1"> Selected
                                </label>                             
                                <label class="flex items-center">
                                    <input type="radio" v-model="exporting.period" value="all" class="mr-1"> All
                                </label>
                            </form>

                            <button @click="exportDownload" class="border border-ms-gray-160 px-4 py-1 hover:bg-ms-gray-40 ml-2 font-bold">Download</button>

                        </div>
                    </template>
                </VDropdown>    
                
                <button class="hover:bg-ms-gray-20 text-ms-gray-160 p-3 flex items-center is-dropdown">
                    <i class="ms-Icon ms-Icon--ProjectCollection mr-2"></i> Country <strong class="ml-3">{{countryName}}</strong>
                    <ul>
                        <li @click="filterCountry('')">All</li>
                        <li @click="filterCountry('Slovakia')">Slovakia</li>
                        <li @click="filterCountry('Slovenia')">Slovenia</li>
                        <li @click="filterCountry('Hungary')">Hungary</li>
                        <li @click="filterCountry('Czech Republic')">Czech Republic</li>
                    </ul>
                </button>

                <button class="hover:bg-ms-gray-20 text-ms-gray-160 p-3 flex items-center is-dropdown">
                    <i class="ms-Icon ms-Icon--ProjectCollection mr-2"></i> QR <strong class="ml-3">{{ brand == 'All' ? brand : brand.title }}</strong>
                    <ul>
                        <li @click="filterBrand('')">All</li>
                        <li @click="filterBrand(brand.id)" v-for="brand in qr">
                            {{ brand.title }}
                        </li>
                    </ul>
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

                <!-- Main Chart -->
                <transition name="fade" mode="out-in">
                <div v-if="grid.scans" class="w-7/12 p-6 bg-white rounded-sm mr-3">

                  <div class="flex w-full mb-8 pl-8 pr-2">
                      <div class="w-3/12 text-center" v-tippy="{ content: 'This number tracks a QR code scans.' }">
                          <h2 class="uppercase">
                                Scans (QR Code)
                                <a class="hover:text-green-600" :href="`/export/daily?period=${rangeUnixStart}@${rangeUnixEnd}`">
                                    <i class="ms-Icon ms-Icon--ExcelDocument ml-2"></i>
                                </a>
                          </h2>
                          <h1 class="text-4xl ml-auto font-bold">
                                {{ nFormatter(stats.scans.count, 1) }}
                          </h1>
                          <span class="rounded-full w-2 h-2 inline-block bg-ms-orange-10"></span>
                      </div>
                      <div class="w-3/12 text-center" v-tippy="{ content: 'This number tracks forms / e-mails on database.' }">
                          <h2 class="uppercase">
                                E-mails 
                                <a class="hover:text-green-600" :href="`/export/emails?period=${rangeUnixStart}@${rangeUnixEnd}`">
                                    <i class="ms-Icon ms-Icon--ExcelDocument ml-2"></i>
                                </a>
                          </h2>
                          <h1 class="text-4xl ml-auto font-bold">
                              {{ nFormatter(stats.registrations.count, 1) }}
                          </h1>
                          <span class="rounded-full w-2 h-2 inline-block bg-ms-cyan-10"></span>
                      </div>
                      <div class="w-4/12 text-center" v-tippy="{ content: 'Average number of scans per user.' }">
                          <h2 class="uppercase">Avg Scans / User</h2>
                          <h1 class="text-4xl ml-auto font-bold">
                              {{ (stats.scans.count / stats.registrations.count).toFixed(1) }}
                          </h1>
                      </div>
                      <div class="w-4/12 text-center" v-tippy="{ content: 'The total of ALL the individual of entries (recipe, sharing, popup).' }">
                          <h2 class="uppercase">Submissions</h2>
                          <h1 class="text-4xl ml-auto font-bold">
                              {{ nFormatter(stats.leaderboard.count, 1) }}
                          </h1>
                      </div>
                  </div>
                  <div>
                      <line-chart height="240px" :library="{ interaction: { mode: 'index' }, animation: { duration: 3000 } }" :dataset="{pointRadius: 0}" :legend="false" :data="charts.scans_scores" :colors="['#ffaa44','#00b7c3']"></line-chart>
                  </div>
                    
                </div>
                <div v-else class="w-7/12 p-6 bg-white rounded-sm mr-3 flex justify-center items-center">
                    <Loader type="bars" />
                </div>
                </transition>

                <transition name="fade" mode="out-in">
                <div v-if="grid.scans" class="w-5/12 ml-3 bg-white rounded-sm flex flex-wrap items-start relative">

                        <div class="w-full">

                            <h2 class="flex w-full border-b p-4">
                                Scans by 
                                <div class="filters-dropdown">
                                    <span>{{ filters.scans }}</span>
                                    <aside>
                                        <ul>
                                            <li>Package</li>
                                        </ul>
                                    </aside>
                                </div>
                            </h2>

                        </div>

                        <div class="p-6 flex w-full">

                            <div class="w-7/12">
                                <pie-chart class="p-12" :legend="false" :library="{ animation: { duration: 3000 }}" :colors="['#00bcf2','#8378de','#005b70','#c239b3','#881798','#0078d4','#8764b8']" :data="charts.skus"></pie-chart>
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
                                        <div :style="{ width: calcPerc(top.views, stats.scans.count) }" class="duration-300 left-0 bottom-0 h-full absolute bg-ms-cyan-10/10"></div>
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
                <div v-else class="w-5/12 ml-3 bg-white rounded-sm flex justify-center items-center">
                    <Loader type="bars" />
                </div>
                </transition>

            </div>

        <div class="flex gap-x-6 mt-6">

            <!-- Games -->
            <transition name="fade" mode="out-in">
            <div v-if="grid.games" class="w-1/3 bg-white rounded-sm">

                <div class="w-full flex flex-col">
                    <h2 class="flex w-full p-4 border-b mb-4">
                        Submissions
                    </h2>
                    <div class="px-6">
                    <div class="flex-table-header">
                        <div class="col">Origin</div>
                        <div class="col text-right">Total</div>
                    </div>
                    <div
                        v-tippy="{ content: `<div class='p-2'>origin <strong class='ml-2'>${origin.views} (${calcPerc(origin.views, stats.leaderboard.count)})</strong></div>`, followCursor: true }"
                        v-for="(origin) in stats.leaderboard.origin" 
                        class="flex-table-row">
                            <div class="col font-bold capitalize">{{ origin.origin_value }}</div>
                            <div class="col text-right">{{ origin.views }}</div>
                            <div :style="{ width: calcPerc(origin.views, stats.leaderboard.count) }" class="duration-300 left-0 bottom-0 h-full absolute bg-ms-orange-20/10"></div>
                    </div>
                </div>
                </div>

            </div>
            <div v-else class="w-1/3 bg-white rounded-sm mx-3 flex justify-center items-center">
                <Loader type="table" />
            </div>
            </transition>
            <!-- end games -->

            <div v-if="grid.share" class="w-1/3 bg-white rounded-sm">
                
                <div class="w-full border-b p-4 mb-4">
                    <h2>Social by <span class="border-b font-bold border-black border-dashed">Shares</span></h2>
                </div>

                <div class="flex w-full mt-4 justify-center">
                    <div class="w-1/3 text-center" v-tippy="{ content: `<div class='p-2 w-40 text-center'>Total number of games started playing<div>`, followCursor: false }">
                        <h2 class="uppercase text-xs">Total Shares</h2>
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
                            <div class="col font-bold capitalize">{{ top.method }}</div>
                            <div class="col text-right">{{ top.views }}</div>
                            <div :style="{ width: calcPerc(top.views, stats.share.count) }" class="duration-300 left-0 bottom-0 h-full absolute bg-ms-magenta-10/10"></div>
                    </div>
                </section>

            </div>
            <div v-else class="w-1/3 bg-white rounded-sm mr-3 flex justify-center items-center">
                <Loader type="table" />
            </div>

            <div v-if="grid.share" class="w-1/3 bg-white rounded-sm">
                
                <div class="w-full border-b p-4 mb-4">
                    <h2>Shops</h2>
                </div>

                <div class="px-6">
                    <div class="flex-table-header">
                        <div class="col">Shops</div>
                        <div class="col text-right">Total</div>
                    </div>
                    <div
                        v-tippy="{ content: `<div class='p-2'>${alcohol} <strong class='ml-2'>${count} (${calcPerc(count, totalWhereBought)})</strong></div>`, followCursor: true }"
                        v-for="(count,wherebought) in stats.users.wherebought" 
                        class="flex-table-row">
                            <div class="col font-bold">{{ wherebought }}</div>
                            <div class="col text-right">{{ count }}</div>
                            <div :style="{ width: calcPerc(count, totalWhereBought) }" class="duration-300 left-0 bottom-0 h-full absolute bg-ms-magenta-20/10"></div>
                    </div>
                </div>

            </div>
            <div v-else class="w-1/3 bg-white rounded-sm mr-3 flex justify-center items-center">
                <Loader type="table" />
            </div>

            <div v-if="grid.profile" class="w-1/3 bg-white rounded-sm">

                <div class="w-full flex flex-col">
                    <h2 class="flex w-full border-b p-4 mb-4">
                        
                        User Profile by  
                        <div class="filters-dropdown">
                            <span>{{ filters.profile }}</span>
                            <aside>
                                <ul>
                                    <li :class="{ 'font-bold' : filters.profile == 'Gender' }" @click="filters.profile='Gender'">Gender</li>
                                    <li :class="{ 'font-bold' : filters.profile == 'Age' }" @click="filters.profile='Age'">Age</li>
                                    <li :class="{ 'font-bold' : filters.profile == 'Alcohol' }" @click="filters.profile='Alcohol'">Alcohol Consumption</li>
                                </ul>
                            </aside>
                        </div>

                    </h2>

                    <div class="flex w-full mt-4">
                         
                        <section v-if="filters.profile == 'Gender'" class="flex-table p-6">
                            <div class="flex-table-header">
                                <div class="col">Gender</div>
                                <div class="col text-right">Total</div>
                            </div>
                            <div
                                v-tippy="{ content: `<div class='p-2'>${gender} <strong class='ml-2'>${count} (${calcPerc(count, totalGender)})</strong></div>`, followCursor: true }"
                                v-for="(count,gender) in stats.users.gender" 
                                class="flex-table-row">
                                    <div class="col font-bold capitalize">{{ gender }}</div>
                                    <div class="col text-right">{{ count }}</div>
                                    <div :style="{ width: calcPerc(count, totalGender) }" class="duration-300 left-0 bottom-0 h-full absolute bg-ms-cyan-20/10"></div>
                            </div>
                        </section>

                        <section v-if="filters.profile == 'Age'" class="flex-table p-6">
                            <div class="flex-table-header">
                                <div class="col">Age Group</div>
                                <div class="col text-right">Total</div>
                            </div>
                            <div
                                v-tippy="{ content: `<div class='p-2'>${gender} <strong class='ml-2'>${count} (${calcPerc(count, totalGender)})</strong></div>`, followCursor: true }"
                                v-for="(count,gender) in stats.users.age" 
                                class="flex-table-row">
                                    <div class="col font-bold">{{ gender }}</div>
                                    <div class="col text-right">{{ count }}</div>
                                    <div :style="{ width: calcPerc(count, totalGender) }" class="duration-300 left-0 bottom-0 h-full absolute bg-ms-cyan-20/10"></div>
                            </div>
                        </section>

                        <section v-if="filters.profile == 'Alcohol'" class="flex-table p-6">
                            <div class="flex-table-header">
                                <div class="col">Alcohol</div>
                                <div class="col text-right">Total</div>
                            </div>
                            <div
                                v-tippy="{ content: `<div class='p-2'>${alcohol} <strong class='ml-2'>${count} (${calcPerc(count, totalAlcohol)})</strong></div>`, followCursor: true }"
                                v-for="(count,alcohol) in stats.users.alcohol" 
                                class="flex-table-row">
                                    <div class="col font-bold">{{ alcohol }}</div>
                                    <div class="col text-right">{{ count }}</div>
                                    <div :style="{ width: calcPerc(count, totalAlcohol) }" class="duration-300 left-0 bottom-0 h-full absolute bg-ms-cyan-20/10"></div>
                            </div>
                        </section>
                        

                    </div>

                </div>            
            </div>
            <div v-else class="w-1/3 bg-white rounded-sm ml-3 flex justify-center items-center">
                <Loader type="table" />
            </div>
        
        </div>

            <!-- other row -->
            <div class="flex gap-x-6 mt-6">
                <div class="w-9/12">
                    <GridQuiz :fullUnixRange="fullUnixRange" :brand="brand" :country="countryName" />
                </div>
                <div v-if="grid.share" class="w-3/12 bg-white rounded-md">

                <div class="w-full border-b p-4 mb-4">
                    <h2>Recipes by <span class="border-b font-bold border-black border-dashed">Quiz Results</span></h2>
                </div>

                <section class="flex-table p-6 mt-8">
                    <div class="flex-table-header">
                        <div class="col">Recipe</div>
                        <div class="col text-right">Views</div>
                    </div>
                    <div
                        v-tippy="{ content: `<div class='p-2'>${top.recipe} <strong class='ml-2'>${top.views} (${calcPerc(top.views, stats.recipes.count)})</strong></div>`, followCursor: true }"
                        v-for="top in stats.recipes.top" 
                        class="flex-table-row">
                            <div class="col font-bold capitalize">{{ top.recipe }}</div>
                            <div class="col text-right">{{ top.views }}</div>
                            <div :style="{ width: calcPerc(top.views, stats.recipes.count) }" class="duration-300 left-0 bottom-0 h-full absolute bg-ms-magenta-110/10"></div>
                    </div>
                </section>

                </div>
            </div>
            <!-- end row -->
        
        </div>
        
        <SidePanel width="w-5/12" :show="filterUserLeaderboad.panel" @close="filterUserLeaderboad.panel=false">
            
            <h1 class="text-xl font-bold">Leaderboard by User</h1>

            <form method="post" class="mt-5 flex items-center" action="" @submit.prevent="filterUserLeaderboadSubmit">
                <input v-model="filterUserLeaderboad.input" class="w-1/2" type="text" placeholder="E-mail"> 
                <label for="show-deleted-scores" class="text-xs mx-4">
                    <input id="show-deleted-scores" v-model="filterUserLeaderboad.incdeleted" type="checkbox"> Show Deleted Scores
                </label>
                <button type="submit" class="border border-ms-gray-160 px-4 py-2 hover:bg-ms-gray-40 ml-2 font-bold">Search</button>
            </form>
            
            <div v-if="filterUserLeaderboad.data.records" class="m-8 text-center flex flex-col">
                <div class="">
                    <h1 class="text-xl font-bold mb-2">{{ filterUserLeaderboad.data.name }}</h1>
                    <h2 class="text-base">Score - {{ filterUserLeaderboad.data.score }}</h2>
                    <h3 class="text-base">Entries - {{ filterUserLeaderboad.data.records.length }}</h3>
                </div>
                <div class="border-t border-b mt-5 py-5 text-xs">
                    <button @click="delAllEntries" class="border mb-2 border-ms-gray-160 px-4 py-2 bg-red-100 hover:bg-red-200 ml-2 font-bold">Delete All</button>
                    <p>Use this option with caution (cheaters/bots). Will delete all person records.</p>
                </div>
            </div>

            <table v-if="filterUserLeaderboad.data.records" class="mt-8">
                <tr>
                    <th class="text-center w-2/12">Score</th>
                    <th class="text-center w-2/12">Origin</th>
                    <th class="text-center w-4/12">Source</th>
                    <th class="text-center w-4/12">Date</th>
                    <th class=""></th>
                </tr>
                <tr 
                    :class="{ 'bg-red-100 text-red-900 text-opacity-50' : item.deleted_at }"
                    class="border-b hover:bg-gray-100" v-for="item in filterUserLeaderboad.data.records">
                    <td class="text-center py-1">{{ item.score }}</td>
                    <td class="text-center">{{ item.origin_value }}</td>
                    <td class="text-center">
                        {{ item.source_id == 1 ? 'Package': item.source_id == 4 ? 'Direct' : '' }} 
                        {{ item.source_value }}
                    </td>
                    <td class="text-center">{{ moment(item.created_at).format('DD-MM-YYYY HH:mm') }}</td>
                    <td>
                        <i @click="delEntryLeaderboard(item.id)" class="ms-Icon ms-Icon--Delete hover:text-red-600 cursor-pointer"></i>
                    </td>
                </tr>
                
            </table>            

        </SidePanel>


    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout.vue'
    import SidePanel from '@/Components/SidePanel.vue'
    import VueCountdown from '@chenfengyuan/vue-countdown'
    import { DatePicker } from 'v-calendar'
    import { useToast } from "vue-toastification"
    import moment from "moment-timezone"
    import Loader from '@/Components/ContentLoader.vue'

    import GridQuiz from '@/Grid/Quiz'

    export default {
        
        props: ['project','period','stats','charts','country','brand','qr','freqTable','between'],

        setup(){
            const toast = useToast();
            return { toast }
        },

        components: {
            AppLayout,
            VueCountdown,
            DatePicker,
            SidePanel,
            Loader,
            GridQuiz
        },

        data(){    
            
            const now = new Date();
            const ends_at = new Date(this.project.ends_at);

            var d = new Date();
            d.setDate(d.getDate()-7);

            return{
                moment: moment,
                countdown: ends_at - now,
                range: {
                    start: this.period.start,
                    end: this.period.end
                },
                masks: {
                    input: 'DD-MM-YYYY',
                },
                filters: {
                    scans: 'Package',
                    selfies: 'Views',
                    games: 'Start',
                    registrations: 'Users',
                    profile: 'Gender',
                    quiz: 'all'
                },
                pagination: {
                    quiz: 15
                },
                exporting: {
                    fields: ['name','email','date'],
                    period: ''
                },
                filterUserLeaderboad: {
                    panel: false,
                    incdeleted: false,
                    input: '',
                    data: [],
                    period: null
                },
                gridata: [],
                grid: {
                    scans: false,
                    profile: false,
                    leaderboard: false,
                    selfies: false,
                    games: false,
                    share: false
                },
                leaderboardPeriod: 'may-jun',
                countryName: this.country,
            }
        },
        watch: {
            range(v){
                
                const start = moment(v.start).format('DD-MM-YYYY')
                const end = moment(v.end).format('DD-MM-YYYY')

                this.$inertia.visit(`/?period=${start}@${end}`)
                this.toast('Period updating...')

            },
            leaderboardPeriod(v){

                this.grid.leaderboard = 0

                axios.post('/', { grid: 'leaderboard', lbperiod: v, period: this.fullUnixRange }).then(r => {
                    this.stats.leaderboard = r.data.stats.leaderboard
                    this.charts.leaderboard = r.data.charts.leaderboard
                    this.between.start = r.data.between.start
                    this.between.end = r.data.between.end
                    this.grid.leaderboard = 1   
                })

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
            },
            totalWhereBought(){
                let count = 0;
                Object.values(this.stats.users.wherebought).forEach(item => {
                    count += item
                })
                return count;
            },
            totalGender(){
                let count = 0;
                Object.values(this.stats.users.gender).forEach(item => {
                    count += item
                })
                return count;
            },
            totalAge(){
                let count = 0;
                Object.values(this.stats.users.age).forEach(item => {
                    count += item
                })
                return count;
            },
            totalAlcohol(){
                let count = 0;
                Object.values(this.stats.users.alcohol).forEach(item => {
                    count += item
                })
                return count;
            },
        },
        methods: {
            delEntryLeaderboard(id){
                let $this = this
                if (confirm("Are you sure?") == true){
                    axios.post('/', { action: 'delEntryLeaderboard', id: id }).then(r => {
                        if(r.data.success == true){
                            $this.filterUserLeaderboadSubmit()
                            $this.toast('Deleted')
                        }
                    })
                }
            },
            delAllEntries(){
                let $this = this
                if (confirm("Are you sure? This will delete ALL user entries") == true){
                    axios.post('/', { action: 'delAllEntries', email: this.filterUserLeaderboad.input }).then(r => {
                        if(r.data.success == true){
                            $this.filterUserLeaderboadSubmit()
                            $this.toast('Deleted')
                        }
                    })
                }
            },
            filterUserLeaderboadSubmit(){
                
                axios.post('/', {
                    type: 'filter',
                    email: this.filterUserLeaderboad.input,
                    incdeleted: this.filterUserLeaderboad.incdeleted,
                    lbperiod: this.leaderboardPeriod
                }).then(r => {
                    this.filterUserLeaderboad.data = r.data
                })

            },
            filterBrand(v){
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.set('filter[brand]', v);
                window.location.search = decodeURIComponent(urlParams);
            },
            filterCountry(v){
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.set('filter[country]', v);
                window.location.search = decodeURIComponent(urlParams);
            },
            exportDownload()
            {
                let fields = encodeURI(this.exporting.fields)
                let period = this.exporting.period

                window.location.href = '/exports?fields=' + fields + '&period=' + period

                this.toast('Download starting...')
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
        mounted()
        {            
            
            this.exporting.period = this.rangeUnixStart + '@' + this.rangeUnixEnd

            axios.post('/', { grid: 'scans', period: this.fullUnixRange, filter: { brand: this.brand.id, country: this.country } }).then(r => {
                this.stats.scans = r.data.stats.scans
                this.stats.freqTable = r.data.freqTable
                this.stats.registrations = r.data.stats.registrations
                this.stats.leaderboard = r.data.stats.leaderboard
                this.charts.skus = r.data.charts.skus
                this.charts.scans_scores = r.data.charts.scans_scores
                this.grid.scans = 1                                
            })

            setTimeout(() => {
                axios.post('/', { grid: 'profile', period: this.fullUnixRange, filter: { brand: this.brand.id, country: this.country } }).then(r => {
                    this.stats.users = r.data.stats.users
                    this.grid.profile = 1         
                })
            }, 1000);

            setTimeout(() => {
                axios.post('/', { grid: 'leaderboard', period: this.fullUnixRange, filter: { brand: this.brand.id, country: this.country } }).then(r => {
                    this.stats.leaderboard = r.data.stats.leaderboard
                    this.charts.leaderboard = r.data.charts.leaderboard
                    this.grid.leaderboard = 1         
                })
            }, 2000);

            setTimeout(() => {
                axios.post('/', { grid: 'games', period: this.fullUnixRange, filter: { brand: this.brand.id, country: this.country } }).then(r => {
                    this.stats.game_start = r.data.stats.game_start
                    this.stats.game_end = r.data.stats.game_end
                    this.grid.games = 1         
                })
            }, 3000);

            setTimeout(() => {
                axios.post('/', { grid: 'selfie', period: this.fullUnixRange, filter: { brand: this.brand.id, country: this.country } }).then(r => {
                    this.stats.selfie_select = r.data.stats.selfie_select
                    this.stats.selfie_take = r.data.stats.selfie_take
                    this.grid.selfies = 1         
                })
            }, 4000);

            setTimeout(() => {
                axios.post('/', { grid: 'share', period: this.fullUnixRange, filter: { brand: this.brand.id, country: this.country } }).then(r => {
                    this.stats.share = r.data.stats.share
                    this.stats.recipes = r.data.stats.recipes
                    this.grid.share = 1         
                })
            }, 5000);

        }
    }
</script>
