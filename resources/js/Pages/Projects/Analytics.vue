<template>
    <app-layout title="Project Settings">

        <template #header>
            <h2 class="text-lg text-ms-gray-160 leading-tight flex items-center">
                
                Project <span class="text-ms-gray-50 mx-2">|</span> <span class="text-gradient">Analytics</span>
                
                <button v-if="!module || module.is_active == false" @click="$modules.enable('analytics')" class="hover:bg-ms-cyan-20 bg-ms-cyan-10 text-white ml-6 px-3 py-3 text-sm">
                    Enable
                </button>
                <button v-else @click="$modules.disable('analytics')" class="hover:bg-ms-magenta-20 bg-ms-magenta-10 text-white ml-6 px-3 py-3 text-sm">
                    Disable
                </button>

            </h2>
            <nav class="flex">
                <button @click="save" class="hover:bg-ms-gray-20 text-ms-gray-160 p-3">
                    Save
                </button>
            </nav>
        </template>

        <div>
            <div class="mx-auto py-10 px-20 flex items-start">

                <div class="w-10/12">

                <div class="flex">
                    <div class="w-4/12 pr-12">
                        <h2 class="text-xl">Analytics Platform</h2>
                        <p>Choose between the popular Google platform and a lighter privacy oriented solution.</p>
                    </div>
                    <div class="w-8/12">
                        <form action="">

                            <div class="flex flex-col">
                                <label class="" for="">Select Analytics</label>
                                <ul class="flex">
                                    <label for="plausible" class="bg-white cb-container p-8 w-full font-normal shadow-ms rounded-md hover:bg-ms-gray-20 m-2">
                                        <input id="plausible" type="radio" value="plausible" v-model="form.platform">
                                        <img class="h-10 mx-auto" src="https://d33wubrfki0l68.cloudfront.net/7fe1e9eb24029471f1b2c53cbcf8c4ad0bc87af7/0b5a6/assets/images/icon/plausible_logo.compressed.png" alt="">
                                        <p class="text-sm mt-6">Simple, lightweight and privacy-friendly Google Analytics alternative. website analytics tool. No cookies and fully compliant with GDPR, CCPA and PECR.</p>
                                        <p class="text-sm mt-2 text-ms-gray-80">Does NOT provide: cities / most active times of day.</p>
                                        <div class="checkmark"></div>
                                    </label>
                                    <label for="google" class="bg-white cb-container p-8 w-full font-normal shadow-ms rounded-md hover:bg-ms-gray-20 m-2">
                                        <input id="google" type="radio" value="google" v-model="form.platform">
                                        <img class="h-10 mx-auto" src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/89/Logo_Google_Analytics.svg/1200px-Logo_Google_Analytics.svg.png" alt="">
                                        <p class="text-sm mt-6">Google Analytics is a web analytics service offered by Google that tracks and reports website traffic, currently as a platform inside the Google Marketing Platform brand.</p>
                                        <div class="checkmark"></div>
                                    </label>
                                </ul>
                            </div>

                            <!-- Plausible -->
                            <div class="mt-5" v-if="form.platform == 'plausible'">
                                <div class="flex">
                                    <div class="flex flex-col w-1/2 mr-2">
                                        <label for="">Domain Name</label>
                                        <input type="text" class="input" v-model="form.details.domain_name">
                                    </div>
                                    <div class="flex flex-col w-1/2">
                                        <label for="">Auth Code</label>
                                        <input type="text" class="input" v-model="form.details.auth_code">
                                    </div>
                                </div>
                                <button @click="createPlausible">Create</button>
                            </div>

                            <!-- Google Analytics -->
                            <div class="mt-5" v-if="form.platform == 'google'">
                                <div class="flex">
                                    <div class="flex flex-col w-full">
                                        <label for="">View ID</label>
                                        <input type="text" class="input" v-model="form.details.view_id">
                                    </div>
                                    <div class="flex flex-col ml-4 w-full">
                                        <label for="">Map</label>
                                        <input type="text" class="input" v-model="form.details.map">
                                    </div>
                                </div>
                            </div>

                        </form>                    
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

    export default {
        
        props: [
            'team',
            'availableRoles',
            'permissions',
            'module',
            'project'
        ],

        data(){
            return{
                form: {
                    platform: '',
                    details: {
                        domain_name: '',
                        auth_code: '',
                        view_id: '',
                        map: ''
                    }
                }
            }
        },

        created(){
            if(this.project.analytics){
                this.form.platform = this.project.analytics.platform
                this.form.details = this.project.analytics.details
            }            
        },

        methods: {
            createPlausible(){
                this.$inertia.post(route('projects.analytics.plausible', this.$page.props.project.id), this.form, {
                    preserveState: true
                })
            },
            save(){
                this.$inertia.post(route('projects.analytics.store', this.$page.props.project.id), this.form, {
                    preserveState: true
                })
            }
        },

        components: {
            AppLayout,
            SubsetOptions
        },
    }
</script>
