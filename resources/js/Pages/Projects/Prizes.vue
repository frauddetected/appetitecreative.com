<template>
    <app-layout title="Project Settings">

        <template #header>
            <h2 class="text-lg text-ms-gray-160 leading-tight">
                
                Project <span class="text-ms-gray-50">|</span> <span class="text-gradient">Prizes</span>

                <button v-if="!module || module.is_active == false" @click="$modules.enable('prizes')" class="hover:bg-ms-cyan-20 bg-ms-cyan-10 text-white ml-6 px-3 py-3 text-sm">
                    Enable
                </button>
                <button v-else @click="$modules.disable('prizes')" class="hover:bg-ms-magenta-20 bg-ms-magenta-10 text-white ml-6 px-3 py-3 text-sm">
                    Disable
                </button>

            </h2>
            <nav class="flex">
                <button @click="addNewPrize=true" class="hover:bg-ms-gray-20 text-ms-gray-160 p-3 flex items-center">
                    <i class="ms-Icon ms-Icon--Add mr-2"></i> Add
                </button>
            </nav>
        </template>

        <div>
            <div class="mx-auto py-10 px-20 flex items-start">

                <div class="w-10/12">

                <div class="flex">
                    <div class="w-4/12 pr-12">
                        <h2 class="text-xl">Prizes</h2>
                        <p>...</p>
                        <p></p>
                    </div>
                    <div class="w-8/12">

                        <table class="table">
                            <tr class="header">
                                <th>Title</th>
                                <th>Periodicity</th>
                                <th>Limit</th>
                                <th></th>
                            </tr>
                            <tr v-for="prize in project.prizes">
                                <td>{{ prize.title }}</td>
                                <td>{{ prize.periodicity }}</td>
                                <td>{{ prize.limit }}</td>
                                <td></td>
                            </tr>
                        </table>

                    </div>
                </div>
                </div>

                <SubsetOptions />

            </div>
        </div>
    </app-layout>

            <!-- Delete Account Confirmation Modal -->
            <jet-dialog-modal :show="addNewPrize" @close="addNewPrize=0">
                <template #title>
                    Add Prize
                </template>

                <template #content>

                    <div class="flex">

                        <div class="flex flex-col w-full mr-2">
                            <label for="">Title</label>
                            <input type="text" class="input" v-model="form.title">
                        </div>

                        <div class="flex flex-col w-2/12 mr-2">
                            <label for="">Limit</label>
                            <input type="number" class="input" v-model="form.limit">
                        </div>

                       <div class="flex flex-col w-6/12 mr-2">
                            <label for="">Periodicity</label>
                            <select v-model="form.periodicity">
                                <option value="none">None</option>
                                <option value="once">1x Only</option>
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>
                    </div>
                    
                </template>

                <template #footer>
                    <button class="bg-ms-gray-20 hover:bg-ms-gray-30 py-2 px-4">Add QR</button>
                    <button class="mr-auto bg-ms-gray-20 hover:bg-ms-gray-30 py-2 px-4">Add Source</button>
                    <button @click="save" class="text-white py-2 px-4 mr-2 font-semibold hover:bg-ms-cyan-120 bg-ms-cyan-110">Save</button>
                    <button class="py-2 px-4 font-semibold border border-ms-gray-160 text-ms-gray-160 hover:bg-ms-gray-30">Cancel</button>
                </template>

            </jet-dialog-modal>

</template>

<script>
    import AppLayout from '@/Layouts/AppLayout.vue'
    import SubsetOptions from '@/Pages/Projects/Partials/SubsetOptions.vue'
    import JetDialogModal from '@/Jetstream/DialogModal.vue'

    export default {
        
        props: [
            'project',
            'module'
        ],

        data(){
            return{
                addNewPrize: false,
                form: this.$inertia.form({
                    title: '',
                    limit: '',
                    periodicity: ''
                })
            }
        },

        methods: {
            save(){
                this.form.post(route('projects.prizes.store'), {
                    preserveState: true,
                    onSuccess: () => {
                        this.addNewPrize = false
                        this.form.reset()
                        this.prizes = this.project.prizes                    
                    }
                })
            }
        },

        components: {
            AppLayout,
            SubsetOptions,
            JetDialogModal
        },
    }
</script>
