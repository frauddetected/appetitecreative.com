<template>
    <app-layout title="Users Listing">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                User List
            </h2>
        </template>
        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 overflow-auto">
                <table id="data-table" class="display" cellspacing="0" width="100%">
                    <thead>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout.vue'
    import $ from 'jquery';
    import 'datatables.net';
    import 'datatables.net-dt/css/jquery.dataTables.css';

    export default {
        components: {
            AppLayout,
        },
        methods: {
            toggle(id){
                this.$inertia.put(`/user/can-subscription/${id}`);
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
                        url: '/user-manage-list',
                        type: 'post', // or 'GET' depending on your server-side setup
                        // Add any necessary data parameters here
                    },
                    columns: [
                        { data: 'email', title: 'Email' },           // Column for user email
                        { data: 'name', title: 'Name' },             // Column for user name
                        { data: 'role', title: 'Role' },             // Column for user role
                        { data: 'created_at', title: 'Created At' }, // Column for user created_at
                        {
                            data: 'id',
                            title: 'Overwrite Subscription',
                            render: function (data, type, row) {
                                var subscriptionStatus = (row.overwrite_subscription === 'yes') ? "checked" : "";
                                var buttonClass = (row.overwrite_subscription === 'yes') ? "!bg-green-500 subscriber-button on" : "subscriber-button off";

                                var sliderClass = (row.overwrite_subscription === 'yes') ? "!bg-green-500" : "";

                                return `<label class="switch"><input type="checkbox" ${subscriptionStatus}  class="${buttonClass}" data-user-id="${data}"><span class="${sliderClass} slider round"></span></label>`;
                            },
                        },
                        { data: 'plan_type', title: 'Plan Type' }, // Column for user created_at
                    ],
                    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                });
                $('#data-table').on('change', '.subscriber-button', function () {
                    var button = $(this);
                    var userId = button.data('user-id');
                    var isChecked = $(this).prop('checked');
                    if(confirm("Are you sure to perform this action?")){
                        if (isChecked) {
                            button.addClass('on !bg-green-500');
                            button.parent().find('.slider').addClass('!bg-green-500');
                        } else {
                            button.removeClass('!bg-green-500');
                            button.parent().find('.slider').removeClass('!bg-green-500');
                        }
                        vm.toggle(userId);
                    }
                    else{
                        if (isChecked) {
                            button.prop('checked', false);
                        }
                        else{
                            button.prop('checked', true);
                        }
                    }                    
                });
            });
        },
    };
</script>
<style>
    #data-table_length select {
        width: 100px; /* Adjust the width to your preference */
    }
    .switch {
        position: relative;
        display: inline-block;
        width: 56px;
        height: 28px;
        margin-left: 10%;
    }

    .switch input { 
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked + .slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>
