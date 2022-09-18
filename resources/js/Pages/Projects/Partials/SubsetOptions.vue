<template>
    <div class="shadow-2xl w-2/12 ml-12 p-4 top-16 sticky">
        
        <p class="font-light text-lg py-4">Subset options</p>
        
        <nav class="flex flex-col">
            <Link 
                :href="route('projects.view')" 
                :class="{ 'border-l-2 border-ms-cyan-120 bg-ms-gray-10' : route().current('projects.view') }" 
                class="hover:bg-ms-gray-20 hover:text-ms-cyan-120 py-2 px-3 flex items-center justify-between">
                Settings
                <i class="ms-Icon ms-Icon--Settings"></i>
            </Link>
            <!--
            <Link 
                :href="route('projects.view', $page.props.user.current_project.ucode)" 
                :class="{ 'border-l-2 border-ms-cyan-120 bg-ms-gray-10' : route().current('projects.dashboard') }" 
                class="hover:bg-ms-gray-20 hover:text-ms-cyan-120 py-2 px-3 flex items-center justify-between">
                Dashboard
                <i class="ms-Icon ms-Icon--ViewDashboard"></i>
            </Link>
            -->
            <Link 
                v-if="$page.props.user.admin"
                :href="route('projects.analytics.view')" 
                :class="{ 'border-l-2 border-ms-cyan-120 bg-ms-gray-10' : route().current('projects.analytics.view') }" 
                class="hover:bg-ms-gray-20 hover:text-ms-cyan-120 py-2 px-3 flex items-center justify-between">
                Analytics
                <i class="ms-Icon ms-Icon--ChartSeries"></i>
            </Link>
            <Link 
                v-if="$page.props.user.role.level <= 3"
                :href="route('projects.i18n.view')" 
                :class="{ 'border-l-2 border-ms-cyan-120 bg-ms-gray-10' : route().current('projects.i18n.view') }" 
                class="hover:bg-ms-gray-20 hover:text-ms-cyan-120 py-2 px-3 flex items-center justify-between">
                I18N
                <i class="ms-Icon ms-Icon--World"></i>
            </Link>
            <Link 
                :href="route('projects.qr.view')" 
                :class="{ 'border-l-2 border-ms-cyan-120 bg-ms-gray-10' : route().current('projects.qr.view') }" 
                class="hover:bg-ms-gray-20 hover:text-ms-cyan-120 py-2 px-3 flex items-center justify-between">
                QR Codes
                <i class="ms-Icon ms-Icon--QRCode"></i>
            </Link>
            <Link 
                v-if="$page.props.user.admin"
                :href="route('projects.quiz.view')" 
                :class="{ 'border-l-2 border-ms-cyan-120 bg-ms-gray-10' : route().current('projects.quiz.view') }" 
                class="hover:bg-ms-gray-20 hover:text-ms-cyan-120 py-2 px-3 flex items-center justify-between">
                Quiz
                <i class="ms-Icon ms-Icon--Table"></i>
            </Link>
            <Link 
                v-if="$page.props.user.admin"
                :href="route('projects.prizes.view')" 
                :class="{ 'border-l-2 border-ms-cyan-120 bg-ms-gray-10' : route().current('projects.prizes.view') }" 
                class="hover:bg-ms-gray-20 hover:text-ms-cyan-120 py-2 px-3 flex items-center justify-between">
                Prizes
                <i class="ms-Icon ms-Icon--Trophy"></i>
            </Link>
            <Link 
                v-if="$page.props.user.role.level <= 2"
                :href="route('projects.sharing.view')" 
                :class="{ 'border-l-2 border-ms-cyan-120 bg-ms-gray-10' : route().current('projects.sharing.view') }" 
                class="hover:bg-ms-gray-20 hover:text-ms-cyan-120 py-2 px-3 flex items-center justify-between">
                Sharing
                <i class="ms-Icon ms-Icon--Share"></i>
            </Link>
            <Link 
                :href="route('projects.sources.view')" 
                :class="{ 'border-l-2 border-ms-cyan-120 bg-ms-gray-10' : route().current('projects.sources.view') }" 
                class="hover:bg-ms-gray-20 hover:text-ms-cyan-120 py-2 px-3 flex items-center justify-between">
                Sources
                <i class="ms-Icon ms-Icon--OpenSource"></i>
            </Link>
            <Link 
                :href="route('projects.logs')" 
                :class="{ 'border-l-2 border-ms-cyan-120 bg-ms-gray-10' : route().current('projects.logs') }" 
                class="hover:bg-ms-gray-20 hover:text-ms-cyan-120 py-2 px-3 flex items-center justify-between">
                Logs
                <i class="ms-Icon ms-Icon--Backlog"></i>
            </Link>

            <!--
            <a class="hover:bg-ms-gray-20 hover:text-ms-cyan-120 py-2 px-3 flex items-center justify-between">
                Coupons
                <i class="ms-Icon ms-Icon--Coupon"></i>
            </a>
            <a class="hover:bg-ms-gray-20 hover:text-ms-cyan-120 py-2 px-3 flex items-center justify-between">
                Quiz
                <i class="ms-Icon ms-Icon--Questionnaire"></i>
            </a>
            -->

        </nav>
    </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3';

export default{
    components: { Link }
}
</script>