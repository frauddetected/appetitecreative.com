<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-r from-ms-cyan-10  to-ms-magenta-110">
        
        <div>
            <slot name="logo" />
        </div>

        <div class="w-full sm:max-w-md mt-8 px-6 py-4 bg-white shadow-ms overflow-hidden">
            <slot />
        </div>

    </div>
</template>
