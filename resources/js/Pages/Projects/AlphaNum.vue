<template>
    <app-layout title="Project Settings">

        <template #header>
            <h2 class="text-lg text-ms-gray-160 leading-tight">
                Project <span class="text-ms-gray-50">|</span> <span class="text-gradient">Alpha Num Codes</span>
            </h2>
            <nav class="flex" v-if="$page.props.user.role.level <= 1">
                <button 
                    @click="addNewCode=true" class="hover:bg-ms-gray-20 text-ms-gray-160 p-3 flex items-center">
                    <i class="ms-Icon ms-Icon--Add mr-2"></i> Generate
                </button>
            </nav>
        </template>

        <div>
            <div class="mx-auto py-10 px-20 flex items-start">

                <div class="w-10/12">

                <div class="flex">
                    <div class="w-4/12 pr-12">
                        <h2 class="text-xl">Alpha Num Codes</h2>
                        <p class="mt-5">
                            {{ totalCodes.toLocaleString() }} Total Codes <br>
                            {{ burnedCodes.toLocaleString() }} Burned Codes
                        </p>
                        <nav class="flex gap-x-4 mt-4 pt-4 border-t text-xs">
                            <a target="_blank" class="text-ms-magenta-10" :href="`https://query.appetite.link/api/alphanum/${project_id}/view?type=array`">API</a>
                            <a target="_blank" class="text-ms-magenta-10" :href="`https://query.appetite.link/api/alphanum/${project_id}/view?type=csv`">CSV</a>
                            <a target="_blank" class="text-ms-magenta-10" :href="`https://query.appetite.link/api/alphanum/${project_id}/view?type=txt`">Text</a>
                        </nav>
                    </div>
                    <div class="w-8/12">

                        <table class="table">
                            <tr class="header">
                                <th class="text-center">Title</th>
                                <th class="text-center">Amount</th>
                                <th class="text-center">Burned</th>
                                <th></th>
                            </tr>
                            <tr v-for="alphanum in codes.data">
                                <td class="text-center">
                                    {{ alphanum.title }}
                                </td>
                                <td class="text-center">
                                    {{ alphanum.total.toLocaleString('en-US') }}
                                </td>
                                <td class="text-center">
                                    {{ alphanum.burned.toLocaleString('en-US') }}
                                </td>
                                <td class="text-center flex gap-3 text-sm">
                                    <a target="_blank" class="text-ms-magenta-10" :href="`https://query.appetite.link/api/alphanum/${project_id}/view?type=array&filter=${encodeURIComponent(alphanum.title).replace(/%20/g, '+')}`">API</a>
                                    <a target="_blank" class="text-ms-magenta-10" :href="`https://query.appetite.link/api/alphanum/${project_id}/view?type=csv&filter=${encodeURIComponent(alphanum.title).replace(/%20/g, '+')}`">CSV</a>
                                </td>
                            </tr>
                        </table>

                    </div>
                </div>
                </div>

                <SubsetOptions />

            </div>
        </div>
    </app-layout>
            
            <!-- Generate Codes -->
            <jet-dialog-modal maxWidth="4xl" :show="addNewCode" @close="addNewCode=0">
                <template #title>
                    Generate Codes
                </template>

                <template #content>

                    <div class="flex">
                        <div class="flex flex-col w-full mr-2">
                            <label for="">Title</label>
                            <input v-model="form.title" type="text" class="input">
                        </div>
                        <div class="flex flex-col w-full mr-2">
                            <label for="">Digits</label>
                            <input v-model="form.digits" type="number" class="input">
                        </div>
                        <div class="flex flex-col w-full">
                            <label for="">Quantity</label>
                            <input v-model="form.quantity" type="number" class="input">
                        </div>
                    </div>
                    
                </template>

                <template #footer>
                    <div>
                        <button v-if="!generating" @click="save" class="text-white py-2 px-4 mr-2 font-semibold hover:bg-ms-cyan-120 bg-ms-cyan-110">Save</button>
                        <button v-else class="text-white py-2 px-4 mr-2 font-semibold hover:bg-ms-cyan-120 bg-ms-cyan-110">Generating...</button>

                        <button @click="close" class="py-2 px-4 font-semibold border border-ms-gray-160 text-ms-gray-160 hover:bg-ms-gray-30">Cancel</button>
                    </div>
                </template>
                
            </jet-dialog-modal>

</template>

<script>
    import AppLayout from '@/Layouts/AppLayout.vue'
    import SubsetOptions from '@/Pages/Projects/Partials/SubsetOptions.vue'
    import JetDialogModal from '@/Jetstream/DialogModal.vue'
    import VueMultiselect from 'vue-multiselect'
    import VueQrcode from '@chenfengyuan/vue-qrcode'
    import ColorPicker from '@/Components/ColorPicker.vue'

    export default {
        
        props: [
            'project_id',
            'codes',
            'totalCodes',
            'burnedCodes',
        ],

        data(){
            return{
                generating: false,
                addNewCode: false,
                form: this.$inertia.form({
                    title: '',
                    digits: 5,
                    quantity: 1_000
                }),
            }
        },

        mounted(){
          console.log("this.codes",this.codes)
        },

        methods: {
            save(){
                this.generating = true
                this.form.post(route('projects.alphanum.store'), {
                    preserveState: true,
                    onSuccess: () => {
                        this.addNewCode = false
                        this.generating = false   
                        this.form.reset()
                        this.codes = this.project.alphanum                 
                    }
                })
            },
            close(){
                this.addNewCode = false
            },
        },

        components: {
            AppLayout,
            SubsetOptions,
            JetDialogModal,
            VueMultiselect,
            VueQrcode,
            ColorPicker
        },
    }
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>