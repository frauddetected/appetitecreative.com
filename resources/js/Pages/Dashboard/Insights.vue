<template>
    <app-layout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                InsAIghts (Preview)
            </h2>
            <nav class="flex">
            </nav>
        </template>


        <div class="bg-gradient-to-r from-ms-magenta-110 to-ms-cyan-110 flex flex-col justify-center items-center min-h-screen">

            <h1 class="text-2xl -mt-32 text-white text-center" ref="text">
                Over the past <span class="text-3xl font-bold">7 days</span> there was an average of <span class="text-4xl font-bold">{{data.scans_avg_per_day.toFixed(0)}}</span> daily scans. <br><br>
                That represents an overall increase of <span class="text-3xl font-bold">{{data.percentage_increase.toFixed(1)}}%</span> versus the previous week.
            </h1>

            <form @submit.prevent class="px-6 w-full bottom-6 absolute flex items-center">
                <input type="text" class="bg-transparent w-full px-0 py-4 placeholder-white text-white border-0 border-b-2 text-xl border-white" placeholder="Ask me about your data...">
                <button class="bg-white absolute right-6 font-bold uppercase py-3 rounded-lg w-48" @click="chat">Ask Me</button>
            </form>

        </div>
        
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout.vue'
    import Welcome from '@/Jetstream/Welcome.vue'
    import Editor from '@tinymce/tinymce-vue'
    import JetDialogModal from '@/Jetstream/DialogModal.vue'
    import { Configuration, OpenAIApi } from 'openai';

    export default {
        props: ['data'],
        components: {
            AppLayout,
            Welcome,
            'editor': Editor,
            JetDialogModal
        },
        data(){
            return {
                isLoading: false,
                name: '',
                openai: null
            }
        },
        mounted(){

            const configuration = new Configuration({
                apiKey: "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"
            });
            
            this.openai = new OpenAIApi(configuration);
        },
        methods: {
            async chat(){
                const completion = await this.openai.createCompletion({
                    model: "text-davinci-003",
                    prompt: "Hello world",
                });
                console.log(completion.data.choices[0].text);
            }
        }
    }
</script>
