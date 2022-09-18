<template>
    <button :type="type" class="border border-ms-gray-160 px-4 py-1 hover:bg-ms-gray-40 ml-2 font-bold">
        <slot></slot>
    </button>
</template>

<script>
    export default {
        props: {
            type: {
                type: String,
                default: 'submit',
            },
        }
    }
</script>
