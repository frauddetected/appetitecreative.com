<template>
    <transition name="panel" mode="out-in">
        <div v-if="show" class="bg-white bg-opacity-75 fixed inset-0 z-20" @click.self="$emit('close')">
            <div class="panel flex flex-col absolute bg-white right-0 top-0 h-full overflow-y-auto p-6 shadow-2xl" :class="width">
                <slot />

            </div>
        </div>
   </transition>
</template>

<script>
export default {
    props: {
        show: {
            type: Boolean,
            default: false
        },
        width: {
            type: String,
            default: 'w-4/12'
        }
    }
}
</script>