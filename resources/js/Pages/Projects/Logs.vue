<template>
    <app-layout title="Project Settings">

        <template #header>
            <h2 class="text-lg text-ms-gray-160 leading-tight">
                Project <span class="text-ms-gray-50">|</span> <span class="text-gradient">Logs</span> 
                <span class="font-bold ml-2">{{ logs.total }}</span>
            </h2>
            <nav class="flex" v-if="filter">
                <!--
                <button @click="addNewCode=true" class="hover:bg-ms-gray-20 text-ms-gray-160 p-3 flex items-center">
                    <i class="ms-Icon ms-Icon--BulkUpload mr-2"></i> Bulk Add (Soon)
                </button>
                -->
                <a href="/projects/logs" class="hover:bg-ms-gray-20 text-ms-gray-160 p-3">
                    Clear Filters
                </a>
            </nav>
        </template>

        <div>
            <div class="mx-auto py-10 px-4 md:px-6 xl:px-20 flex flex-wrap items-start">

                <div class="w-full md:w-2/3 lg:w-10/12 order-2 md:order-none md:pr-12">

                        <div class="flex flex-wrap">
                            <div class="w-full md:w-4/12 lg:w-2/12 pr-8">                        
                                <line-chart height="260px" :colors="['#00bcf2','#881798']" :data="chart"></line-chart>
                            </div>
                            <div class="w-full md:w-8/12 lg:w-10/12 overflow-auto">

                                <table class="table">
                                    <tr class="header">
                                        <th>Event</th>
                                        <th>Values</th>
                                        <th></th>
                                    </tr>
                                    <tr v-for="log in logs.data">
                                        <td>
                                            <span class="cursor-pointer text-ms-cyan-110 hover:text-ms-magenta-110" @click="filterName(log.name)">{{ log.name }}</span>
                                        </td>
                                        <td v-html="parseValues(log)"></td>
                                        <td>{{ moment(log.created_at).fromNow() }}</td>
                                    </tr>
                                </table>

                            </div>
                        </div>

                        <div class="w-full mt-4 flex justify-end">
                            <a v-if="page>1" 
                                :href="`/projects/logs?page=${parseInt(page)-1}`"
                                class="border border-ms-gray-160 px-4 py-1 hover:bg-ms-gray-40 ml-2 font-bold">Prev</a>
                            <a :href="`/projects/logs?page=${parseInt(page)+1}`"
                                class="border border-ms-gray-160 px-4 py-1 hover:bg-ms-gray-40 ml-2 font-bold">Next</a>
                        </div>

                </div>

                <SubsetOptions />

            </div>
        </div>
    </app-layout>

            <!-- Delete Account Confirmation Modal -->
            <jet-dialog-modal :show="addNewCode" @close="addNewCode=0">
                <template #title>
                    Add Prize
                </template>

                <template #content>

                    <div class="flex">

                        <div class="flex flex-col w-full mr-2">
                            <label for="">Title</label>
                            <input type="text" class="input">
                        </div>

                       <div class="flex flex-col w-full mr-2">
                            <label for="">Periodicity</label>
                            <select name="" id="">
                                <option value="">Daily</option>
                                <option value="">Weekly</option>
                                <option value="">Monthly</option>
                                <option value="">1x Only</option>
                            </select>
                        </div>
                    </div>
                    
                </template>

                <template #footer>
                    <button class="bg-ms-gray-20 hover:bg-ms-gray-30 py-2 px-4">Add QR</button>
                    <button class="mr-auto bg-ms-gray-20 hover:bg-ms-gray-30 py-2 px-4">Add Source</button>
                    <button class="text-white py-2 px-4 mr-2 font-semibold hover:bg-ms-cyan-120 bg-ms-cyan-110">Save</button>
                    <button class="py-2 px-4 font-semibold border border-ms-gray-160 text-ms-gray-160 hover:bg-ms-gray-30">Cancel</button>
                </template>
            </jet-dialog-modal>

</template>

<script>
    import AppLayout from '@/Layouts/AppLayout.vue'
    import SubsetOptions from '@/Pages/Projects/Partials/SubsetOptions.vue'
    import JetDialogModal from '@/Jetstream/DialogModal.vue'
    import moment from 'moment'

    export default {
        
        props: [
            'logs', 'chart', 'filter', 'page'
        ],

        data(){
            return{
                moment: moment,
                addNewCode: false
            }
        },

        methods: {
            filterName(v){
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.set('filter[name]', v);
                window.location.search = decodeURIComponent(urlParams);
            },
            filterValues(v){
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.set('filter[values]', v);
                window.location.search = decodeURIComponent(urlParams);
            },
            parseValues(log){
                
                // run through all values object
                let values = log.values
                let parsed = {}
                for (const [key, value] of Object.entries(values)) {
                    parsed[key] = value
                }
                
                // return key + val, split within spans
                return Object.keys(parsed).map((key, i) => {
                    return `<span class="bg-ms-gray-30 px-2 rounded-2xl text-xs mr-2 mb-2">${key}</span> <span class="text-ms-cyan-20">${parsed[key]}</span>`
                }).join('<br>')

                /*
                switch (log.name) {
                    case 'page_view':
                        return log.values.page_title
                        break;
                    case 'game_start':
                        return log.values.game_name
                        break;
                    case 'game_end':
                        return log.values.game_name
                        break;
                    case 'selfie_take':
                        return log.values.filter
                        break;
                    case 'selfie_select':
                        return log.values.filter
                        break;
                    default:
                        break;
                }
                */
            }
        },

        components: {
            AppLayout,
            SubsetOptions,
            JetDialogModal
        },
    }
</script>
