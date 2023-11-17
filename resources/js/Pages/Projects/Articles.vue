<template>
    <app-layout title="Project Settings">

        <template #header>
            <h2 class="text-lg text-ms-gray-160 leading-tight">
                
                Project <span class="text-ms-gray-50">|</span> <span class="text-gradient">Articles</span>

                <button v-if="!module || module.is_active == false" @click="$modules.enable('articles')" class="hover:bg-ms-cyan-20 bg-ms-cyan-10 text-white ml-6 px-3 py-3 text-sm">
                    Enable
                </button>
                <button v-else @click="$modules.disable('articles')" class="hover:bg-ms-magenta-20 bg-ms-magenta-10 text-white ml-6 px-3 py-3 text-sm">
                    Disable
                </button>

            </h2>
            <nav class="flex">
                <button @click="addNewArticle=true; form.reset(); selected=null;" class="hover:bg-ms-gray-20 text-ms-gray-160 p-3 flex items-center">
                    <i class="ms-Icon ms-Icon--Add mr-2"></i> Add
                </button>
            </nav>
        </template>

        <div>
            <div class="mx-auto py-10 px-20 flex items-start">

                <div class="w-10/12">

                <div class="flex">
                    <div class="w-4/12 pr-12">
                        <h2 class="text-xl">Articles</h2>
                        <p>...</p>
                        <p></p>
                    </div>
                    <div class="w-8/12">

                        <table class="table">
                            <tr class="header">
                                <th>Title</th>
                                <th>Tags</th>
                                <th>Created At</th>
                                <th></th>
                            </tr>
                            <tr v-for="article in project.articles">
                                <td>{{ article.title }}</td>
                                <td>{{ article.tags }}</td>
                                <td>{{ article.created_at }}</td>
                                <td>
                                    <i @click="editArticle(article)" class="ms-Icon ms-Icon--Edit hover:text-ms-blue-20 mr-1"></i>
                                    <i @click="delArticle(article.id)" class="ms-Icon ms-Icon--Delete hover:text-ms-orange-10 ml-2"></i>
                                </td>
                            </tr>
                        </table>

                    </div>
                </div>
                </div>

                <SubsetOptions />

            </div>
        </div>
    </app-layout>

            <!-- Delete Account Confirmation Modal -->
            <jet-dialog-modal :show="addNewArticle" @close="addNewArticle=0">
                
                <template #title>
                    {{ selected ? 'Edit' : 'Add' }} Article
                </template>

                <template #content>

                    <div class="flex flex-col">

                        <div class="flex flex-col w-full mr-2">
                            <label for="">Title</label>
                            <input type="text" class="input" v-model="form.title" @keyup="generateSlug">
                            <span class="text-red-500">{{ titleError }}</span>
                        </div>

                        <div class="flex mt-2 gap-x-4">

                        <div class="flex flex-col w-5/12">
                            <label for="">Slug</label>
                            <input type="text" class="input" v-model="form.slug" readonly>
                        </div>

                        <div class="flex flex-col w-7/12">
                            
                            <label for="">Tags</label>
                            <VueMultiselect
                                v-model="form.tags"
                                :multiple="true"
                                :taggable="true"
                                :options="tags"
                                :searchable="true"
                                :close-on-select="false"
                                :show-labels="false"
                                placeholder="Select One or Many"
                                @tag="addTag"
                            />
                            
                        </div>

                        </div>

                        <div class="flex mt-2 flex-col w-full">
                            <label for="">Image</label>
                            <div class="w-full flex items-center">
                                <input type="text" class="input w-full" readonly v-model="form.image">
                                <label class="custom-file-upload ml-3">
                                    <input type="file" ref="file" @change="handleFileUpload($event, 'image')" class="hidden" />
                                    <i class="ms-Icon ms-Icon--CloudUpload text-2xl"></i>
                                </label>
                            </div>
                        </div>

                        <div class="flex mt-2">
                            <div class="flex flex-col w-full mr-2" v-if="countries">
                            <label for="">Country</label>
                            <VueMultiselect
                                v-model="form.country"
                                :options="countries"
                                :multiple="false"
                                :close-on-select="true"
                                placeholder="Select One"
                                label="name"
                                track-by="code"
                            />
                        </div>

                        <div class="flex flex-col w-full" v-if="languages">
                            <label for="">Language</label>
                            <VueMultiselect
                                v-model="form.language"
                                :options="languages"
                                :multiple="false"
                                :close-on-select="true"
                                placeholder="Select One"
                                label="name"
                                track-by="code"
                            />
                        </div>
                        </div>

                        <div class="flex mt-2 flex-col w-full">
                            <label for="">Content</label>
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
                            <span class="text-red-500">{{ contentError }}</span>
                        </div>

                        <div class="flex w-full p-3 mt-3 items-center justify-between bg-ms-gray-20">
                            <label for="">Add Secondary Content</label>
                            <input type="checkbox" v-model="extraContent">
                        </div>

                        <div class="flex flex-col w-full" v-if="extraContent">
                            <label for="">Content Secondary</label>
                            <editor
                                           v-model="form.content_secondary"
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

                    </div>
                    
                </template>

                <template #footer>
                    <button @click="save" class="text-white py-2 px-4 mr-2 font-semibold hover:bg-ms-cyan-120 bg-ms-cyan-110">Save</button>
                    <button @click="close" class="py-2 px-4 font-semibold border border-ms-gray-160 text-ms-gray-160 hover:bg-ms-gray-30">Cancel</button>
                </template>

            </jet-dialog-modal>

</template>

<script>
    import AppLayout from '@/Layouts/AppLayout.vue'
    import SubsetOptions from '@/Pages/Projects/Partials/SubsetOptions.vue'
    import JetDialogModal from '@/Jetstream/DialogModal.vue'
    import Editor from '@tinymce/tinymce-vue'
    import VueMultiselect from 'vue-multiselect'
    
    export default {
        
        props: [
            'project',
            'module'
        ],

        data(){
            return{
                addNewArticle: false,
                extraContent: false,
                tags: [],
                selected: null,
                valid:true,
                titleError: '',
                contentError: '',
                form: this.$inertia.form({
                    id: null,
                    title: '',
                    slug: '',
                    content: '',
                    content_secondary: '',
                    tags: [],
                    image: '',
                    country: null,
                    language: null,
                }),
                languages: [],
                countries: [],
            }
        },

        mounted(){
            this.languages = this.project.i18n && this.project.i18n.languages
            this.countries = this.project.i18n && this.project.i18n.countries
        },

        methods: {
            editArticle(article){
                this.selected = article.id
                this.addNewArticle = true
                this.form.id = article.id
                this.form.title = article.title
                this.form.slug = article.slug
                this.form.content = article.content
                this.form.content_secondary = article.content_secondary
                this.form.tags = article.tags
                this.form.image = article.image
                const language = this.languages.find(lang => lang.code === article.language)
                const country = this.countries.find(country => country.code === article.country)
                this.form.language = language ?? null
                this.form.country = country ?? null

            },
            delArticle(id){
                this.$inertia.post(route('projects.articles.actions', { delQuestion: id }))
            },
            generateSlug(){
                this.form.slug = this.form.title.toLowerCase().replace(/ /g,'-').replace(/[^\w-]+/g,'')
            },
            addTag (newTag) {
                this.tags.push(newTag)
                this.form.tags.push(newTag)
            },
            handleFileUpload(event, model){

                let data = new FormData();
                let file = event.target.files[0];

                data.append('name', 'my-file')
                data.append('file', file)

                let config = { header: {
                    'Content-Type' : 'multipart/form-data'
                }}

                axios.post(route('projects.articles.upload'), data, config).then(r => {
                    this.form[model] = r.data.location
                })

            },
            save(){
                this.valid=true;

                if(this.form.title == ''){
                    this.valid = false;
                    this.titleError = 'Please enter title.'
                }
                if(this.form.content == ''){
                    this.valid = false;
                    this.contentError = 'Please enter contents.'
                }
                
                if(this.valid){
                    this.form.post(route('projects.articles.store'), {
                        preserveState: true,
                        onSuccess: () => {
                            this.addNewArticle = false
                            this.form.reset()
                            this.articles = this.project.articles                    
                        }
                    })
                }
            },
            close(){
                this.addNewArticle = false
            },
        },

        components: {
            AppLayout,
            SubsetOptions,
            'editor': Editor,
            JetDialogModal,
            VueMultiselect
        },
    }
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>