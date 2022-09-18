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
            <div class="mx-auto py-10 px-20 flex items-start">

                <div class="w-10/12">

                <div class="flex">
                    <div class="w-4/12 pr-12">
                        <h2 class="text-xl">Sources</h2>
                        <p>...</p>
                        <p></p>
                    </div>
                    <div class="w-8/12">

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
                        </div>

                    </div>
                    
                </template>

                <template #footer>
                    <button @click="save" class="text-white py-2 px-4 mr-2 font-semibold hover:bg-ms-cyan-120 bg-ms-cyan-110">Save</button>
                    <button class="py-2 px-4 font-semibold border border-ms-gray-160 text-ms-gray-160 hover:bg-ms-gray-30">Cancel</button>
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
                this.form.post(route('projects.sources.store'), {
                    preserveState: true,
                    onSuccess: () => {
                        this.addNewCode = false
                    }
                })
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