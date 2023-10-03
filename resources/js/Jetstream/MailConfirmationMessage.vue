<template>
    <div v-if="hasSuccess">
        <div class="font-medium text-green-600">{{ success }}</div>
    </div>
</template>

<script>
    export default {
        computed: {
            success() {
                return this.$page.props.success
            },

            hasSuccess() {
                return this.success !== undefined;
            },
        }
    }
</script>
