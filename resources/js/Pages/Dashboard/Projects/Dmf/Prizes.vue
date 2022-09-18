<template>
    <app-layout title="Prizes">

        <template #header>
            <h2 class="text-lg text-ms-gray-160 leading-tight">
                Prizes
            </h2>
            <nav class="flex">
                <a href="/prizes?export=true" class="hover:bg-ms-gray-20 text-ms-gray-160 p-3 flex items-center">
                    <i class="ms-Icon ms-Icon--ExcelDocument mr-2"></i> Export
                </a>
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
                                <th>Address</th>
                                <th>Prize</th>
                            </tr>
                            <tr v-for="u in leaderboard" :key="u.id">
                                <td class="w-10">
                                    <p class="text-xs text-gray-400">{{ u.user.id }}</p>
                                </td>
                                <td>
                                    <h4>{{ u.name }}</h4>
                                    <p>{{ u.email }}</p>
                                </td>
                                <td v-if="u.user.profile">
                                     <VDropdown>
                                        <div>
                                            <p>{{ u.user.profile.address ?? "null" }}</p>
                                            <p>{{ u.user.profile.city }}</p>
                                            <p>{{ u.user.profile.postcode }}</p>
                                        </div>
                                        <template #popper>
                                            <div>
                                                <form action="" class="flex flex-col" @submit.prevent="formSubmit(u.user.id)">
                                                    <input type="text" :name="`address${u.user.id}`" :value="u.user.profile.address">
                                                    <input type="text" :name="`city${u.user.id}`" :value="u.user.profile.city">
                                                    <input type="text" :name="`postcode${u.user.id}`" :value="u.user.profile.postcode">
                                                    <button class="bg-black hover:bg-ms-magenta-20 py-2 mt-1 text-white">Save</button>
                                                </form>
                                            </div>
                                        </template>
                                    </VDropdown>
                                </td>
                                <td v-if="u.prize">
                                    {{ u.prize.title }}
                                </td>
                            </tr>
                        </table>

                    </div>
                </div>
                </div>

                <div class="w-3/12 ml-12 shadow-2xl w-full p-6 top-16 sticky">

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
        ],

        data(){
            return{
                moment: moment,
                form: [
                    {
                        address: ''
                    }
                ]
            }
        },

        beforeMount(){

        },

        methods: {
            calcPerc(num, total){
                return Math.round(num * 100 / total) + '%';
            },
            formSubmit(id){

                let form = {
                    id: id,
                    address: document.querySelector(`[name="address${id}"]`).value,
                    city: document.querySelector(`[name="city${id}"]`).value,
                    postcode: document.querySelector(`[name="postcode${id}"]`).value
                }

                this.$inertia.post('/participant/update', form)
            }
        },

        components: {
            AppLayout,
            JetDialogModal,
            JetDropdown
        }
    }
</script>
