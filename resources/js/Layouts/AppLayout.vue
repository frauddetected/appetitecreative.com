<template>
    <div>
        <Head :title="title" />

        <jet-banner />

        <div class="bg-white min-h-screen">
            <nav class="bg-ms-gray-190">
                <!-- Primary Navigation Menu -->
                <div class="mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-12">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex-shrink-0 flex items-center">
                                <Link :href="route('dashboard')">
                                    <jet-application-mark class="block w-auto" />
                                </Link>
                            </div>
                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <jet-nav-link :href="route('dashboard')" :active="route().current('dashboard')">
                                    Dashboard
                                </jet-nav-link>
                                <jet-nav-link v-if="menuAnalytics" :href="route('analytics')" :active="route().current('analytics')">
                                    Analytics
                                </jet-nav-link>
                                <jet-nav-link v-if="menuPrizes" :href="route('prizes')" :active="route().current('prizes')">
                                    Prizes
                                </jet-nav-link>
                                <jet-nav-link :href="route('notes')" :active="route().current('notes')">
                                    Notes
                                </jet-nav-link>
                                <jet-nav-link v-if="$page.props.user.admin" :href="route('contact.list')" :active="route().current('contact.list')">
                                    Contact Us
                                </jet-nav-link>
                                <jet-nav-link v-if="$page.props.user.admin" :href="route('user.list')" :active="route().current('user.list')">
                                    Users
                                </jet-nav-link>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ml-6">

                            <!-- Settings Dropdown -->
                            <div class="relative">
                                <jet-dropdown align="right" width="48">

                                    <template #trigger>
                                        <button type="button" class="inline-flex items-center p-4 text-sm leading-4 font-medium text-gray-300 hover:bg-ms-gray-150 hover:text-gray-100 focus:outline-none focus:bg-ms-gray-140 active:bg-ms-gray-150 transition">
                                            <i class="ms-Icon ms-Icon--World"></i>
                                        </button>
                                    </template>

                                    <template #content>
                                        <ul class="flex flex-col p-4" v-if="$page.props.user.notifications.length">
                                            <li v-for="notification in $page.props.user.notifications">
                                                <p v-html="parseNotification(notification)"></p>
                                                <p class="text-xs text-ms-gray-100 mt-2">{{ moment(notification.created_at).fromNow() }}</p>
                                            </li>
                                        </ul>
                                        <div class="p-4 text-center text-sm text-ms-gray-100" v-else>
                                            <p>You don't have any notifications</p>
                                        </div>
                                    </template>

                                </jet-dropdown>
                            </div>

                            <div class="relative">
                                <!-- Teams Dropdown -->
                                <jet-dropdown align="right" width="60">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center p-4 text-sm leading-4 font-medium text-gray-300 hover:bg-ms-gray-150 hover:text-gray-100 focus:outline-none focus:bg-ms-gray-140 active:bg-ms-gray-150 transition">
                                                {{ $page.props.user.current_project.name }}

                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <div class="w-60">
                                            <!-- Team Management -->
                                            
                                                <div class="block px-4 py-2 text-xs text-ms-gray-100">
                                                    Manage Projects
                                                </div>

                                                <!-- Team Settings -->
                                                <jet-dropdown-link :href="route('projects.view')">
                                                    Project Settings
                                                </jet-dropdown-link>

                                                <jet-dropdown-link :href="route('projects.create')" v-if="$page.props.user.admin==1">
                                                    Create New Project
                                                </jet-dropdown-link>

                                                <div class="border-t border-gray-100"></div>

                                                <!-- Team Switcher -->
                                                <div class="block px-4 py-2 text-xs text-ms-gray-100">
                                                    Switch Projects
                                                </div>

                                                <div class="h-96 overflow-hidden overflow-y-auto">
                                                <template v-for="project in $page.props.user.projects_list" :key="project.id">
                                                    <form @submit.prevent="switchToProject(project)">
                                                        <jet-dropdown-link as="button">
                                                            <div class="flex items-center">
                                                                <svg v-if="project.id == $page.props.user.current_project.id || project.id == $page.props.user.current_project.parent_id" class="mr-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                                <div class="flex justify-between w-full">
                                                                    {{ project.name }}
                                                                    <div v-if="project.id == $page.props.user.current_project.id || project.id == $page.props.user.current_project.parent_id">
                                                                        <span class="bg-purple-500 px-2 rounded-sm text-white text-sm ml-auto" v-if="$page.props.user.current_project.parent_id">Test</span>
                                                                        <span class="bg-green-500 px-2 rounded-sm text-white text-sm ml-auto" v-else>Live</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </jet-dropdown-link>
                                                    </form>
                                                </template>
                                                </div>
                                            
                                        </div>
                                    </template>
                                </jet-dropdown>
                            </div>

                            <!-- Settings Dropdown -->
                            <div class="relative">
                                <jet-dropdown align="right" width="48">
                                    <template #trigger>
                                        <button v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                            <img class="h-8 w-8 rounded-full object-cover" :src="$page.props.user.profile_photo_url" :alt="$page.props.user.name" />
                                        </button>

                                        <span v-else class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center p-4 text-sm leading-4 font-medium text-gray-300 hover:bg-ms-gray-150 hover:text-gray-100 focus:outline-none focus:bg-ms-gray-140 active:bg-ms-gray-150 transition">
                                                {{ $page.props.user.name }}

                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <!-- Account Management -->
                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            Manage Account
                                        </div>

                                        <jet-dropdown-link :href="route('profile.show')">
                                            Profile
                                        </jet-dropdown-link>
                                        <a class="block px-4 py-2 text-sm leading-5 text-ms-gray-130 hover:bg-ms-gray-20 focus:outline-none focus:bg-ms-gray-20 transition" href="/billing" v-if="$page.props.user.admin != 1 && $page.props.user.role.name != 'contributor' && $page.props.user.overwrite_subscription == 'no'">
                                            Billing
                                        </a>

                                        <div class="border-t border-gray-100"></div>
                                        <!-- Authentication -->
                                        <form @submit.prevent="logout">
                                            <jet-dropdown-link as="button">
                                                Log Out
                                            </jet-dropdown-link>
                                        </form>
                                    </template>
                                </jet-dropdown>
                            </div>

                        </div>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button @click="showingNavigationDropdown = ! showingNavigationDropdown" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <jet-responsive-nav-link :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </jet-responsive-nav-link>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="flex items-center px-4">
                            <div v-if="$page.props.jetstream.managesProfilePhotos" class="flex-shrink-0 mr-3" >
                                <img class="h-10 w-10 rounded-full object-cover" :src="$page.props.user.profile_photo_url" :alt="$page.props.user.name" />
                            </div>

                            <div>
                                <div class="font-medium text-base text-gray-800">{{ $page.props.user.name }}</div>
                                <div class="font-medium text-sm text-gray-500">{{ $page.props.user.email }}</div>
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <jet-responsive-nav-link :href="route('profile.show')" :active="route().current('profile.show')">
                                Profile
                            </jet-responsive-nav-link>
                            <a class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition" href="/billing" v-if="$page.props.user.admin != 1 && $page.props.user.role.name != 'contributor' && $page.props.user.overwrite_subscription == 'no'">
                                Billing
                            </a>
                            <!-- Authentication -->
                            <form method="POST" @submit.prevent="logout">
                                <jet-responsive-nav-link as="button">
                                    Log Out
                                </jet-responsive-nav-link>
                            </form>

                            <!-- Team Management -->
                            <template v-if="$page.props.jetstream.hasTeamFeatures">
                                <div class="border-t border-gray-200"></div>

                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    Manage Team
                                </div>

                                <!-- Team Settings -->
                                <jet-responsive-nav-link :href="route('teams.show', $page.props.user.current_team)" :active="route().current('teams.show')">
                                    Team Settings
                                </jet-responsive-nav-link>

                                <jet-responsive-nav-link :href="route('teams.create')" :active="route().current('teams.create')" v-if="$page.props.jetstream.canCreateTeams">
                                    Create New Team
                                </jet-responsive-nav-link>

                                <div class="border-t border-gray-200"></div>

                                <!-- Team Switcher -->
                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    Switch Teams
                                </div>

                                <template v-for="team in $page.props.user.all_teams" :key="team.id">
                                    <form @submit.prevent="switchToTeam(team)">
                                        <jet-responsive-nav-link as="button">
                                            <div class="flex items-center">
                                                <svg v-if="team.id == $page.props.user.current_team_id" class="mr-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <div>{{ team.name }}</div>
                                            </div>
                                        </jet-responsive-nav-link>
                                    </form>
                                </template>
                            </template>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow-ms top-0 sticky z-20" v-if="$slots.header">
                <div class="mx-auto h-11 px-4 flex justify-between items-center sm:px-6 lg:px-8">
                    <slot name="header"></slot>
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <transition name="fade" mode="out-in">
                    <div v-if="trigger">
                        <slot></slot>
                    </div>
                </transition>
            </main>

        </div>

        <!-- Footer -->
        <footer class="bg-ms-gray-40 text-ms-gray-110 text-center p-10 mt-auto">
            <h4 class="font-bold">Appetite.link</h4>
            <p>support@appetite.link</p>
        </footer>

        <!-- Beta -->
        <div class="absolute top-0 -left-5 hover:bg-ms-cyan-20 duration-300 bg-ms-magenta-20 text-xs font-bold z-10 rotate-90 text-white mt-3 py-1 px-4 uppercase">
            Beta
        </div>

        <SidePanel :show="betaWarning">
            <h1 class="text-4xl font-bold text-gradient">Welcome</h1>
            <h2 class="text-lg font-bold mt-4">You are looking at a <strong>Beta</strong> version of our brand new PaaS.</h2>
            <p class="my-6">Please note, a lot of features are still currently in active development.</p>
            <p class="">We are working as hard as possible to bring the new features to you while keeping everything running as smooth as possible, however, keep in mind the state of product isn't final and there may be some issues.</p>
            <p class="mt-6">If you find any issues, please contact us at <a class="text-ms-magenta-10 hover:text-ms-magenta-110 duration-300" href="mailto:support@appetite.link?subject=Link Support">support@appetite.link</a></p>
            <footer class="flex justify-center mt-auto">
                <button @click="closeBetaWarning" class="border border-ms-gray-160 px-4 py-1 hover:bg-ms-gray-40 mb-5 font-bold">Let's Start!</button>
            </footer>
        </SidePanel>

    </div>
</template>

<script>
    import JetApplicationMark from '@/Jetstream/ApplicationMark.vue'
    import JetBanner from '@/Jetstream/Banner.vue'
    import JetDropdown from '@/Jetstream/Dropdown.vue'
    import JetDropdownLink from '@/Jetstream/DropdownLink.vue'
    import JetNavLink from '@/Jetstream/NavLink.vue'
    import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink.vue'
    import { Head, Link } from '@inertiajs/inertia-vue3';
    import { useToast } from "vue-toastification";
    import moment from 'moment'
    import SidePanel from '@/Components/SidePanel.vue'

    export default {

        props: {
            title: String,
        },

        setup(){
            const toast = useToast();
            return { toast }
        },

        components: {
            Head,
            JetApplicationMark,
            JetBanner,
            JetDropdown,
            JetDropdownLink,
            JetNavLink,
            JetResponsiveNavLink,
            Link,
            SidePanel
        },

        data() {
            return {
                moment,
                showingNavigationDropdown: false,
                trigger: false,
                betaWarning: false
            }
        },

        mounted(){
            this.trigger = true       
            if(!sessionStorage.getItem('beta-warning-read')){
                this.betaWarning = 1
            }
        },  

        computed: {
            menuAnalytics(){
                if (this.$page.props.modules.some(e => e.name === 'analytics')) return true;
            },
            menuPrizes(){
                if (this.$page.props.modules.some(e => e.name === 'prizes')) return true;
            }
        },

        methods: {

            closeBetaWarning(){
                sessionStorage.setItem('beta-warning-read', true)
                this.betaWarning = 0
            },

            parseNotification(notification) {
                switch (notification.type) {
                    case 'App\\Notifications\\AddedToProject':
                        if(notification.data){
                            return `You've been added to the project <strong>${notification.data.project.name}</strong>`;
                        }
                        else{
                            return `You've been added to the project`; 
                        }
                        // return `You've been added to the project`;
                        break;
                
                    default:
                        break;
                }

            },
            
            switchToProject(project) {

                this.$inertia.put(route('projects.current.update'), {
                    'id': project.id
                }, {
                    preserveState: false
                })
            },

            logout() {
                this.$inertia.post(route('logout'));
            },
        },

        watch: {
            "$page.props": function(v){
                if(v.flash.message){
                    this.toast(v.flash.message);
                }
                /*
                if(v.errors[0]){
                    this.toast.error(v.errors[0]);
                }
                */
            },
            "$page.props.errorBags.default": function(v){
                if(v){
                    v[0].forEach(msg => {
                        this.toast.error(msg);
                    });
                }
            }
        }
    }
</script>

<style>
.Vue-Toastification__toast--default{
  background: linear-gradient(-45deg, #038387, #c239b3, #8378de);
  background-size: 150% 150%;
  animation-duration: 2s;
}

.Vue-Toastification__toast--error{
    background: linear-gradient(-45deg, #c239b3, #ca5010)
}

.Vue-Toastification__toast-body{
    font-weight: bolder;
    text-align: center;
    font-size: 1.1rem;
}

.Vue-Toastification__toast--default .Vue-Toastification__toast-body{
  color: white;
}
</style>