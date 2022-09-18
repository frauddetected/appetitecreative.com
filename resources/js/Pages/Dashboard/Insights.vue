<template>
    <app-layout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Insights (Preview)
            </h2>
            <nav class="flex">
            </nav>
        </template>


        <div class="bg-gradient-to-r from-ms-magenta-110 p-56 to-ms-cyan-110 flex flex-col justify-center items-center min-h-screen">

            <h1 class="text-2xl text-white text-center" ref="text">
                Over the past <span class="text-3xl font-bold">7 days</span> there was an average of <span class="text-4xl font-bold">1200</span> daily scans. <br><br>
                That represents an overall increase of <span class="text-3xl font-bold">10%</span> versus the previous week. <br><br>
                In average each user is scanning about <span class="text-3xl font-bold">1.5 packages</span> and interacting with the app for <span class="text-3xl font-bold">2 minutes and 32 seconds</span>.
            </h1>

            <button @click="greet" class="mt-24 text-3xl text-white w-24 h-24 bg-white rounded-full">
                <i class="ms-Icon ms-Icon--Volume1 text-gradient"></i>
            </button>

        </div>
        
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout.vue'
    import Welcome from '@/Jetstream/Welcome.vue'
    import Editor from '@tinymce/tinymce-vue'
    import JetDialogModal from '@/Jetstream/DialogModal.vue'

    export default {
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
                selectedVoice: 0,
                synth: window.speechSynthesis,
                voiceList: [],
                greetingSpeech: new window.SpeechSynthesisUtterance()
            }
        },
        mounted(){
            
            this.voiceList = this.synth.getVoices();

            this.synth.onvoiceschanged = () => {
                this.voiceList = this.synth.getVoices()
                const voice = this.voiceList.filter(voice => voice.name.includes('UK'));
                this.selectedVoice = voice[0]
                
            }

        },
        methods: {
             greet(){

                let text = this.$refs.text.innerHTML.replace(/<[^>]*>?/gm, '')
            
                this.greetingSpeech.text = text
                this.greetingSpeech.voice = this.selectedVoice
      
                this.synth.speak(this.greetingSpeech)
    }
  
        }
    }
</script>
