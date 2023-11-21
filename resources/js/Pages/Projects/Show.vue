<!--  -->
<style>
/* Styles for the modal */
.modal {
    display: block;
    position: fixed;
    z-index: 50;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
}

.modal-background {
    background-color: rgba(0, 0, 0, 0.4);
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 60%;
    max-width: 500px;
    z-index: 20;
    position: relative;
}

.modal-header {
    position: relative;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
}

.modal-body {
    margin-bottom: 20px;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin-right: 10px;
}

.btn-delete {
    background-color: #ff3333;
    color: white;
}

.btn-cancel {
    background-color: #aaa;
    color: white;
}

</style>
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
                <button v-if="$page.props.user.admin" @click="resetData" class="hover:bg-ms-gray-20 text-ms-gray-160 p-3">
                    Reset All Data
                </button>
            </nav>
        </template>

        <div>
            <div class="mx-auto py-10 px-4 md:px-6 xl:px-20 flex flex-wrap items-start">

                <div class="w-full md:w-2/3 lg:w-10/12 order-2 md:order-none sm:pr-8 xl:pr-12">

                <div class="flex flex-wrap xl:flex-nowrap">
                    <div class="w-full md:w-full xl:w-2/12 sm:pr-8">
                        <h2 class="text-xl">Settings</h2>
                        <p>Update the settings.</p>
                    </div>
                    <div class="w-full xl:w-10/12 overflow-auto">
                        <form action="" class="mt-4 xl:mt-0">
                            
                            <div class="flex flex-wrap">
                                <div class="flex flex-col w-full sm:w-2/4 mb-4 sm:mb-0">
                                    <label for="">Project Name</label>
                                    <input v-model="form.name" type="text" class="input w-full" :readonly="!$page.props.user.admin">
                                </div>
                                <div v-if="$page.props.user.admin" class="flex flex-col sm:pl-4 w-full sm:w-2/4">
                                    <label for="">Controller Namespace</label>
                                    <input v-model="form.controller" type="text" class="input w-full">
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

                <div class="flex flex-wrap" v-if="$page.props.user.admin">
                    <div class="w-full md:w-1/2 xl:w-4/12 mb-4 md:mb-0 pr-8 xl:pr-12">
                        <h2 class="text-xl">API</h2>
                        <p>Create a bearer token for this project</p>
                    </div>
                    <div class="w-full md:w-1/2 xl:w-8/12">
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

                <div class="flex flex-wrap" v-if="$page.props.user.admin">
                    <div class="w-full xl:w-4/12 md:pr-12">
                        <h2 class="text-xl">Add Project Member</h2>
                        <p>Add a new member to the project, allowing them to collaborate with you.</p>
                    </div>
                    <div class="w-full xl:w-8/12">
                        <form @submit.prevent="submitAddMember">

                            <div class="max-w-xl text-sm text-ms-gray-120 mb-8">
                                Please provide the email address of the person you would like to add to this team.
                            </div>

                            <div class="flex">
                                <!-- Member Name -->
                                <div class="flex flex-col w-full mr-2">
                                    <label for="">Name</label>
                                    <input v-model="memberForm.name" type="text" class="input w-full" @input="validateName">
                                    <p class="text-sm text-red-600">{{ validationErrors.name }}</p>
                                </div>
                                <!-- Member Email -->
                                <div class="flex flex-col w-full ml-2">
                                    <label for="">E-mail</label>
                                    <input v-model="memberForm.email" type="text" class="input w-full" @input="validateEmail">
                                    <p class="text-sm text-red-600">{{ validationErrors.email }}</p>
                                </div>
                            </div>

                            <div class="flex flex-col mt-8">
                                <label class="" for="">Role</label>
                                <ul class="flex flex-wrap sm:flex-nowrap">
                                    <label :for="role" class="bg-white m-0 mb-4 sm:m-2 w-full sm:w-auto cb-container p-6 shadow-ms rounded-md hover:bg-ms-gray-20" v-for="rules, role in availableRoles">
                                        <input :id="role" type="radio" v-model="memberForm.role" :value="role" @input="validateRole"> <b>{{ role }}</b>
                                        <p class="text-xs mt-2">{{ rules.description }}</p>
                                        <div class="checkmark"></div>
                                    </label>
                                </ul>
                                <p class="text-sm text-red-600">{{ validationErrors.role }}</p>
                            </div>

                            <div class="flex flex-wrap justify-end w-full sm:mt-8">
                                <button type="submit" class="border border-ms-gray-160 px-4 py-1 hover:bg-ms-gray-40 ml-2 font-bold">Add</button>
                            </div>

                        </form>                    
                    </div>
                </div>

                <hr class="my-8">

                <div class="flex flex-wrap">
                    <div class="w-full xl:w-4/12 xl:pr-12">
                        <h2 class="text-xl">Project Members</h2>
                        <p>All of the people that are part of this project.</p>
                    </div>
                    <div class="w-full xl:w-8/12">
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
                                        <i class="ms-Icon ms-Icon--Delete mr-4" @click="showDeleteModal(user)"></i>
                                        <VDropdown>
                                            <span :class="roleClass(user.pivot.role)" class="text-xs cursor-pointer text-white rounded-sm py-1 px-2">{{ user.pivot.role }}</span>
                                            <template #popper>
                                                <select @change="updateUserRole($event, user)" name="" id="">
                                                    <option value="">Select Role</option>
                                                    <option value="admin">admin</option>
                                                    <option value="editor">editor</option>
                                                    <option value="contributor">contributor</option>
                                                </select>
                                            </template>
                                        </VDropdown>
                                    </nav>
                                    <span v-else :class="roleClass(user.pivot.role)" class="text-xs text-white rounded-sm py-1 px-2">{{ user.pivot.role }}</span>
                                    <!-- Modal for deletion confirmation -->
                                    <div v-if="showModal" class="modal">
                                        <div class="modal-background" @click="hideDeleteModal"></div>
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <span class="close" @click="hideDeleteModal">&times;</span>
                                                <h2>Confirm Deletion</h2>
                                            </div>
                                            <div class="modal-body">
                                                <p>Are you sure you want to delete user <strong>{{ userToDelete.name }}</strong>?</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button class="btn btn-delete" @click="deleteUser(userToDelete)">Delete</button>
                                                <button class="btn btn-cancel" @click="hideDeleteModal">Cancel</button>
                                            </div>
                                        </div>
                                    </div>
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
                showModal: false,
                userToDelete: null,
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
                }),
                validationErrors: {
                    name: "",
                    email: "",
                    role: "",
                },
            }
        },
        computed: {
            hasValidationErrors() {
                return Object.values(this.validationErrors).some((error) => error !== "");
            },
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
            showDeleteModal(user) {
                this.userToDelete = user;
                this.showModal = true;
            },
            hideDeleteModal() {
                this.userToDelete = null;
                this.showModal = false;
            },
            deleteUser(user) {
                const user_id = user.id;

                this.$inertia.post(route('user.delete'), {
                    _method: 'post',
                    user_id: user_id,
                }, {
                    preserveState: true,
                })
                .then(response => {
                    // Handle success
                    this.hideDeleteModal();
                })
                .catch(error => {
                    // Handle error
                    console.error('Error deleting user', error);
                    this.hideDeleteModal(); // Hide the modal even in case of an error
                });
            },
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
                if(ev.target.value !== ''){
                    this.$inertia.post(route('projects.members.role'), {
                        id: user.id,
                        role: ev.target.value
                    },
                    {
                        preserveScroll: true
                    })
                }
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
            resetData()
            {
                this.$inertia.post(route('projects.reset.data'))
            },
            generateToken()
            {
                this.$inertia.post(route('projects.token.update'))
            },
            submitAddMember()
            {
                // Perform form submission or further validation here
                if (this.validateForm()) {
                    // Form is valid, proceed with submission
                    // console.log("Form submitted successfully!");
                    this.memberForm.post(route('projects.members.add'), {
                        preserveScroll: true,
                        onSuccess: () => this.memberForm.reset(),
                        onError: (errors) => {
                            // Handle errors here
                            console.error(errors);
                            // You can also update your component's state or show error messages to the user
                        },
                    })
                } else {
                    // Form is not valid, do not submit
                    console.log("Form has validation errors.");
                }
                // this.memberForm.post(route('projects.members.add'), {
                //     preserveScroll: true,
                //     onSuccess: () => this.memberForm.reset(),
                // })
            },
            validateForm() {
                let valid = true;

                // Validate name
                if (!this.memberForm.name) {
                    this.validationErrors.name = "Please enter Name.";
                    valid = false;
                } else {
                    this.validationErrors.name = "";
                }

                // Validate email
                const emailPattern = /^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/;
                if(!this.memberForm.email){
                    this.validationErrors.email = "Please enter Email.";
                    valid = false;
                }
                else if (!this.memberForm.email.match(emailPattern)) {
                    this.validationErrors.email = "Please enter valid email address.";
                    valid = false;
                } else {
                    this.validationErrors.email = "";
                }

                // Validate role
                if (!this.memberForm.role) {
                    this.validationErrors.role = "Please select role.";
                    valid = false;
                } else {
                    this.validationErrors.role = "";
                }

                return valid;       
            },
        },

        components: {
            AppLayout,
            SubsetOptions,
            Calendar,
            DatePicker
        },
    }
</script>
