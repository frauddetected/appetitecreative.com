<template>
    <app-layout title="Project Settings">

        <template #header>
            <h2 class="text-lg text-ms-gray-160 leading-tight flex">
                Project <span class="text-ms-gray-50 mx-1">|</span> <span class="text-gradient">Settings</span>
                <small class="text-sm ml-4 text-ms-gray-80 flex">
                    {{ project.ucode }}
                    <div v-if="$page.props.user.admin">
                        <button @click="toggleLiveStatus" v-if="project.is_test==false" class="bg-green-500 px-2 text-white rounded-sm ml-4">Live</button>
                        <button @click="toggleLiveStatus" v-else class="bg-purple-500 px-2 text-white rounded-sm ml-4">Test</button>
                        <span class="bg-light-700 text-blue-gray-600 px-2 ml-4">Id: {{ project.id }}</span>
                    </div>
                </small>
            </h2>
            <nav class="flex" v-if="$page.props.user.admin">
                <button @click="save" class="hover:bg-ms-gray-20 text-ms-gray-160 p-3">
                    Save
                </button>
                <button v-if="$page.props.user.admin" @click="deleteData" class="hover:bg-ms-gray-20 text-ms-gray-160 p-3">
                    Delete All Data
                </button>
            </nav>
        </template>

        <div>
            <div class="mx-auto py-10 px-20 flex items-start">

                <div class="w-10/12">

                <div class="flex">
                    <div class="w-4/12 pr-12">
                        <h2 class="text-xl">Settings</h2>
                        <p>Update the settings.</p>
                    </div>
                    <div class="w-8/12">
                        <form action="">
                            
                            <div class="flex">
                                <div class="flex flex-col w-8/12">
                                    <label for="">Project Name</label>
                                    <input v-model="form.name" type="text" class="input" :readonly="!$page.props.user.admin">
                                </div>

                                <div v-if="$page.props.user.admin" class="flex flex-col ml-4 w-4/12">
                                    <label for="">Controller Namespace</label>
                                    <input v-model="form.controller" type="text" class="input">
                                </div>
                            </div>

                            <div v-if="$page.props.user.admin" class="flex mt-5">
                                <div class="flex flex-col w-full">
                                    <label for="">App Domain</label>
                                    <input v-model="form.domain" type="text" class="input">
                                </div>
                            </div>

                            <div v-if="$page.props.user.admin" class="flex mt-5">
                                <div class="flex flex-col w-full">
                                    <label for="">Ends At</label>
                                    <DatePicker class="w-full" v-model="form.ends_at">
                                        <template v-slot="{ inputValue, inputEvents }">
                                        <div class="w-full">
                                            <input
                                                :value="inputValue"
                                                v-on="inputEvents"
                                                type="text" 
                                                class="input w-full"
                                            />
                                        </div>
                                        </template>
                                    </DatePicker>
                                </div>
                            </div>

                        </form>                    
                    </div>
                </div>

                <hr class="my-8" v-if="$page.props.user.admin">

                <div class="flex" v-if="$page.props.user.admin">
                    <div class="w-4/12 pr-12">
                        <h2 class="text-xl">API</h2>
                        <p>Create a bearer token for this project</p>
                    </div>
                    <div class="w-8/12">
                        <form action="">
                            
                            <div class="flex">
                                <div class="flex flex-col w-full relative">
                                    <label for="">API Token</label>
                                    <input v-model="api_token" readonly type="text" class="input bg-ms-gray-20">
                                    <i @click="generateToken" class="ms-Icon ms-Icon--AzureKeyVault absolute right-3 bottom-2 text-lg cursor-pointer hover:text-ms-cyan-110"></i>
                                </div>
                            </div>

                        </form>                    
                    </div>
                </div>         

                <hr class="my-8" v-if="$page.props.user.admin">

                <div class="flex" v-if="$page.props.user.admin">
                    <div class="w-4/12 pr-12">
                        <h2 class="text-xl">Add Project Member</h2>
                        <p>Add a new member to the project, allowing them to collaborate with you.</p>
                    </div>
                    <div class="w-8/12">
                        <form @submit.prevent="submitAddMember">

                            <div class="max-w-xl text-sm text-ms-gray-120 mb-8">
                                Please provide the email address of the person you would like to add to this team.
                            </div>

                            <div class="flex">
                                <!-- Member Name -->
                                <div class="flex flex-col w-full mr-2">
                                    <label for="">Name</label>
                                    <input v-model="memberForm.name" type="text" class="input">
                                </div>
                                <!-- Member Email -->
                                <div class="flex flex-col w-full ml-2">
                                    <label for="">E-mail</label>
                                    <input v-model="memberForm.email" type="text" class="input">
                                </div>
                            </div>

                            <div class="flex flex-col mt-8">
                                <label class="" for="">Role</label>
                                <ul class="flex">
                                    <label :for="role" class="bg-white cb-container p-6 shadow-ms rounded-md hover:bg-ms-gray-20 m-2" v-for="rules, role in availableRoles">
                                        <input :id="role" type="radio" v-model="memberForm.role" :value="role"> <b>{{ role }}</b>
                                        <p class="text-xs mt-2">{{ rules.description }}</p>
                                        <div class="checkmark"></div>
                                    </label>
                                </ul>
                            </div>

                            <div class="flex justify-end w-full mt-8">
                                <button type="submit" class="border border-ms-gray-160 px-4 py-1 hover:bg-ms-gray-40 ml-2 font-bold">Add</button>
                            </div>

                        </form>                    
                    </div>
                </div>

                <hr class="my-8">

                <div class="flex">
                    <div class="w-4/12 pr-12">
                        <h2 class="text-xl">Project Members</h2>
                        <p>All of the people that are part of this project.</p>
                    </div>
                    <div class="w-8/12">
                        <form action="">


                            <div class="flex items-center mt-4 py-4 border-b">
                                <img class="w-12 h-12 rounded-full object-cover" :src="project.owner.profile_photo_url" :alt="project.owner.name">
                                <div class="ml-4 leading-tight">
                                    <div>{{ project.owner.name }}</div>
                                    <div class="text-ms-gray-100 text-sm">{{ project.owner.email }}</div>
                                </div>
                                <div class="ml-auto">
                                    <span class="text-xs text-white rounded-sm py-1 px-2 bg-ms-cyan-120">owner</span>
                                </div>
                            </div>

                            <div v-for="user in project.users" class="flex items-center mt-4 py-4 border-b">
                                <img class="w-12 h-12 rounded-full object-cover" :src="user.profile_photo_url" :alt="user.name">
                                <div class="ml-4 leading-tight">
                                    <div>{{ user.name }}</div>
                                    <div class="text-ms-gray-100 text-sm">{{ user.email }}</div>
                                </div>
                                <div class="ml-auto flex items-center">
                                    <nav class="flex" v-if="$page.props.user.admin">
                                        <i class="ms-Icon ms-Icon--Delete mr-4"></i>
                                        <VDropdown>
                                            <span :class="roleClass(user.pivot.role)" class="text-xs cursor-pointer text-white rounded-sm py-1 px-2">{{ user.pivot.role }}</span>
                                            <template #popper>
                                                <select @change="updateUserRole($event, user)" name="" id="">
                                                    <option value="admin">admin</option>
                                                    <option value="editor">editor</option>
                                                    <option value="contributor">contributor</option>
                                                </select>
                                            </template>
                                        </VDropdown>
                                    </nav>
                                    <span v-else :class="roleClass(user.pivot.role)" class="text-xs text-white rounded-sm py-1 px-2">{{ user.pivot.role }}</span>
                                </div>
                            </div>

                        </form>                    
                    </div>
                </div>

                </div>

                <SubsetOptions />

            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout.vue'
    import SubsetOptions from '@/Pages/Projects/Partials/SubsetOptions.vue'
    import { Calendar, DatePicker } from 'v-calendar'

    export default {
        
        props: [
            'availableRoles',
            'project',
            'token'
        ],

        data(){
            return{
                api_token: '',
                user_role: [],
                form: this.$inertia.form({
                    name: '',
                    controller: '',
                    domain: '',
                    ends_at: ''
                }),
                memberForm: this.$inertia.form({
                    name: '',
                    email: '',
                    role: ''
                })
            }
        },

        watch: {
            token: function(v){
                this.api_token = v
            }
        },

        created() {
            this.form.name = this.project.name
            this.form.controller = this.project.controller
            this.form.domain = this.project.domain
            this.form.ends_at = this.project.ends_at
        },

        methods: 
        {
            roleClass(role)
            {
                let className;
                switch (role) {
                    case 'admin': className = 'bg-ms-cyan-110';
                    break;
                    case 'editor': className = 'bg-ms-cyan-30';
                    break;
                    case 'contributor': className = 'bg-ms-cyan-20';
                    break;
                }
                return className;
            },
            updateUserRole(ev, user)
            {
                this.$inertia.post(route('projects.members.role'), {
                    id: user.id,
                    role: ev.target.value
                },
                {
                    preserveScroll: true
                })
            },
            save()
            {    
                this.form.put(route('projects.details.update'), {
                    preserveScroll: true
                })              
            },
            toggleLiveStatus()
            {
                this.$inertia.post(route('projects.live.toggle'))
            },
            deleteData()
            {

            },
            generateToken()
            {
                this.$inertia.post(route('projects.token.update'))
            },
            submitAddMember()
            {
                this.memberForm.post(route('projects.members.add'), {
                    preserveScroll: true,
                    onSuccess: () => this.memberForm.reset(),
                })
            }
        },

        components: {
            AppLayout,
            SubsetOptions,
            Calendar,
            DatePicker
        },
    }
</script>
