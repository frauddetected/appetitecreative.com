<template>
    <app-layout title="Prizes">

        <template #header>
            <h2 class="text-lg text-ms-gray-160 leading-tight">
                Participants
            </h2>
            <nav class="flex">
                <input type="text" class="h-8 text-sm" v-model="filter" name="filter" placeholder="Type to filter email">
                <input type="text" class="h-8 text-sm ml-1" v-if="filter"  v-model="filterdate" name="filter" placeholder="Type to filter date">
                <!--
                <a href="/prizes?export=true" class="hover:bg-ms-gray-20 text-ms-gray-160 p-3 flex items-center">
                    <i class="ms-Icon ms-Icon--ExcelDocument mr-2"></i> Export
                </a>
                -->
            </nav>
        </template>

        <div>
            <div class="mx-auto py-10 px-20 flex items-start">

                <div class="w-9/12">

                <div class="flex">
                    <div class="w-full">

                        <table class="table" v-if="leaderboard.length">
                            <tr class="header">
                                <th></th>
                                <th>Name</th>
                                <th>Profile</th>
                                <th>Score</th>
                                <th>Date</th>
                                <th></th>
                            </tr>
                            <tr v-for="u in fLeaderboard" :key="u.id">
                                <td class="w-10">
                                    <p class="text-xs text-gray-400">{{ u.id }}. {{ u.user.id }}.</p>
                                </td>
                                <td>
                                    <h4>{{ u.name }}</h4>
                                    <p>{{ u.email }}</p>
                                </td>
                                <td v-if="u.user.profile">
                                    <p>{{ u.user.profile.gender ?? "null" }}</p>
                                    <p>{{ u.user.profile.agerange }}</p>
                                </td>
                               <td>
                                    <VDropdown>
                                        <div>
                                            <p>{{ u.score }}</p>
                                            <p>{{ u.origin_value }}</p>
                                        </div>
                                        <template #popper>
                                            <div>
                                                <form action="" class="flex flex-col" @submit.prevent="formSubmit(u.id)">
                                                    <input type="text" :name="`score${u.id}`" :value="u.score">
                                                    <input type="text" :name="`origin_value${u.id}`" class="" :value="u.origin_value">
                                                    <button class="bg-black hover:bg-ms-magenta-20 py-2 mt-1 text-white">Save</button>
                                                </form>
                                            </div>
                                        </template>
                                    </VDropdown>
                                </td>
                                <td class="text-sm">
                                     <VDropdown>
                                        <div>
                                            <p>{{ moment(u.created_at).format('YYYY-MM-DD HH:mm') }}</p>
                                        </div>
                                        <template #popper>
                                            <div>
                                                <form action="" class="flex flex-col" @submit.prevent="formChangeDate(u.id)">
                                                    <input type="text" :name="`created_at${u.id}`" class="" :value="moment(u.created_at).format('YYYY-MM-DD HH:mm')">
                                                    <button class="bg-black hover:bg-ms-magenta-20 py-2 mt-1 text-white">Save</button>
                                                </form>
                                            </div>
                                        </template>
                                    </VDropdown>
                                </td>
                                <td>
                                    <i @click="delForm(u.id)" class="ms-Icon ms-Icon--Delete"></i>
                                </td>
                            </tr>
                        </table>

                    </div>
                </div>
                </div>

                <div class="w-3/12 ml-12 p-6 top-16 sticky">

                    <!--
                    <section class="flex-table">
                        <div class="flex-table-header">
                            <div class="col">Prize</div>
                            <div class="col text-right">Count / Max</div>
                        </div>
                        <div
                            v-tippy="{ content: `<div class='p-2'>${p.title} <strong class='ml-2'>${p.count} (${calcPerc(p.count, p.limit)})</strong></div>`, followCursor: true }"
                            v-for="p in prizes"
                            class="flex-table-row">
                                <div class="col font-bold">{{ p.title }}</div>
                                <div class="col text-right">{{ p.count }} / {{ p.limit }}</div>
                                <div :style="{ width: calcPerc(p.count, p.limit) }" class="duration-300 left-0 bottom-0 border-b-2 absolute border-ms-orange-10"></div>
                        </div>
                    </section>  
                    -->                     
                
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
    import { DatePicker } from 'v-calendar'

    export default {
        
        props: [
           'leaderboard',
           'prizes',
        ],

        data(){
            return{
                moment: moment,
                form: [
                    {
                        address: ''
                    }
                ],
                filter: null,
                filterdate: null
            }
        },

        beforeMount(){

        },

        computed: {
            fLeaderboard(){

                if(this.filter){
                    let lb = this.leaderboard.filter(item => item.email.includes(this.filter))
                    if(this.filterdate){
                        lb = lb.filter(item => item.created_at.includes(this.filterdate))
                    }
                    return lb
                }
                
                return this.leaderboard

            }
        },

        methods: {
            calcPerc(num, total){
                return Math.round(num * 100 / total) + '%';
            },
            delForm(id){

                let form = {
                    id: id,
                    action: "delete"
                }

                this.$inertia.post('/participant/update', form, {
                    preserveScroll: true
                })
            },
            formChangeDate(id){

                let date = moment(document.querySelector(`[name="created_at${id}"]`).value)

                let form = {
                    id: id,
                    action: "date",
                    created_at: date
                }

                this.$inertia.post('/participant/update', form, {
                    preserveScroll: true
                })
            },
            formSubmit(id){

                let form = {
                    id: id,
                    action: "update",
                    score: document.querySelector(`[name="score${id}"]`).value,
                    origin_value: document.querySelector(`[name="origin_value${id}"]`).value,
                }

                this.$inertia.post('/participant/update', form, {
                    preserveScroll: true
                })
            }
        },

        components: {
            AppLayout,
            JetDialogModal,
            JetDropdown
        }
    }
</script>
