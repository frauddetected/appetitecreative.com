<template>
    <div :class="className" class="p-4 border justify-center text-center flex items-center relative overflow-hidden rounded-sm">
        
        <div>
            <slot />
        </div>

    </div>
</template>

<script>
export default{
    props: ['className','icon']
}
</script>