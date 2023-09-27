<template>
    <app-layout title="Contact Listing">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Contact Us
            </h2>
        </template>
        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <table id="data-table" class="display" cellspacing="0" width="100%">
                    <thead>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </app-layout>
    <!-- View  -->
    <jet-contact-view-dialog-modal maxWidth="2xl" :show="viewContact" @close="viewContact=0">
        <template #title>
            <div class="bg-black text-white p-2 text-center">
                {{ contactData.name }} ({{ contactData.plan_type }})
            </div>
        </template>

        <template #content>
            <div class="bg-[#efefef] p-6 border-b border-gray-300">
                <div class="flex gap-8">
                    <label for="" class="m-0 w-8">Phone</label>
                    <p>:</p>
                    <p class="text-sm">{{ contactData.phone }}</p>
                </div>
                <div class="flex gap-8 mt-2.5">
                    <label for="" class="m-0 w-8">Subject</label>
                    <p>:</p>
                    <p class="text-sm">{{ contactData.subject }}</p>
                </div>
                <div class="flex gap-8 mt-2.5">
                    <label for="" class="m-0 w-8">Message</label>
                    <p>:</p>
                    <p class="text-sm">{{ contactData.message }}</p>
                </div>
            </div>
        </template>
        <template #footer>
            <div>
                <button @click="close" class="py-2 px-4 font-semibold border border-ms-gray-160 text-ms-gray-160 hover:bg-ms-gray-30">Cancel</button>
            </div>
        </template>
    </jet-contact-view-dialog-modal>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout.vue'
    import JetContactViewDialogModal from '@/Jetstream/ContactViewDialogModal.vue'
    import $ from 'jquery';
    import 'datatables.net';
    import 'datatables.net-dt/css/jquery.dataTables.css';

    export default {
        data(){
            return{
                viewContact: false,
                contactData: {},
            }
        },
        components: {
            AppLayout,
            JetContactViewDialogModal,
        },
        methods: {
            handleContactView(id){
                this.viewContact = true
                axios.get(`/contact/${id}`)
                    .then(response => {
                        // Handle the API response data here
                        this.contactData.name = response.data.name;
                        this.contactData.phone = response.data.phone;
                        this.contactData.plan_type = response.data.plan_type;
                        this.contactData.subject = response.data.subject;
                        this.contactData.message = response.data.message;

                        // Implement your logic to display or process the data
                    })
                    .catch(error => {
                        // Handle any errors that occur during the API request
                        console.error('API Error:', error);

                        // Implement error handling or display error messages
                    });
            },
            close(){
                this.viewContact = false
            },
        },
        mounted() {
            const vm = this;

            // Initialize the DataTable
            $(document).ready(function() {
                $('#data-table').DataTable({
                    processing: true,
                    serverSide: true,
                    paging: true, // Enable pagination
                    ajax: {
                        url: '/contact-manage-list',
                        type: 'post', // or 'GET' depending on your server-side setup
                        // Add any necessary data parameters here
                    },
                    columns: [
                        { data: 'email', title: 'Email' },           // Column for user email
                        { data: 'name', title: 'Name' },             // Column for contact name
                        { data: 'phone', title: 'Phone' },           // Column for contact phone
                        { data: 'plan_type', title: 'Plan Type' },   // Column for plan type
                        { data: 'subject', title: 'Subject' },       // Column for subject
                        {
                            data: 'id',
                            title: 'Action',
                            render: function (data) {
                                return `<button class="view-button" data-contact-id="${data}"><i class="ms-Icon ms-Icon--View"></i></button>`;
                            },
                        },
                    ],
                    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                });
                $('#data-table').on('click', '.view-button', function () {
                    const contactId = $(this).data('contact-id');
                    vm.handleContactView(contactId);
                });
            });
        },
    };
</script>
