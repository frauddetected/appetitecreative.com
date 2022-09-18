<template>
    <div :class="className">
        <div ref="picker"></div>
    </div>
</template>

<script>
import Pickr from '@simonwep/pickr';
import '@simonwep/pickr/dist/themes/nano.min.css'; 

export default{
    props: {
        modelValue: String,
        className: String,
        color: {
          default: "#000000"  
        },
        save: {
            default: false,
            type: Boolean
        }
    },
    mounted(){

       const pickr = Pickr.create({
            el: this.$refs.picker,
            theme: 'nano',
            default: this.color ? this.color : "#000000",
            comparison: this.save,
            components: {
                preview: false,
                opacity: false,
                hue: true,
                interaction: {
                    hex: true,
                    rgba: true,
                    input: true,
                    save: this.save
                }
            }
        })

        pickr.on(this.save ? 'save' : 'change', (color, instance) => {
            this.$emit('update:modelValue', color.toHEXA().toString())
        })

    }
}
</script>