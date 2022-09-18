<template>
    <app-layout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Notes
            </h2>
            <nav class="flex">
                <button @click="addNewNote=true" class="hover:bg-ms-gray-20 text-ms-gray-160 p-3 flex items-center">
                    <i class="ms-Icon ms-Icon--Add mr-2"></i> Add Note
                </button>
            </nav>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <p class="p-4 text-center bg-ms-gray-20 text-ms-gray-100 mb-5">Here we can share any particular data analysis, suggestions or notes of anything we've recently noticed. Anything worth of note that goes beyond automated data analytics can be shared here.</p>

                <div class="grid grid-cols-3 gap-5">
                    
                    <div v-for="note in notes" class="w-full shadow-2xl p-6">
                        
                        <h3 class="font-bold text-xl">{{ note.title }}</h3>
                        <article class="py-4 is-article" v-html="note.content">
                        </article>
                        <h4 class="text-xs text-right pt-4 text-ms-gray-100">{{note.user.name}} @ {{ moment(note.created_at).format('D-M-Y H:s') }}</h4>

                    </div>

                </div>                    
                    
            </div>
        </div>
    </app-layout>

            <!-- Delete Account Confirmation Modal -->
            <jet-dialog-modal maxWidth="4xl" :show="addNewNote" @close="addNewNote=0">
                <template #title>
                    Add Note
                </template>

                <template #content>

                    <div class="flex flex-col">

                        <div class="flex flex-col w-full mb-4">
                            <label for="">Title</label>
                            <input v-model="form.title" type="text" class="input">
                        </div>

                                           <editor
                                           v-model="form.content"
       api-key="1bq1tsdkaxesa8a48bk56ji6yt6bgh3kcgfizd6wj0uw5swt"
       :init="{
         height: 300,
         menubar: false,
         plugins: [
           'advlist autolink lists link image charmap print preview anchor',
           'searchreplace visualblocks code fullscreen',
           'insertdatetime media table paste code help wordcount'
         ],
         toolbar:
           'undo redo | formatselect | bold italic backcolor | \
           alignleft aligncenter alignright alignjustify | \
           bullist numlist outdent indent | removeformat | help'
       }"
     />
                    </div>
                    
                </template>

                <template #footer>
                    <button @click="save" class="text-white py-2 px-4 mr-2 font-semibold hover:bg-ms-cyan-120 bg-ms-cyan-110">Save</button>
                    <button class="py-2 px-4 font-semibold border border-ms-gray-160 text-ms-gray-160 hover:bg-ms-gray-30">Cancel</button>
                </template>
            </jet-dialog-modal>
    
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout.vue'
    import Welcome from '@/Jetstream/Welcome.vue'
    import Editor from '@tinymce/tinymce-vue'
    import JetDialogModal from '@/Jetstream/DialogModal.vue'
    import moment from 'moment'

    export default {
        props: ['notes'],
        components: {
            AppLayout,
            Welcome,
            'editor': Editor,
            JetDialogModal
        },
        data(){
            return{
                addNewNote: false,
                moment: moment,
                form: {
                    title: '',
                    content: ''
                }
            }
        },
        methods: {
            save(){
                this.$inertia.post(route('notes'), this.form, {
                    preserveState: true,
                    onSuccess(){
                        this.addNewNote = false
                    }
                })
            }
        }
    }
</script>

<style>
.is-article p{
    padding: 6px 0;
}
</style>