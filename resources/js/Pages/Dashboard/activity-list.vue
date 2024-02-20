<template>
    <app-layout title="Users Listing">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Activity Log
            </h2>
        </template>
        <div>
            <input type="hidden" :value="id" id="user-id"/>
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
        props: [
            'id'
        ],
        components: {
            AppLayout,
        },
        methods: {
        },
        mounted() {
            $(document).ready(function() {
                var userId = $('#user-id').val();

                $('#data-table').DataTable({
                    processing: true,
                    serverSide: true,
                    paging: true, // Enable pagination
                    ajax: {
                        url: '/ajax-activity-log',
                        type: 'post',
                        data: {
                            user_id: userId, // Add the user_id to the Ajax request data
                        },
                    },
                    columns: [
                        { data: 'created_at', title: 'Created At' },
                        { data: 'user_name', title: 'User Name' },
                        { data: 'subject_type', title: 'Subject Type' },
                        { data: 'event', title: 'Event' },
                        { data: 'description', title: 'Description' },
                        { data: 'log_data', title: 'Log Data' },
                    ],
                    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
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
