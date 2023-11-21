<template>
    <app-layout title="Project Settings">

        <template #header>
            <h2 class="text-lg text-ms-gray-160 leading-tight">
                Project <span class="text-ms-gray-50">|</span> <span class="text-gradient">Sources</span>
            </h2>
            <nav class="flex" v-if="$page.props.user.role.level <= 1">
                <button @click="addNewCode=true" class="hover:bg-ms-gray-20 text-ms-gray-160 p-3 flex items-center">
                    <i class="ms-Icon ms-Icon--Add mr-2"></i> Add
                </button>
            </nav>
        </template>

        <div>
            <div class="mx-auto py-10 px-4 md:px-6 xl:px-20 flex flex-wrap items-start">

                <div class="w-full md:w-2/3 lg:w-10/12 order-2 md:order-none md:pr-12">

                <div class="flex flex-wrap">
                    <div class="w-full md:w-4/12 lg:w-2/12 pr-8">
                        <h2 class="text-xl">Sources</h2>
                        <p>...</p>
                        <p></p>
                    </div>
                    <div class="w-full md:w-8/12 lg:w-10/12 overflow-auto">

                        <table class="table">
                            <tr class="header">
                                <th></th>
                                <th>Title</th>
                                <th class="text-center">Count</th>
                                <th class="text-center"></th>
                            </tr>
                            <tr v-for="source in project.sources">
                                <td>#{{ source.id }}</td>
                                <td>{{ source.title }}</td>
                                <td class="text-center">
                                
                                   {{ source.pivot.count }}
                                
                                </td>
                                <td class="text-center">
                                    <span v-if="$page.props.user.role.level <= 1" @click="toggleSource(source.id)"><i class="ms-Icon ms-Icon--Delete"></i></span>
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

            <!-- Delete Account Confirmation Modal -->
            <jet-dialog-modal :show="addNewCode" @close="addNewCode=0">
                <template #title>
                    Add Source
                </template>

                <template #content>

                    <div class="flex">

                        <div class="flex flex-col w-full">                            
                            <VueMultiselect
                                v-model="form.source"
                                :options="sources"
                                :multiple="false"
                                :close-on-select="true"
                                placeholder="Select one"
                                track-by="id"
                                label="title"
                            />
                            <span class="text-red-500">{{ sourceError }}</span>
                        </div>

                    </div>
                    
                </template>

                <template #footer>
                    <button @click="save" class="text-white py-2 px-4 mr-2 font-semibold hover:bg-ms-cyan-120 bg-ms-cyan-110">Save</button>
                    <button @click="close" class="py-2 px-4 font-semibold border border-ms-gray-160 text-ms-gray-160 hover:bg-ms-gray-30">Cancel</button>
                </template>
            </jet-dialog-modal>

</template>

<script>
    import AppLayout from '@/Layouts/AppLayout.vue'
    import SubsetOptions from '@/Pages/Projects/Partials/SubsetOptions.vue'
    import JetDialogModal from '@/Jetstream/DialogModal.vue'
    import VueMultiselect from 'vue-multiselect'

    export default {
        
        props: [
            'project',
            'sources'
        ],

        data(){
            return{
                sourceError: '',
                valid: true,
                addNewCode: false,
                form: this.$inertia.form({
                    source: {}
                })
            }
        },

        mounted(){
            
        },

        methods: {
            save(){
                this.valid = true;
                if(Object.keys(this.form.source).length === 0){
                    this.valid = false;
                    this.sourceError = 'Please select source.'
                }
                if(this.valid){
                    this.form.post(route('projects.sources.store'), {
                        errorBag: 'save',
                        preserveState: true,
                        onSuccess: () => {
                            this.addNewCode = false
                        }
                    })
                }
            },
            close(){
                this.addNewCode = false
            },
            toggleSource(id){
                this.$inertia.post(route('projects.sources.toggle', { id }))
            }
        },

        components: {
            AppLayout,
            SubsetOptions,
            JetDialogModal,
            VueMultiselect
        },
    }
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>