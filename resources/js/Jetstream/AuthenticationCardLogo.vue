<template>
    <Link :href="'/'">
        <div class="flex justify-center items-center font-bold logo">
            <img class="h-20" src="https://static.appetitecreative.com/assets/four/images/logos/small-white.png">
            <p class="absolute w-5 h-5 -ml-2 rounded-full logo-animate-gradient"></p>
            <p class="ml-3 font-bold font-montserrat uppercase text-white text-3xl logo-text">Link</p>
        </div>
    </Link>
</template>

<script>
    import { Link } from '@inertiajs/inertia-vue3';

    export default {
        components: {
            Link,
        },
    }
</script>
