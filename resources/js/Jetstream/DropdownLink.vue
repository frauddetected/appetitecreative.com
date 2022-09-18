<template>
    <div>
        <button type="submit" class="block w-full px-4 py-2 text-sm leading-5 text-ms-gray-130 text-left hover:bg-ms-gray-20 focus:outline-none focus:bg-ms-gray-20 transition" v-if="as == 'button'">
            <slot></slot>
        </button>

        <a :href="href" class="block px-4 py-2 text-sm leading-5 text-ms-gray-130 hover:bg-ms-gray-20 focus:outline-none focus:bg-ms-gray-20 transition" v-else-if="as =='a'">
            <slot></slot>
        </a>

        <Link :href="href" class="block px-4 py-2 text-sm leading-5 text-ms-gray-130 hover:bg-ms-gray-20 focus:outline-none focus:bg-ms-gray-20 transition" v-else>
            <slot></slot>
        </Link>
    </div>
</template>

<script>
    import { Link } from '@inertiajs/inertia-vue3';

    export default {
        components: {
            Link,
        },
        props: ['href', 'as']
    }
</script>
