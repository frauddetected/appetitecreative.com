<template>
    <app-layout title="Prizes">

        <template #header>
            <h2 class="text-lg text-ms-gray-160 leading-tight">
                Prizes
            </h2>
            <nav class="flex">

                                <jet-dropdown align="right" width="64">

                                    <template #trigger>
                <button class="hover:bg-ms-gray-20 focus:bg-ms-gray-30 text-ms-gray-160 p-3 flex items-center">
                    <i class="ms-Icon ms-Icon--Add mr-2"></i> Select Prize
                </button>
                                    </template>

                                    <template #content>
                    
                        <a  :href="route('prizes', { 'prize': prize.id })" 
                            class="hover:bg-ms-gray-20 cursor-pointer hover:text-ms-cyan-120 p-3 flex items-center justify-between" 
                            v-for="prize in prizes"
                            :key="prize.title"
                            >
                            <strong>{{ prize.title }}</strong>
                            <span class="text-ms-gray-100">{{ prize.periodicity }}</span>
                        </a>
                    
                                    </template>

                                </jet-dropdown>

    

            </nav>
        </template>

        <div>
            <div class="mx-auto py-10 px-20 flex items-start">

                <div class="w-9/12">

                <div class="flex">
                    <div class="w-full">

                        <table class="table" v-if="leaderboard.length">
                            <tr class="header">
                                <th>Name</th>
                                <th>Profile</th>
                                <th>Score</th>
                                <th>Source</th>
                            </tr>
                            <tr v-for="u in leaderboard" :key="u.id">
                                <td>
                                    {{ u.name }}
                                </td>
                                <td>
                                    {{ u.user.profile.gender }}
                                    {{ u.user.profile.age }}
                                </td>
                                <td>
                                    {{ u.score }}
                                </td>
                                <td>
                                    {{ u.origin_value }}
                                    {{ u.source_value }}
                                </td>
                            </tr>
                        </table>

                    </div>
                </div>
                </div>

                <div class="w-3/12 ml-12">

                    <div class="mt-5 shadow-xl p-4 bg-white z-10 relative" v-if="prize">
                        
                        <p class="font-light text-lg py-4">{{ prize.title }}</p>
                        <ul class="max-h-96 overflow-y-scroll">
                            <li 
                                @click="clickDate(p)"
                                class="p-2 hover:bg-ms-gray-10 cursor-pointer rounded-sm flex justify-between items-center" 
                                v-for="p in period">
                                <span>{{ p.date }}</span>
                                <span v-if="p.winner" class="text-ms-cyan-20">
                                    <i class="ms-Icon ms-Icon--SkypeCheck"></i> 
                                </span>
                                <span v-else class="text-ms-magenta-110 text-xs">
                                    Need Winner
                                </span>
                            </li>
                        </ul>

                    </div>

                    <div class="mt-8 shadow-ms" v-if="winnerPanel">
                        <div v-if="winnerPanel.winner" class="bg-ms-cyan-30 text-white p-6 text-center">
                            <h1 class="text-lg font-light">{{ winnerPanel.date }}</h1>
                            <p class="font-bold my-4">{{ winnerPanel.winner.user.profile.name }}</p>
                            <p>{{ winnerPanel.winner.user.profile.email }}</p>
                            <p>{{ winnerPanel.winner.user.profile.age }}</p>
                            <p>{{ winnerPanel.winner.user.profile.gender }}</p>
                            <button @click="deleteCurrentWinner(winnerPanel.winner.id)" class="bg-white text-ms-magenta-30 font-bold px-4 py-1 mt-3">Delete Current Winner</button>
                        </div>
                        <div v-else class="bg-ms-magenta-110 p-6 text-center text-white">
                            <h1 class="text-lg font-light">{{ winnerPanel.date }}</h1>
                            <p class="py-3">We don't have a winner for the selected period.</p>
                            <div class="">
                                <button @click="getWinner" class="bg-white text-ms-magenta-30 font-bold px-4 py-1">Get Winner</button>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout.vue'
    import JetDropdown from '@/Jetstream/Dropdown.vue'
    import JetDialogModal from '@/Jetstream/DialogModal.vue'
    import moment from 'moment'

    export default {
        
        props: [
           'leaderboard',
           'prizes',
           'prize',
           'period',
           'selectedDate'
        ],

        data(){
            return{
                moment: moment,
                winnerPanel: false,
            }
        },

        beforeMount(){
            if(this.selectedDate){
                this.winnerPanel = this.period.filter(item => item.date == this.selectedDate)[0]
            }
        },

        methods: {
            deleteCurrentWinner(id){
                this.$inertia.post('/prizes/winner/delete', { wid: id })
            },
            selectPrize(id){
                window.location.href = '?prize=' + id
            },
            clickDate(p){
                this.$inertia.get('/prizes', { date: p.date, prize: this.prize.id })
            },
            getWinner(){
                this.$inertia.post('/prizes/winner', { date: this.winnerPanel.date, prize: this.prize.id })
            }
        },

        components: {
            AppLayout,
            JetDialogModal,
            JetDropdown
        },
    }
</script>
