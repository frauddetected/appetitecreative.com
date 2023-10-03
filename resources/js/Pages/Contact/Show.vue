<template>
    <app-layout title="Contact Us">
        <div>
            <div class="mx-auto py-10 px-20 flex items-start">
                <div class="w-full">
                    <div class="flex">
                        <div class="w-4/12 pr-12">
                            <h2 class="text-xl">Contact Us</h2>
                            <p>Please Contact me if you want to choose plan Silver / Gold.</p>
                        </div>
                        <div class="w-8/12">
                            <div class="flex justify-between">
                                <div class="flex flex-col w-5/12">
                                    <label for="">Email</label>
                                    <input v-model="form.email" type="text" class="input" readonly>
                                </div>
                                <div class="flex flex-col w-5/12">
                                    <label for="">Name</label>
                                    <input v-model="form.name" type="text" class="input">
                                    <div class="mt-2" v-if="error">
                                        <p class="text-sm text-red-600">
                                            {{ errorMsg.name }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-between">
                                <div class="flex flex-col w-5/12">
                                    <label for="">Contact Number</label>
                                    <input v-model="form.phone" type="text" class="input">
                                    <div class="mt-2" v-if="error">
                                        <p class="text-sm text-red-600">
                                            {{ errorMsg.phone }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex flex-col w-5/12">
                                    <label for="">Subject</label>
                                    <input v-model="form.subject" type="text" class="input">
                                    <div class="mt-2" v-if="error">
                                        <p class="text-sm text-red-600">
                                            {{ errorMsg.subject }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex">
                                <div class="flex flex-col w-full">
                                    <label for="">Message</label>
                                    <textarea v-model="form.message" class="" rows="4"></textarea>
                                </div>
                            </div>
                            <div class="flex items-center justify-end py-3 text-right  sm:rounded-bl-md sm:rounded-br-md">
                                <button type="submit" class="border border-ms-gray-160 px-4 py-1 hover:bg-ms-gray-40 ml-2 font-bold" @click="submitContact" v-show="submitProcess"> Send </button>
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
    import SubsetOptions from '@/Pages/Projects/Partials/SubsetOptions.vue'
    import JetButton from '@/Jetstream/Button.vue'
    import JetActionMessage from '@/Jetstream/ActionMessage.vue'
    import { Calendar, DatePicker } from 'v-calendar'

    export default {
        
        props: [
            'plan_type',
            'email'
        ],

        data(){
            return{
                api_token: '',
                user_role: [],
                form: this.$inertia.form({
                    name: '',
                    phone: '',
                    message: '',
                    plan_type:'',
                    email:'',
                    subject:''
                }),
                error:false,
                errorMsg:{
                    name:'',
                    phone:'',
                    subject:''
                },
                submitProcess:true
            }
        },

        watch: {
            token: function(v){
                this.api_token = v
            }
        },

        created() {
            this.form.plan_type = this.plan_type
            this.form.email = this.email
        },

        methods: 
        {
            submitContact(){
                this.error = false;
                this.errorMsg.name = '';
                this.errorMsg.phone = '';
                this.errorMsg.subject = '';
                if(this.form.name == ''){
                    this.errorMsg.name = 'Please enter Name.';
                    this.error = true;
                }
                if(this.form.phone == ''){
                    this.errorMsg.phone = 'Please enter Contact Number.';
                    this.error = true;
                }
                else{
                    if(!this.isValidPhoneNumber(this.form.phone)){
                        this.errorMsg.phone = 'Please enter Valid Number.';
                        this.error = true;
                    }
                }
                if(this.form.subject == ''){
                    this.errorMsg.subject = 'Please enter subject.';
                    this.error = true;
                }
                if(!this.error){
                    this.submitProcess = false;
                    this.form.post(route('contact.store'), {
                        errorBag: 'submitContact',
                        onSuccess: () => {
                            this.form.name = '';
                            this.form.phone = '';
                            this.form.message = '';
                            this.form.subject = '';
                            this.submitProcess = true;
                        },
                        onError: () => {
                            this.submitProcess = true;
                        }
                    })
                }
            },
            isValidPhoneNumber(phoneNumber) {
                // Define a regular expression for a typical US phone number format
                const phoneRegex = /^(?:\+?1[-.\s]?)?\(?(\d{3})\)?[-.\s]?(\d{3})[-.\s]?(\d{4})$/;

                // Test if the phoneNumber matches the regex pattern
                return phoneRegex.test(phoneNumber);
            }
        },

        components: {
            AppLayout,
            SubsetOptions,
            Calendar,
            DatePicker
        },
    }
</script>
