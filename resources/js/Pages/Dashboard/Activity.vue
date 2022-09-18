<template>
    <app-layout title="Project Settings">

        <template #header>
            <h2 class="text-lg text-ms-gray-160 leading-tight">
                Activity
            </h2>
            <nav class="flex">
            </nav>
        </template>

        <div>
            <div class="mx-auto py-10 px-20 flex items-start">

                <div class="w-10/12">

                <div class="flex">
                    <div class="w-4/12 pr-12">                        
                        
                    </div>
                    <div class="w-8/12">

                        <table class="table">
                            <tr class="header">
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                            <tr v-for="activity in auth">
                                <td>{{ activity.user.name }}</td>
                                <td>{{ activity.login_at }}</td>
                                <td>{{ activity.logout_at }}</td>
                            </tr>
                        </table>

                    </div>
                </div>
                </div>

            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout.vue'
    import JetDialogModal from '@/Jetstream/DialogModal.vue'
    import moment from 'moment'

    export default {
        
        props: [
            'auth'
        ],

        data(){
            return{
                moment: moment
            }
        },

        methods: {
        },

        components: {
            AppLayout,
            JetDialogModal
        },
    }
</script>
