<template>
    <app-layout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Analytics
            </h2>
        </template>

        <div class="">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
              
                <iframe 
                    plausible-embed="" 
                    :src="src" 
                    scrolling="no" 
                    frameborder="0" 
                    loading="lazy" 
                    style="width: 1px; min-width: 100%; height: 1760px;"
                    id="iframe">
                </iframe>
                    
            </div>
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
        props: [
            'analytics'
        ],
        data(){
            return{
                src: ''
            }
        },
        created(){
            this.src = `https://analytics.appetitecreative.com/share/${this.analytics.details.domain_name}?auth=${this.analytics.details.auth_code}&embed=true&theme=light&background=transparent`
        },
        mounted() {
            setTimeout(function () {
                var iframe = document.getElementById("iframe");
                const iframeWindow = iframe.contentWindow;
                const anchorTags = iframeWindow.document.getElementsByTagName("a");
                for (let i = 0; i < anchorTags.length; i++) {
                    anchorTags[i].addEventListener("click", (event) => {
                        window.scrollTo(0, 0);
                    });
                }
            }, 3000);
        },
        methods: {
            // 
        },
    }
</script>
