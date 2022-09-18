<template>
    <app-layout title="Project Settings">

        <template #header>
            <h2 class="text-lg text-ms-gray-160 leading-tight">
                Project <span class="text-ms-gray-50">|</span> <span class="text-gradient">Quiz</span>
            </h2>
            <nav class="flex" v-if="$page.props.user.role.level <= 1">
                <button 
                    @click="addNewQuestion=true" class="hover:bg-ms-gray-20 text-ms-gray-160 p-3 flex items-center">
                    <i class="ms-Icon ms-Icon--Add mr-2"></i> Add
                </button>
            </nav>
        </template>

        <div>
            <div class="mx-auto py-10 px-20 flex items-start">

                <div class="w-10/12">
                    <div class="flex">
                        <div class="w-3/12 pr-12">
                            <h2 class="text-xl">Quiz</h2>
                            <p>...</p>
                        </div>
                        <div class="w-9/12">

                            <table class="table">
                                <tr class="header">
                                    <th>Title</th>
                                    <th class="text-center">QR Code</th>
                                    <th class="w-16">Answers</th>
                                    <th class="w-8"></th>
                                </tr>
                                <tr v-for="q in project.quiz">
                                    <td @click="selected=q">{{ q.title }}</td>
                                    <td class="text-center">
                                        {{ qrcode(q.qrcode_id) }}
                                    </td>
                                    <td class="text-center">
                                        {{ q.total_answers_count }}
                                    </td>
                                    <td>
                                        <i @click="delQuestion(q.id)" class="ms-Icon ms-Icon--Delete"></i>
                                    </td>
                                </tr>
                            </table>

                        </div>
                    </div>
                </div>

                <SubsetOptions />
            
            </div>
        </div>

        <!-- x -->
        <jet-dialog-modal maxWidth="4xl" :show="selected" @close="selected=null">
            <template #title>
                Edit Question
            </template>

            <template #content>

                <div class="d-flex align-items-center">
                    <input type="text" class="w-full" @change="changeQuestionTitle($event, selected.id)" :value="selected.title">
                </div>
                <ul class="list-group mt-2">
                    <li class="list-group-item pl-6 py-2" v-for="a in selected.answers">
                        <input type="text" class="w-full" @change="changeAnswerTitle($event, a.id)" :value="a.title">
                        <div class="flex">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" @change="toggleCorrectAnswer($event, a.id)" type="checkbox" :checked="a.is_correct==true" :id="'flexSwitchCheckDefault'+a.id">
                                <label class="form-check-label" :for="'flexSwitchCheckDefault'+a.id"> Is correct?</label>
                            </div>
                            <div class="text-right pt-2">
                                <i @click="delAnswer(a.id)" class="fas fa-trash text-secondary"></i>
                            </div>
                        </div>
                    </li>
                </ul>
            
            </template>
        </jet-dialog-modal>
        <!---->

        <jet-dialog-modal maxWidth="4xl" :show="addNewQuestion" @close="addNewQuestion=0">
            <template #title>
                Add Quiz Question
            </template>

            <template #content>

                            <form action="" method="post" class="flex flex-col w-full" @submit.prevent="save">

                                <div class="flex flex-col">
                                    <label class="font-bold" for="">Question</label>
                                    <input type="text" v-model="form.title" class="form-control" placeholder="">
                                </div>

                                <div class="flex my-3">
                                    <div class="flex items-center w-2/12">
                                        <input id="checkbox_always_correct" class="form-check-input mr-2" type="checkbox" v-model="form.is_always_correct">
                                        <label for="checkbox_always_correct" class="form-check-label">Always correct?</label>
                                    </div>
                                    <div class="flex flex-col w-5/12">
                                        <label class="font-bold" for="">If Correct</label>
                                        <textarea rows="1" class="w-full" v-model="form.if_correct"></textarea>
                                    </div>
                                    <div class="flex flex-col w-5/12 ml-3">
                                        <label class="font-bold" for="">If Incorrect</label>
                                        <textarea rows="1" class="w-full" v-model="form.if_incorrect"></textarea>
                                    </div>
                                </div>

                                <div class="flex my-3">
                                    
                                    <div class="flex flex-col w-full">
                                        
                                        <div class="flex flex-col w-full" v-if="codes.length">
                                            <label for="">QR Code</label>
                                            <VueMultiselect
                                                v-model="form.qrcode"
                                                :options="codes"
                                                :multiple="false"
                                                :close-on-select="true"
                                                placeholder="Select One"
                                                label="title"
                                                track-by="id"
                                            />
                                        </div>
                                        
                                    </div>

                                    <div class="flex flex-col w-full ml-3">
                                        <label for="">Source</label>
                                        <VueMultiselect
                                            v-model="form.source_id"
                                            :options="project.sources"
                                            :multiple="false"
                                            :close-on-select="true"
                                            placeholder="Select One"
                                            label="title"
                                            track-by="id"
                                        />
                                    </div>

                                    <div class="flex flex-col w-full mx-3" v-if="countries">
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

                                <div class="grid grid-cols-2 gap-2 mt-2">
                                    <div class="w-full bg-gray-100 flex flex-col items-end justify-center rounded-sm text-center py-2 px-4 border border-gray-300" v-for="(answer, index) in form.answers">

                                            <input type="text" v-model="answer.title" class="w-full my-3" :placeholder="`Answer ${index+1}`">

                                            <div class="w-6/12 text-right">
                                                <div class="form-check form-switch mt-1 ml-auto">
                                                    <input class="form-check-input" type="checkbox" v-model="answer.is_correct" :id="'flexSwitchCheckDefault-_'+index">
                                                    <label class="form-check-label" :for="'flexSwitchCheckDefault-_'+index"> Is correct?</label>
                                                </div>
                                            </div>

                                        

                                    </div>
                                </div>

                                <nav class="flex items-center mt-4">
                                    <button type="submit" class="border border-ms-gray-160 px-4 py-1 hover:bg-ms-gray-40 font-bold">Save</button>
                                    <span class="hover:bg-ms-gray-20 text-ms-gray-160 p-1 cursor-pointer ml-auto flex items-center" @click="form.answers.push({ title: '', is_correct: false })">+ Add More</span>
                                </nav>

                            </form>

            </template>
        </jet-dialog-modal>
        
    </app-layout>
</template>


<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import SubsetOptions from '@/Pages/Projects/Partials/SubsetOptions.vue'
import JetDialogModal from '@/Jetstream/DialogModal.vue'
import VueMultiselect from 'vue-multiselect'

export default {

    components: {
        AppLayout,
        SubsetOptions,
        JetDialogModal,
        VueMultiselect,
    },
    
    props: [
        'project'
    ],

    data(){
        return{
            addNewQuestion: false,
            form: this.$inertia.form({
                title: null,
                if_correct: null,
                if_incorrect: null,
                is_always_correct: null,
                source: null,
                details: null,
                source_id: null,
                source_value: null,
                qrcode: null,
                country: null,
                language: null,
                answers: [
                    { title: '', is_correct: false }
                ],   
            }),
            selected: null,
            codes: []
        }
    },

    mounted(){
        this.languages = this.project.i18n && this.project.i18n.languages
        this.countries = this.project.i18n && this.project.i18n.countries
        this.codes = this.project.qr
    },

    methods: {
        qrcode(id){
            let f = this.codes.filter(code => code.id == id)
            if(f.length){
                return f[0].title
            }
        },
        save(){
            this.form.post(route('projects.quiz.store'), {
                onSuccess: () => this.form.reset('title','if_correct','if_incorrect','is_always_correct','answers')
            })
        },
        delQuestion(id){
            this.$inertia.post(route('projects.quiz.actions', { delQuestion: id }))
        },
        delAnswer(id){
            this.$inertia.post(route('projects.quiz.actions', { delAnswer: id }))
        },
        changeQuestionTitle(ev, id){
            this.$inertia.post(route('projects.quiz.actions', { cid: id, changeQuestionTitle: ev.target.value }))
        },
        changeAnswerTitle(ev, id){
            this.$inertia.post(route('projects.quiz.actions', { cid: id, changeAnswerTitle: ev.target.value }))
        },
        toggleCorrectAnswer(ev, id){
            this.$inertia.post(route('projects.quiz.actions', { cid: id, toggleCorrectAnswer: true, value: ev.target.checked }))
        }
    }

}
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>