<template>
    <app-layout title="Project Settings">

        <template #header>
            <h2 class="text-lg text-ms-gray-160 leading-tight">
                Project <span class="text-ms-gray-50">|</span> <span class="text-gradient">Sharing</span>
            </h2>
            <nav class="flex">
                <button @click="save" class="hover:bg-ms-gray-20 text-ms-gray-160 p-3 flex items-center">
                    Save
                </button>
            </nav>
        </template>

        <div>
            <div class="mx-auto py-10 px-4 md:px-6 xl:px-20 flex flex-wrap items-start">

                <div class="w-full md:w-2/3 lg:w-10/12 order-2 md:order-none md:pr-12">

                    <div class="flex flex-wrap">
                        <div class="w-full md:w-4/12 lg:w-2/12 pr-8">
                            <h2 class="text-xl">Style</h2>
                            <p>Customize the styling</p>
                        </div>
                        <div class="w-full md:w-8/12 lg:w-10/12 overflow-auto mt-4 md:mt-0">
                            <div class="flex">
                                <div class="flex flex-col w-6/12 relative">
                                    <label for="">Primary Color</label>
                                    <input type="text" maxlength="7" v-model="form.style.primary_color" class="input w-full">
                                    <ColorPicker className="mr-2 absolute bottom-[7px] right-[-2px]" :color="form.style.primary_color" v-model="form.style.primary_color" />
                                </div>
                                <div class="flex flex-col ml-4 w-6/12 relative">
                                    <label for="">Secondary Color</label>
                                    <input type="text" maxlength="7" color="" v-model="form.style.secondary_color" class="input w-full">
                                    <ColorPicker className="mr-2 absolute bottom-[7px] right-[-2px]" :color="form.style.secondary_color" v-model="form.style.secondary_color" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-6 sm:my-12" />

                    <div class="flex flex-wrap md:flex-nowrap">
                        <div class="w-full md:w-4/12 lg:w-2/12 pr-8">
                            <h2 class="text-xl">Messages</h2>
                            <p>The messages to be shared</p>
                        </div>
                        <div class="w-full md:w-8/12 lg:w-10/12 overflow-auto mt-4 md:mt-0">

                        <ul v-if="project.i18n" class="-mt-6">
                            <li v-for="country,i in project.i18n.countries">
                                

                                <div class="flex w-full border-b py-6">
                                    <h2 class="text-xl w-4/12 border-r mr-6">{{ country.name }}</h2>
                                    <div class="flex flex-col w-8/12">
                                        
                                        <div v-for="language in project.i18n.languages" class="flex flex-col mb-4">
                                            <label for="">{{ language.name }}</label>
                                            <input type="text" class="input mt-1" placeholder="Message to share" v-model="form.messages.share[country.code + '_' + language.code]">
                                            <input type="text" class="input mt-2" placeholder="Go to project" v-model="form.messages.go[country.code + '_' + language.code]">
                                        </div>

                                    </div>
                                </div>


                            </li>   
                        </ul>
                        <div v-else>
                            <p>You need to add countries first.</p>
                        </div>

                        </div>
                    </div>

                </div>

                <SubsetOptions />

            </div>
        </div>
    </app-layout>

</template>

<script>
    import AppLayout from '@/Layouts/AppLayout.vue'
    import SubsetOptions from '@/Pages/Projects/Partials/SubsetOptions.vue'
    import JetDialogModal from '@/Jetstream/DialogModal.vue'
    import ColorPicker from '@/Components/ColorPicker.vue'

    export default {
        
        props: [
            'project'
        ],

        data(){
            return{
                form: {
                    style: {
                        primary_color: '',
                        secondary_color: '',   
                    },
                    messages: {
                        share: {},
                        go: {}
                    }
                }
            }
        },

        methods: {
            save(){
                this.$inertia.post(route('projects.sharing.store'), this.form, {
                    preserveState: true
                })
            }
        },

        created(){
            if(this.project.sharing){
                this.form.style = this.project.sharing.style ? this.project.sharing.style : { primary_color: '', secondary_color: '' }
                this.form.messages = this.project.sharing.messages ? this.project.sharing.messages : { share: {}, go: {} }
            }
        },

        components: {
            AppLayout,
            SubsetOptions,
            JetDialogModal,
            ColorPicker
        },
    }
</script>
